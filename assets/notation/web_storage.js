/* Account-scoped browser counterpart of Flutter NotationApi. No credentials are persisted. */
(function(root){
'use strict';
const clone=value=>JSON.parse(JSON.stringify(value));
class NotationWebStorage {
 constructor(userId,remote,storage=root.localStorage){this.userId=Number(userId)||0;this.remote=remote;this.storage=storage;this.key='notation_web_v1_'+this.userId;this.favorite='__favorite__';}
 read(){
  const primary=this.storage.getItem(this.key),backup=this.storage.getItem(this.key+'_backup');
  for(const raw of [primary,backup]){if(raw===null)continue;try{const value=JSON.parse(raw);if(value&&typeof value==='object'&&!Array.isArray(value)&&value.entries&&value.lists)return value;}catch(_){} }
  if(primary!==null||backup!==null)throw Error('Saved music sheets are damaged.');
  return {entries:{},lists:{[this.favorite]:[]}};
 }
 write(value){
  const previous=this.storage.getItem(this.key);
  if(previous){try{const parsed=JSON.parse(previous);if(parsed.entries&&parsed.lists)this.storage.setItem(this.key+'_backup',previous);}catch(error){if(error.name==='QuotaExceededError')throw Error('Could not save music sheets.');}}
  try{this.storage.setItem(this.key,JSON.stringify(value));}catch(_){throw Error('Could not save music sheets.');}
 }
 summary(sheet){return {...clone(sheet),title:sheet.metadata.title,editable:sheet.owner_id===this.userId,local:true};}
 async remember(sheet){const store=this.read();store.entries[sheet.id]={...clone(sheet),local:true,uploaded:true,remote_id:sheet.id,remote_version:sheet.version};this.write(store);return this.summary(store.entries[sheet.id]);}
 request(action,data={}){return root.navigator?.locks?root.navigator.locks.request(this.key,()=>this.perform(action,data)):this.perform(action,data);}
 async perform(action,data={}){
  const store=this.read(),id=Number(data.sheetId),entry=store.entries[id];
  if(action==='instruments'){
   try{const items=(await this.remote(action,data)).map(item=>({...item,...((item.en||'').toLowerCase()==='piano'||item.fa==='پیانو'?{staves:[{clef:'treble'},{clef:'bass'}]}:{})}));this.storage.setItem(this.key+'_instruments',JSON.stringify(items));return items;}
   catch(error){const cached=this.storage.getItem(this.key+'_instruments');if(cached)return JSON.parse(cached);return [{id:'Piano',fa:'پیانو',en:'Piano',staves:[{clef:'treble'},{clef:'bass'}]},{id:'Guitar',fa:'گیتار',en:'Guitar'},{id:'Violin',fa:'ویولن',en:'Violin'},{id:'Voice',fa:'آواز',en:'Voice'}];}
  }
  if(action==='list'){
   const query=String(data.query||'').toLowerCase(),matches=s=>[s.metadata?.title,s.author,s.metadata?.composer,s.metadata?.arranger,s.metadata?.lyricist].some(v=>String(v||'').toLowerCase().includes(query));
   const local=Object.values(store.entries).map(s=>this.summary(s));
   if(data.mode==='lists')return {items:Object.entries(store.lists).map(([name,ids])=>({id:name,title:name===this.favorite?'Favorite':name,kind:'list',favorite:name===this.favorite,count:ids.length,items:ids.map(id=>store.entries[id]).filter(Boolean).map(s=>this.summary(s)).filter(s=>name.toLowerCase().includes(query)||matches(s))})).filter(s=>!query||s.title.toLowerCase().includes(query)||s.items.length),has_more:false};
   let remote={items:[],has_more:false};
   if(['all','mine','saved'].includes(data.mode)){try{remote=await this.remote(action,data);}catch(error){if(!local.length)throw error;}}
   const seen=new Set(local.map(s=>s.remote_id||s.id));
   const filtered=local.filter(s=>(data.mode!=='mine'||s.editable)&&(data.mode!=='saved'||s.saved)&&matches(s));
   return {items:[...(Number(data.page||1)===1?filtered:[]),...remote.items.filter(s=>!seen.has(s.id))],has_more:remote.has_more};
  }
  if(action==='get')return entry?this.summary(entry):this.remember(await this.remote(action,data));
  if(action==='save'){
   if(entry&&entry.owner_id!==this.userId)throw Error('Only the owner can edit this sheet.');
   if(entry&&data.payload?.version!==entry.version)throw Error('This sheet changed elsewhere. Reload before saving.');
   const payload=data.payload;if(new TextEncoder().encode(JSON.stringify(payload)).length>300000)throw Error('Sheet is too large.');
   root.NotationImporter.parse(JSON.stringify({format:'sornaz-notation',version:1,metadata:payload.metadata,score:payload.score}),'score.json');
   // A remote sheet must be read first; never adopt a supplied remote ID as ownership.
   if(id>0&&!entry)throw Error('Sheet not found.');
   let key=id||-Date.now();while(!id&&store.entries[key])key--;
   const sheet={...entry,...clone(payload),id:key,owner_id:this.userId,version:(entry?.version||0)+1,editable:true,local:true,uploaded:false,updated_at:new Date().toISOString()};
   store.entries[key]=sheet;this.write(store);return this.summary(sheet);
  }
  if(action==='upload'){
   if(!this.userId)throw Error('Sign in to continue.');
   if(!entry||entry.owner_id!==this.userId)throw Error('Only the owner can edit this sheet.');
   const remote=await this.remote('save',{sheetId:entry.remote_id||0,payload:{metadata:entry.metadata,score:entry.score,visibility:entry.visibility||'private',version:entry.remote_version||0}});
   store.entries[id]={...entry,uploaded:true,remote_id:remote.id,remote_version:remote.version};this.write(store);return this.summary(store.entries[id]);
  }
  if(action==='delete'){
   if(entry&&entry.owner_id!==this.userId)throw Error('Only the owner can edit this sheet.');
   if(!entry||entry.remote_id)await this.remote('delete',{sheetId:entry?.remote_id||id,payload:{version:entry?.remote_version||data.payload?.version}});
   delete store.entries[id];for(const ids of Object.values(store.lists)){const index=ids.indexOf(id);if(index>=0)ids.splice(index,1);}this.write(store);return true;
  }
  if(action==='bookmark'){const sheet=entry||await this.remote('get',data);if(!entry)store.entries[id]={...sheet,remote_id:id,remote_version:sheet.version,uploaded:true};store.entries[id].saved=data.payload?.active===true;this.write(store);return this.summary(store.entries[id]);}
  if(action==='create-list'){const name=String(data.name||'').trim();if(!name||name.length>80||['__proto__','constructor','prototype'].includes(name))throw Error('Invalid list name.');if(!Object.hasOwn(store.lists,name))store.lists[name]=[];this.write(store);return true;}
  if(action==='add-to-list'){
   if(!Object.hasOwn(store.lists,data.name)||!Array.isArray(data.sheetIds))throw Error('Invalid list.');
   for(const selected of data.sheetIds){if(!Number.isSafeInteger(selected))throw Error('Invalid sheet.');if(!store.entries[selected]){const sheet=await this.remote('get',{sheetId:selected});store.entries[selected]={...sheet,local:true,uploaded:true,remote_id:sheet.id,remote_version:sheet.version};}if(!store.lists[data.name].includes(selected))store.lists[data.name].push(selected);}
   this.write(store);return true;
  }
  throw Error('Invalid operation.');
 }
}
root.NotationWebStorage=NotationWebStorage;
if(typeof module==='object')module.exports=NotationWebStorage;
})(globalThis);
