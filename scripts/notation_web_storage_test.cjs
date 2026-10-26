const assert=require('node:assert/strict');
global.NotationModel=require('../assets/notation/model.js');
require('../assets/notation/importer.js');
const Storage=require('../assets/notation/web_storage.js');
const OMR=require('../assets/notation/omr.js');
const memory=new Map(),storage={getItem:key=>memory.get(key)??null,setItem:(key,value)=>memory.set(key,value)};
const fixture=()=>({metadata:{...NotationModel.fresh().metadata,title:'Local melody',staves:[{clef:'treble'}]},score:{measures:[{notes:[{pitch:'C4',duration:'w',dots:0,staff:1,voice:1,at:0}]}]},visibility:'private',version:0});
(async()=>{
 let offline=true,calls=[],remoteVersion=4;
 const remote=async(action,data)=>{calls.push({action,data});if(offline)throw Error('offline');if(action==='list')return {items:[{...fixture(),id:9,owner_id:7,version:4,editable:true,title:'Server melody'}],has_more:true};if(action==='get')return {...fixture(),id:data.sheetId,owner_id:data.sheetId===10?8:7,version:4,editable:data.sheetId!==10};if(action==='save'){if(data.sheetId&&data.payload.version!==remoteVersion)throw Error('conflict');return {...data.payload,id:data.sheetId||9,version:++remoteVersion};}return true;};
 const api=new Storage(7,remote,storage);
 const local=await api.request('save',{payload:fixture()});assert.ok(local.id<0);assert.equal(local.uploaded,false);assert.equal(calls.length,0);
 assert.equal((await api.request('list',{mode:'mine',page:1})).items.length,1);
 assert.equal((await api.request('instruments')).find(i=>i.id==='Piano').staves.length,2);
 await api.request('create-list',{name:'Practice'});await api.request('add-to-list',{name:'Practice',sheetIds:[local.id]});await api.request('add-to-list',{name:'__favorite__',sheetIds:[local.id]});
 const lists=await api.request('list',{mode:'lists'});assert.equal(lists.items.find(i=>i.id==='Practice').items[0].metadata.title,'Local melody');
 assert.equal((await new Storage(8,remote,storage).request('list',{mode:'lists'})).items[0].count,0);
 await assert.rejects(api.request('save',{sheetId:local.id,payload:{...fixture(),version:0}}),/changed elsewhere/);
 offline=false;const uploaded=await api.request('upload',{sheetId:local.id});assert.equal(uploaded.remote_id,9);assert.equal(uploaded.remote_version,5);
 const catalog=new Storage(7,async()=>[{id:7,en:'Piano',fa:'پیانو'}],storage);await catalog.request('instruments');
 const offlineCatalog=new Storage(7,async()=>{throw Error('offline');},storage);assert.equal((await offlineCatalog.request('instruments'))[0].staves.length,2,'numeric piano IDs retain grand staff offline');
 const payload={...fixture(),version:uploaded.version};payload.metadata.title='Changed offline';await api.request('save',{sheetId:local.id,payload});
 await api.request('upload',{sheetId:local.id});assert.equal(calls.filter(c=>c.action==='save').at(-1).data.payload.version,5);
 remoteVersion++;await assert.rejects(api.request('upload',{sheetId:local.id}),/conflict/);
 await api.request('get',{sheetId:10});await assert.rejects(api.request('save',{sheetId:10,payload:fixture()}),/owner/);await assert.rejects(api.request('upload',{sheetId:10}),/owner/);
 const bad=fixture();bad.score.measures[0].notes[0].pitch='G8';await assert.rejects(api.request('save',{payload:bad}));
 await assert.rejects(api.request('create-list',{name:'__proto__'}));
 // A damaged primary recovers the last valid backup; quota failure never pretends success.
 memory.set(api.key,'{');assert.ok(api.read().entries);const failing=new Storage(7,remote,{getItem:key=>memory.get(key)??null,setItem:()=>{throw Object.assign(Error('full'),{name:'QuotaExceededError'});}});await assert.rejects(failing.request('save',{payload:fixture()}),/Could not save/);
 const guest=new Storage(0,remote,storage);const guestSheet=await guest.request('save',{payload:fixture()});await assert.rejects(guest.request('upload',{sheetId:guestSheet.id}),/Sign in/);
 const notes=Array.from({length:4},()=>({pitch:'C4',duration:'q'}));const score=OMR.toImport(notes,'treble',4,4,'Scanned');assert.equal(score.score.measures.length,1);assert.equal(NotationImporter.parse(JSON.stringify(score),'omr.json').summary.notes,4);
 assert.throws(()=>OMR.toImport([{pitch:'',duration:'q'}],'treble',4,4,''),/Confirm/);assert.throws(()=>OMR.toImport(notes.slice(1),'treble',4,4,''),/fit/);
 // Raster fixture contains a genuine five-line staff, filled head and upward stem.
 const w=400,h=120,data=new Uint8ClampedArray(w*h*4).fill(255);const dot=(x,y)=>{if(x>=0&&x<w&&y>=0&&y<h){const i=(y*w+x)*4;data[i]=data[i+1]=data[i+2]=0;}};
 for(const y of [30,40,50,60,70])for(let x=10;x<390;x++)dot(x,y);
 for(let x=145;x<=155;x++)for(let y=62;y<=68;y++)if(((x-150)/6)**2+((y-65)/4)**2<=1)dot(x,y);
 for(let y=35;y<66;y++){dot(155,y);dot(156,y);}
 const probe=OMR.analyze({width:w,height:h,data});assert.ok(probe.notes.some(n=>Math.abs(n.x-150)<7));assert.equal(probe.notes[0].duration,'');
 assert.throws(()=>OMR.analyze({width:100,height:100,data:new Uint8ClampedArray(40000).fill(255)}),/one staff/);
 console.log('Notation web: account isolation, offline persistence, lists, ownership, upload revisions, corrupt backup, quota failure, pitch validation and raster OMR passed.');
})().catch(error=>{console.error(error);process.exitCode=1;});
