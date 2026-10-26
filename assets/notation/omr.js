/* Browser port of Flutter's experimental printed-score probe and melody review.
 * Images remain in memory. Rhythm and uncertain pitches always require review. */
(function(root){
'use strict';
const workerUrl=typeof document==='object'?document.currentScript?.src:null;
const treble=[64,65,67,69,71,72,74,76,77,79,81,83],bass=[43,45,47,48,50,52,53,55,57,59,60,62];
function midiPitch(midi){if(!Number.isInteger(midi)||midi<21||midi>108)return '';return ['C','C#','D','Eb','E','F','F#','G','Ab','A','Bb','B'][midi%12]+(Math.floor(midi/12)-1);}
function pitchForStep(clef,step){return midiPitch((clef==='bass'?bass:treble)[step]);}
function deskew(image){
 if(typeof OffscreenCanvas!=='function')return image;
 const score=img=>{const rows=[];for(let y=0;y<img.height;y++){let count=0;for(let x=0;x<img.width;x+=2){const p=(y*img.width+x)*4;if((img.data[p]*.299+img.data[p+1]*.587+img.data[p+2]*.114)<245)count++;}rows.push(count);}return rows.sort((a,b)=>b-a).slice(0,5).reduce((a,b)=>a+b,0);};
 let best=image,bestScore=score(image);if(bestScore>=image.width*.7)return image;
 const source=new OffscreenCanvas(image.width,image.height);source.getContext('2d').putImageData(image,0,0);const canvas=new OffscreenCanvas(image.width,image.height),ctx=canvas.getContext('2d',{willReadFrequently:true});
 for(const degrees of [-5,-4,-3,-2,-1,1,2,3,4,5]){ctx.resetTransform();ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.translate(canvas.width/2,canvas.height/2);ctx.rotate(degrees*Math.PI/180);ctx.drawImage(source,-canvas.width/2,-canvas.height/2);const rotated=ctx.getImageData(0,0,canvas.width,canvas.height),value=score(rotated);if(value>bestScore){bestScore=value;best=rotated;}}
 return best;
}
function analyze(image){
 const {width:w,height:h,data}=image;if(w*h>24000000)throw Error('Image dimensions are too large.');
 const ink=new Uint8Array(w*h),dark=new Uint8Array(w*h),rows=[];
 for(let y=0;y<h;y++){let count=0;for(let x=0;x<w;x++){const p=(y*w+x)*4,alpha=data[p+3]/255,lum=(data[p]*.299+data[p+1]*.587+data[p+2]*.114)*alpha+255*(1-alpha);ink[y*w+x]=lum<245?1:0;dark[y*w+x]=lum<160?1:0;count+=ink[y*w+x];}if(count>w*.1)rows.push(y);}
 const lines=[];for(const y of rows)if(!lines.length||y-lines.at(-1)>2)lines.push(y);
 const staffs=[];for(let i=0;i+4<lines.length;i++){const s=lines.slice(i,i+5),gap=(s[4]-s[0])/4;if(gap>=5&&gap<=30&&s.slice(1).every((y,j)=>Math.abs(y-s[j]-gap)<=2)){staffs.push(s);i+=4;}}
 if(staffs.length!==1)throw Error('Exactly one staff is required.');
 const s=staffs[0],spacing=(s[4]-s[0])/4,mask=new Set(s.flatMap(y=>[y-1,y,y+1]));
 const active=[];for(let x=0;x<w;x++)if(s.reduce((n,y)=>n+ink[y*w+x],0)>=4)active.push(x);
 if(!active.length)throw Error('No noteheads found.');
 const left=active[0],right=active.at(-1),rx=Math.max(4,Math.round(spacing*.58)),ry=Math.max(3,Math.round(spacing*.36));
 let extension=0,body=0;
 for(let x=left+Math.round(spacing);x<=Math.min(w-1,left+Math.round(spacing*4));x++){
  for(let y=Math.max(0,s[0]-Math.round(spacing*2.5));y<s[0]-2;y++)extension+=dark[y*w+x];
  for(let y=s[4]+3;y<=Math.min(h-1,s[4]+Math.round(spacing*2.5));y++)extension+=dark[y*w+x];
  for(let y=s[0]+2;y<=s[4]-2;y++)if(!mask.has(y))body+=dark[y*w+x];
 }
 const clef=extension>spacing*3?'treble':body>spacing*2?'bass':'unknown',candidates=[];
 for(let x=left+Math.round(spacing*7);x<=right-rx;x++)for(let y=Math.max(0,Math.round(s[0]-spacing*1.5))+ry;y<=Math.min(h-1,Math.round(s[4]+spacing*1.5))-ry;y++){
  let score=0,central=0;for(let dy=-ry;dy<=ry;dy++){let count=0;const limit=Math.round(rx*Math.sqrt(1-dy*dy/(ry*ry+1)));for(let dx=-limit;dx<=limit;dx++)if(!mask.has(y+dy)&&ink[(y+dy)*w+x+dx]){score++;count++;}if(Math.abs(dy)<=1&&count>=rx)central++;}
  if(score>=rx*ry*1.4&&central>=1){candidates.push({x,y,score});if(candidates.length>100000)throw Error('Too many notes.');}
 }
 candidates.sort((a,b)=>b.score-a.score);const heads=[];
 for(const c of candidates)if(heads.every(n=>Math.abs(n.x-c.x)>rx*1.7||Math.abs(n.y-c.y)>ry*1.8))heads.push(c);
 heads.sort((a,b)=>a.x-b.x);const notes=[];
 for(const head of heads){let up=0,down=0;for(let dx=-rx;dx<=rx;dx++){const x=head.x+dx;let u=0,d=0;for(let distance=3;distance<=Math.round(spacing*3);distance++){if(head.y-distance>=0)u+=dark[(head.y-distance)*w+x];if(head.y+distance<h)d+=dark[(head.y+distance)*w+x];}up=Math.max(up,u);down=Math.max(down,d);}
  const group=heads.filter(n=>Math.abs(n.x-head.x)<=rx*.8&&Math.abs(n.y-head.y)>=ry).length+1;
  if(!((up>=spacing*2.2&&down<=spacing*.7)||(down>=spacing*2.2&&up<=spacing*.7))&&group<3)continue;
  let preceding=0,barline=false;for(let x=Math.max(0,head.x-Math.round(spacing*1.8));x<head.x-rx-1;x++){let vertical=0;for(let y=s[0];y<=s[4];y++)vertical+=ink[y*w+x];if(vertical>(s[4]-s[0])*.78)barline=true;for(let y=Math.max(0,head.y-Math.round(spacing));y<=Math.min(h-1,head.y+Math.round(spacing));y++)if(!mask.has(y))preceding+=dark[y*w+x];}
  const step=Math.round((s[4]-head.y)/(spacing/2));notes.push({x:head.x,y:head.y,staffStep:step,pitch:clef==='unknown'||preceding>spacing*2.5&&!barline?'':pitchForStep(clef,step),duration:'',editedPitch:false});
 }
 const bars=[];for(let x=left+Math.round(spacing*7);x<=right;x++){let count=0;for(let y=s[0];y<=s[4];y++)count+=ink[y*w+x];if(count>(s[4]-s[0])*.78&&(!bars.length||x-bars.at(-1)>2))bars.push(x);}
 const barlines=bars.filter(x=>!heads.some(n=>Math.abs(n.x-x)<=rx*1.2));
 const filtered=notes.filter(n=>!barlines.some(x=>Math.abs(n.x-x)<=rx*2));
 if(!filtered.length)throw Error('No noteheads found.');if(filtered.length>128)throw Error('Too many notes.');
 return {clef,notes:filtered,lines:s};
}
function toImport(notes,clef,numerator,denominator,title){
 if(!notes.length||notes.length>128||!['treble','bass'].includes(clef)||!Number.isInteger(numerator)||numerator<1||numerator>32||![2,4,8,16].includes(denominator))throw Error('Invalid melody settings.');
 const M=root.NotationModel,capacity=M.wholeUnits*numerator/denominator,measures=[];let bar={notes:[],preserve:true},at=0;
 for(const n of notes){const match=/^([A-G])([#b]?)([0-8])$/.exec(n.pitch),duration=M.durations[n.duration]*M.wholeUnits/64;if(!match||!M.validPitch(match[1]+match[3])||!['w','h','q','8','16'].includes(n.duration)||duration>capacity)throw Error('Confirm every pitch and duration.');if(at+duration>capacity){if(at!==capacity)throw Error('Durations do not fit the measures.');measures.push(bar);bar={notes:[],preserve:true};at=0;}bar.notes.push({pitch:match[1]+match[3],accidental:match[2],duration:n.duration,dots:0,staff:1,voice:1,at});at+=duration;}
 if(at!==capacity)throw Error('Durations do not fit the measures.');measures.push(bar);if(measures.length>64)throw Error('Too many measures.');
 return {format:'sornaz-notation',version:1,metadata:{title:title.trim()||'Scanned melody',instrument:'Voice',staves:[{clef}],clef,key:'C',time:numerator+'/'+denominator,bpm:100,tempo_note:'q',tempo_dots:0},score:{measures}};
}
function open(config,importContent){
 const fa=config.locale==='fa',t=(a,b)=>fa?a:b,box=document.createElement('dialog');box.className='notation-web-dialog omr-review';let activeWorker,cancelRecognition;
 const heading=document.createElement('h2');heading.textContent=t('بررسی نت‌های تصویر','Review scanned notes');box.append(heading);
 const hint=document.createElement('p');hint.textContent=t('تشخیص آزمایشی: تصویر را به یک حامل افقی برش دهید. کشش هر نت را تعیین و ارتفاع و ترتیب همه نت‌ها را بررسی کنید. سکوت‌ها و علائم تغییر خودکار منتقل نمی‌شوند. تصویر ارسال یا ذخیره نمی‌شود.','Experimental recognition: crop to one horizontal staff. Set every duration and review all pitches and their order. Rests and accidentals are not imported automatically. The image is not uploaded or stored.');box.append(hint);
 const file=document.createElement('input');file.type='file';file.accept='image/png,image/jpeg,image/webp';file.setAttribute('aria-label',t('انتخاب تصویر','Choose image'));box.append(file);
 const camera=document.createElement('input');camera.type='file';camera.accept=file.accept;camera.setAttribute('capture','environment');camera.hidden=true;box.append(camera);
 const addButton=(label,action)=>{const b=document.createElement('button');b.type='button';b.textContent=label;b.onclick=action;box.append(b);return b;};
 addButton(t('گرفتن عکس','Take photo'),()=>camera.click());
 const canvas=document.createElement('canvas');canvas.className='omr-image';box.append(canvas);const ctx=canvas.getContext('2d',{willReadFrequently:true});let original,notes=[],clef='treble';
 const error=document.createElement('p');error.setAttribute('role','status');box.append(error);
 const explain=e=>{const messages={'Image dimensions are too large.':['ابعاد تصویر بیش از حد مجاز است.','Image dimensions are too large.'],'Exactly one staff is required.':['تصویر را به یک حامل پنج‌خطی برش دهید.','Crop to exactly one five-line staff.'],'No noteheads found.':['سرنتی پیدا نشد؛ برش واضح‌تری انتخاب کنید.','No noteheads found; try a clearer crop.'],'Confirm every pitch and duration.':['ارتفاع و کشش تمام نت‌ها را تعیین کنید.','Confirm every pitch and duration.'],'Durations do not fit the measures.':['کشش‌ها با میزان‌نما برابر نیستند؛ آن‌ها را اصلاح کنید.','Durations do not fit the measures.']};const m=messages[e.message];error.textContent=m?t(...m):t('خواندن یا تشخیص تصویر انجام نشد.','Could not read or recognize the image.');};
 const review=document.createElement('div');box.append(review);
 function showNotes(){review.replaceChildren();const title=document.createElement('input');title.maxLength=180;title.placeholder=t('عنوان قطعه','Score title');title.setAttribute('aria-label',title.placeholder);review.append(title);
  const select=(label,values,value)=>{const wrap=document.createElement('label');wrap.textContent=label;const input=document.createElement('select');values.forEach(v=>input.add(Array.isArray(v)?new Option(v[1],v[0]):new Option(v,v)));input.value=String(value);wrap.append(input);review.append(wrap);return input;};
  const clefInput=select(t('کلید','Clef'),[['treble',t('کلید سل','Treble')],['bass',t('کلید فا','Bass')]],clef),num=select(t('صورت میزان','Beats'),Array.from({length:32},(_,i)=>i+1),4),den=select(t('مخرج میزان','Beat unit'),[2,4,8,16],4);
  clefInput.onchange=()=>{clef=clefInput.value;notes.forEach((n,i)=>{if(!n.editedPitch&&n.pitch){n.pitch=pitchForStep(clef,n.staffStep);review.querySelectorAll('[data-omr-pitch]')[i].value=n.pitch;}});};
  notes.forEach((n,i)=>{const row=document.createElement('div');row.className='omr-note';const label=document.createElement('span');label.textContent=String(i+1);const pitch=document.createElement('input');pitch.value=n.pitch;pitch.dataset.omrPitch='';pitch.setAttribute('aria-label',t('ارتفاع نت ','Pitch ')+(i+1));pitch.oninput=()=>{n.pitch=pitch.value;n.editedPitch=true;};const duration=document.createElement('select');duration.setAttribute('aria-label',t('کشش نت ','Duration ')+(i+1));duration.add(new Option(t('انتخاب کشش','Choose duration'),''));[['w',t('گرد','Whole')],['h',t('سفید','Half')],['q',t('سیاه','Quarter')],['8',t('چنگ','Eighth')],['16',t('دولاچنگ','Sixteenth')]].forEach(([value,label])=>duration.add(new Option(label,value)));duration.onchange=()=>n.duration=duration.value;row.append(label,pitch,duration);review.append(row);});
  const accept=document.createElement('button');accept.type='button';accept.textContent=t('تأیید و پیش‌نمایش نت','Confirm and preview score');accept.onclick=async()=>{try{const score=toImport(notes,clef,Number(num.value),Number(den.value),title.value||t('ملودی اسکن‌شده','Scanned melody'));if(await importContent(JSON.stringify(score),'omr.json'))close();}catch(e){explain(e);}};review.append(accept);
 }
 function draw(image){canvas.width=image.width;canvas.height=image.height;ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(image,0,0);}
 async function load(input){const f=input.files?.[0];if(!f)return;error.textContent='';review.replaceChildren();try{if(f.size>12000000||!['image/png','image/jpeg','image/webp'].includes(f.type))throw Error('Invalid image.');const bitmap=await createImageBitmap(f);if(bitmap.width*bitmap.height>24000000){bitmap.close();throw Error('Image dimensions are too large.');}const scale=Math.min(1,2048/bitmap.width,2600/bitmap.height);canvas.width=Math.round(bitmap.width*scale);canvas.height=Math.round(bitmap.height*scale);ctx.fillStyle='#fff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(bitmap,0,0,canvas.width,canvas.height);bitmap.close();original=document.createElement('canvas');original.width=canvas.width;original.height=canvas.height;original.getContext('2d').drawImage(canvas,0,0);}catch(e){explain(e);}}
 file.onchange=()=>load(file);camera.onchange=()=>load(camera);
 // Drag a rectangle in the displayed image; coordinates map to the full pixel canvas.
 let start;canvas.onpointerdown=e=>{if(detect.disabled)return;start=[e.offsetX*canvas.width/canvas.clientWidth,e.offsetY*canvas.height/canvas.clientHeight];canvas.setPointerCapture(e.pointerId);};canvas.onpointerup=e=>{if(!start||detect.disabled)return;const end=[e.offsetX*canvas.width/canvas.clientWidth,e.offsetY*canvas.height/canvas.clientHeight],x=Math.max(0,Math.round(Math.min(start[0],end[0]))),y=Math.max(0,Math.round(Math.min(start[1],end[1]))),w=Math.min(canvas.width-x,Math.round(Math.abs(end[0]-start[0]))),h=Math.min(canvas.height-y,Math.round(Math.abs(end[1]-start[1])));start=null;if(w<40||h<30)return;const crop=document.createElement('canvas');crop.width=w;crop.height=h;crop.getContext('2d').drawImage(canvas,x,y,w,h,0,0,w,h);draw(crop);review.replaceChildren();};
 addButton(t('بازنشانی برش','Reset crop'),()=>{if(original&&!detect.disabled){draw(original);review.replaceChildren();}});
 async function recognize(image){if(!workerUrl||typeof Worker!=='function')return analyze(deskew(image));return new Promise((resolve,reject)=>{const worker=activeWorker=new Worker(workerUrl),timer=setTimeout(()=>{finish();reject(Error('Recognition timed out.'));},60000);const finish=()=>{clearTimeout(timer);worker.terminate();activeWorker=null;cancelRecognition=null;};cancelRecognition=()=>{finish();reject(Error('Recognition cancelled.'));};worker.onmessage=event=>{finish();event.data.error?reject(Error(event.data.error)):resolve(event.data.result);};worker.onerror=()=>{finish();reject(Error('Could not recognize the image.'));};worker.postMessage(image,[image.data.buffer]);});}
 const detect=addButton(t('تشخیص نت‌ها','Detect notes'),async()=>{if(!original)return;detect.disabled=true;file.disabled=camera.disabled=true;error.textContent=t('در حال تشخیص…','Recognizing…');review.replaceChildren();await new Promise(resolve=>setTimeout(resolve,25));try{const result=await recognize(ctx.getImageData(0,0,canvas.width,canvas.height));if(!box.isConnected)return;notes=result.notes;clef=result.clef==='bass'?'bass':'treble';error.textContent=result.clef==='unknown'?t('کلید نامشخص است؛ دستی بررسی کنید.','Clef is unknown; check it manually.'):'';showNotes();}catch(e){explain(e);}finally{detect.disabled=false;file.disabled=camera.disabled=false;}});
 function close(){cancelRecognition?.();box.close();box.remove();canvas.width=canvas.height=0;original=null;notes=[];}
 addButton(t('انصراف','Cancel'),close);box.addEventListener('cancel',e=>{e.preventDefault();close();});document.body.append(box);box.showModal();
}
root.NotationOmr={analyze,toImport,pitchForStep,open};
if(typeof document==='undefined'&&typeof root.postMessage==='function')root.onmessage=event=>{try{root.postMessage({result:analyze(deskew(event.data))});}catch(error){root.postMessage({error:error.message});}};
if(typeof module==='object')module.exports=root.NotationOmr;
})(globalThis);
