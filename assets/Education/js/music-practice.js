(() => {
  'use strict';
  const root=document.getElementById('musicPractice'),boot=window.musicPracticeBoot;
  if(!root||!boot)return;
  const t=(fa,en)=>boot.locale==='en'?en:fa, title=x=>boot.locale==='en'?x.titleEn:x.titleFa;
  const status=root.querySelector('[data-practice-status]'),list=root.querySelector('[data-practice-list]'),player=root.querySelector('[data-practice-player]'),editor=root.querySelector('[data-practice-editor]');
  const scope=String(boot.userId||0);let catalog, parent=null, progress=[], managing=false, active=null, activeCard=null, syncing=null;
  const dbReady=new Promise((resolve,reject)=>{const r=indexedDB.open('sornaz-music-practice-v1',1);r.onupgradeneeded=()=>r.result.createObjectStore('records');r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error);});
  async function record(key,value){const db=await dbReady;return new Promise((resolve,reject)=>{const tx=db.transaction('records',value===undefined?'readonly':'readwrite'),store=tx.objectStore('records');let result;if(value!==undefined&&key.startsWith('run:')){const read=store.get(key);read.onsuccess=()=>{const old=read.result;let merged=value;if(old){const latest=old.answers.length>value.answers.length?old:value;merged={...latest,ack:Math.max(old.ack,value.ack),closed:old.closed||value.closed,passed:old.passed||value.passed};merged.closedAck=(old.closedAck||value.closedAck)&&merged.ack>=merged.answers.length;}store.put(merged,key);};}else{const r=value===undefined?store.get(key):store.put(value,key);r.onsuccess=()=>result=r.result;}tx.oncomplete=()=>resolve(result);tx.onerror=()=>reject(tx.error);tx.onabort=()=>reject(tx.error);});}
  async function runs(){const db=await dbReady;return new Promise((resolve,reject)=>{const tx=db.transaction('records'),r=tx.objectStore('records').openCursor(),rows=[];r.onsuccess=()=>{const c=r.result;if(c){if(String(c.key).startsWith('run:'+scope+':'))rows.push(c.value);c.continue();}else resolve(rows.sort((a,b)=>a.startedAt.localeCompare(b.startedAt)));};r.onerror=()=>reject(r.error);});}
  async function api(path,payload){
    const controller=new AbortController(),timeout=setTimeout(()=>controller.abort(),20000);
    try{
      const response=await fetch(boot.api+'/'+path,{method:payload?'POST':'GET',credentials:'same-origin',cache:'no-store',signal:controller.signal,headers:{Accept:'application/json','Accept-Language':boot.locale,...(payload?{'X-CSRF-TOKEN':boot.csrf,'X-Sornaz-Actor':String(boot.userId||0)}:{})},...(payload?{body:new URLSearchParams({_token:boot.csrf,payload:JSON.stringify(payload)})}:{})});
      const body=await response.json(),envelope=body.data??body;
      if(!response.ok||envelope.success===false){const error=new Error(envelope.message||t('درخواست انجام نشد.','Request failed.'));error.status=response.status;throw error;}
      return envelope.data??envelope;
    }finally{clearTimeout(timeout);}
  }
  const fail=e=>{status.textContent=e.message||t('ارتباط برقرار نشد.','Connection failed.');};
  function element(tag,text='',className=''){const el=document.createElement(tag);el.textContent=text;el.className=className;return el;}
  function button(text,action,primary=false){const b=element('button',text,'rounded-xl border px-4 py-3 '+(primary?'bg-indigo-600 text-white':'hover:bg-gray-50'));b.type='button';b.onclick=()=>Promise.resolve().then(action).catch(fail);return b;}
  function visibleNodes(){return catalog.nodes.filter(n=>(n.parentId??null)===parent);}
  function render(){
    if(!catalog||active)return;
    list.replaceChildren();root.querySelector('[data-practice-manage]').hidden=!Object.values(catalog.capabilities||{}).some(Boolean);
    const crumbs=root.querySelector('[data-site-breadcrumb]'),items=crumbs.querySelector('[data-breadcrumb-items]');items.replaceChildren();
    const appendCrumb=(label,action,href)=>{if(items.children.length)items.append(crumbs.querySelector('[data-breadcrumb-separator]').content.cloneNode(true));const fragment=crumbs.querySelector(action||href?'[data-breadcrumb-link]':'[data-breadcrumb-current]').content.cloneNode(true),item=fragment.firstElementChild;item.textContent=label;if(action){item.href='#';item.onclick=e=>{e.preventDefault();action();};}else if(href)item.href=href;items.append(fragment);};
    appendCrumb(t('خانه','Home'),null,'/');
    appendCrumb(t('آزمون‌ها','Tests'),parent?()=>{parent=null;render();}:null);
    let n=catalog.nodes.find(n=>n.id===parent),path=[];while(n){path.unshift(n);n=catalog.nodes.find(p=>p.id===n.parentId);}
    for(const node of path)appendCrumb(title(node),node.id===parent?null:()=>{parent=node.id;render();});
    for(const node of visibleNodes()){
      const card=element('article','','space-y-3 rounded-2xl border bg-white p-4');card.append(button(title(node),()=>{parent=node.id;render();},true));
      if(managing&&catalog.capabilities.edit&&node.canManage)card.append(button(t('ویرایش بخش','Edit section'),()=>nodeForm(node)));
      if(managing&&catalog.capabilities.publish&&node.canManage)card.append(button(node.published?t('توقف انتشار','Unpublish'):t('انتشار','Publish'),async()=>{await api('publish',{kind:'node',id:node.id,version:node.version,published:!node.published});await load();}));
      list.append(card);
    }
    for(const exam of catalog.exams.filter(e=>e.nodeId===parent)){
      const card=element('article','','practice-exam-card space-y-3 rounded-2xl border bg-white p-4');card.append(element('h2',title(exam),'font-bold'));
      if(exam.audience==='public')card.append(element('p',exam.ownerId===1?t('رایگان · مدیر سایت','Free · Site administrator'):t('رایگان','Free'),'text-sm text-gray-500'));
      const done=progress.find(p=>p.examId===exam.id&&p.version===exam.version);card.append(element('p',`${'★'.repeat(done?.stars||0)}${'☆'.repeat(5-(done?.stars||0))} · ${done?.completedStages?.length||0}/${exam.stageCount}`,'text-sm'));
      const launch=button(exam.access?t('شروع تمرین','Start practice'):t('خرید آزمون یا اشتراک','Buy test or subscription'),()=>exam.access?start(exam,card):offers(exam.ownerId,exam.id),true);launch.dataset.practiceStart='';card.append(launch);
      if(managing&&catalog.capabilities.edit)card.append(button(t('ویرایش آزمون','Edit test'),()=>examForm(exam)));
      if(managing&&catalog.capabilities.publish)card.append(button(exam.published?t('توقف انتشار','Unpublish'):t('انتشار','Publish'),async()=>{await api('publish',{id:exam.id,version:exam.version,published:!exam.published});await load();}));
      if(managing&&catalog.capabilities.approvePlans)card.append(button(t('سهم درآمد','Revenue share'),()=>shareForm(exam)));
      list.append(card);
    }
    if(!list.children.length)list.append(element('p',t('هنوز آزمونی در این بخش منتشر نشده است.','No tests have been published in this section yet.')));
    if(managing){if(catalog.capabilities.create)list.prepend(button(t('افزودن بخش','Add section'),()=>nodeForm()),button(t('افزودن آزمون','Add test'),()=>examForm()),button(t('پیشنهاد اشتراک','Propose subscription'),()=>planForm()));if(catalog.capabilities.approvePlans)list.prepend(button(t('بررسی اشتراک‌ها و سهم درآمد','Review subscriptions and revenue'),reviewPlans));}
  }
  let loadingCatalog=null,loadingMode=false,queuedCatalog=null;
  function load(force=false){
    if(loadingCatalog){
      if(!force&&loadingMode===managing)return loadingCatalog;
      if(!queuedCatalog)queuedCatalog=loadingCatalog.catch(()=>{}).then(()=>{queuedCatalog=null;return load();});
      return queuedCatalog;
    }
    loadingMode=managing;
    loadingCatalog=loadCatalog().finally(()=>loadingCatalog=null);
    return loadingCatalog;
  }
  async function loadCatalog(){
    try{const fresh=await api('catalog'+(managing?'?manage=1':''));if(fresh.viewerId!==Number(scope)){const error=new Error(t('حساب تغییر کرده است؛ صفحه را تازه کنید.','Your account changed; refresh the page.'));error.status=401;throw error;}catalog=fresh;status.textContent='';}
    catch(e){if([401,403].includes(e.status)){await record('catalog:'+scope,null);catalog=null;active=null;list.replaceChildren();player.hidden=true;editor.hidden=true;throw e;}throw e;}
    if(parent&&!catalog.nodes.some(n=>n.id===parent))parent=null;
    render();
    if(boot.userId)try{progress=(await api('progress')).progress;}catch(_){}
    const local=await runs();for(const run of local.filter(r=>r.passed)){let p=progress.find(p=>p.examId===run.exam.id&&p.version===run.exam.version);if(!p){p={examId:run.exam.id,version:run.exam.version,completedStages:[]};progress.push(p);}if(!p.completedStages.includes(run.stage))p.completedStages.push(run.stage);p.stars=Math.min(5,Math.floor(p.completedStages.length*5/run.exam.stageCount));}
    render();
  }
  async function upload(){
    if(syncing)return syncing;
    if(!boot.userId)throw new Error(t('برای ثبت آنلاین وارد حساب شوید.','Sign in to upload results.'));
    syncing=(async()=>{for(const run of await runs()){
      if(!run.answers.length)continue;
      while(run.ack<run.answers.length||(run.closed&&!run.closedAck)){
        const chunk=run.answers.slice(run.ack,run.ack+100),closed=run.closed&&run.ack+chunk.length===run.answers.length;
        const result=await api('results',{runId:run.id,examId:run.exam.id,version:run.exam.version,stage:run.stage,permitExpiresAt:run.exam.permitExpiresAt,downloadPermit:run.exam.downloadPermit,answers:chunk,closed});
        if(result.acceptedThrough!==run.ack+chunk.length)throw new Error(t('تأیید ثبت پاسخ معتبر نیست.','Invalid answer acknowledgement.'));
        run.ack=result.acceptedThrough;run.closedAck=closed;await record('run:'+scope+':'+run.id,run);
      }
    }})().finally(()=>syncing=null);
    return syncing;
  }
  function question(exam){const c=exam.config;if(c.kind==='interval'){const expected=c.quarterSteps[Math.floor(Math.random()*c.quarterSteps.length)],sign=c.direction==='ascending'?1:-1;const minimum=Math.ceil(21+(sign<0?expected/2:0)),maximum=Math.floor(108-(sign>0?expected/2:0));return {expected,rootMidi:c.variableRoot?minimum+Math.floor(Math.random()*(maximum-minimum+1)):60};}return {question:Math.floor(Math.random()*c.questions.length)};}
  async function start(exam,card){
    if(!navigator.onLine)throw new Error(t('برای اجرای آزمون به اینترنت متصل شوید.','Connect to the internet to take this test.'));
    if(active)throw new Error(t('ابتدا تمرین باز را ببندید.','Close the current practice first.'));
    const done=progress.find(p=>p.examId===exam.id&&p.version===exam.version),completed=new Set(done?.completedStages||[]);let stage=1;while(completed.has(stage))stage++;
    if(stage>exam.stageCount)stage=exam.stageCount;
    active={id:crypto.randomUUID().replaceAll('-',''),exam:structuredClone(exam),stage,answers:[],ack:0,closedAck:false,closed:false,passed:false,streak:0,startedAt:new Date().toISOString(),current:question(exam),started:performance.now(),busy:false};
    activeCard=card;card.classList.add('practice-exam-active');card.querySelector('[data-practice-start]').hidden=true;
    card.append(player);player.hidden=false;drawPlayer();
  }
  let audioContext,playback=0,oscillator;
  async function tone(frequency,duration){audioContext??=new AudioContext();await audioContext.resume();const o=audioContext.createOscillator(),g=audioContext.createGain();o.frequency.value=frequency;g.gain.setValueAtTime(.1,audioContext.currentTime);g.gain.exponentialRampToValueAtTime(.001,audioContext.currentTime+duration);o.connect(g);g.connect(audioContext.destination);o.start();oscillator=o;o.stop(audioContext.currentTime+duration);await new Promise(r=>setTimeout(r,duration*1000));}
  async function play(){const run=active;if(!run||run.busy)return;run.busy=true;run.playing=true;const generation=++playback;try{const c=run.exam.config,q=run.current;if(c.kind==='interval'){await tone(440*2**((q.rootMidi-69)/12),.6);await new Promise(r=>setTimeout(r,150));if(generation===playback&&active===run)await tone(440*2**((q.rootMidi+(c.direction==='ascending'?1:-1)*q.expected/2-69)/12),.6);}else if(c.kind==='rhythm'){const qd=c.questions[q.question];for(const beat of qd.beats){if(active!==run||generation!==playback)break;await tone(880,.05);await new Promise(r=>setTimeout(r,Math.max(0,60000/qd.bpm*beat-50)));}}}finally{run.busy=false;run.playing=false;}}
  function drawPlayer(){
    player.replaceChildren();const run=active;if(!run)return;
    player.append(element('h3',`${t('مرحله','Stage')} ${run.stage}/${run.exam.stageCount}`,'text-lg font-bold'));
    const meter=element('progress','','practice-progress');meter.max=run.exam.requiredWins;meter.value=run.streak;meter.setAttribute('aria-label',t('پیشرفت پاسخ‌های درست','Correct answer progress'));player.append(meter,element('p',`${run.streak}/${run.exam.requiredWins}`));
    if(!run.closed){
      const c=run.exam.config,q=run.current;
      if(c.kind!=='choice'){const playButton=button('▶',play,true);playButton.classList.add('practice-play');playButton.setAttribute('aria-label',t('شنیدن پرسش','Play question'));player.append(playButton);}
      const choices=c.kind==='interval'?c.quarterSteps.map(value=>[value,intervalLabel(value)]):c.questions[q.question].choices.map((value,index)=>[index,value]);
      if(c.kind!=='interval')player.append(element('p',boot.locale==='en'?c.questions[q.question].promptEn:c.questions[q.question].promptFa));
      const group=element('div','',choices.length>5?'practice-choices practice-choices-grid':'practice-choices'),check=button(t('بررسی پاسخ','Check answer'),()=>answer(run.selected),true);check.disabled=run.selected===undefined;
      for(const [value,label]of choices){const choice=button(label,()=>{run.selected=value;for(const item of group.children)item.setAttribute('aria-pressed',String(item===choice));check.disabled=false;});choice.setAttribute('aria-pressed',String(run.selected===value));group.append(choice);}
      player.append(group,check);
    }
    else player.append(element('p',run.passed?t('مرحله کامل شد.','Stage completed.'):t('تمرین ذخیره شد.','Practice saved.')));
    player.append(button(t('بازگشت','Back'),async()=>{if(run.busy&&!run.playing)return;playback++;try{oscillator?.stop();}catch(_){}run.closed=true;await record('run:'+scope+':'+run.id,run);active=null;player.hidden=true;list.after(player);activeCard?.classList.remove('practice-exam-active');activeCard=null;await load();if(boot.userId)upload().catch(fail);}));
  }
  function intervalLabel(value){
    const names={2:['دوم کوچک','Minor second'],4:['دوم بزرگ','Major second'],6:['سوم کوچک','Minor third'],7:['سوم خنثی','Neutral third'],8:['سوم بزرگ','Major third'],10:['چهارم درست','Perfect fourth'],12:['تریتون','Tritone'],14:['پنجم درست','Perfect fifth'],16:['ششم کوچک','Minor sixth'],17:['ششم خنثی','Neutral sixth'],18:['ششم بزرگ','Major sixth'],20:['هفتم کوچک','Minor seventh'],21:['هفتم خنثی','Neutral seventh'],22:['هفتم بزرگ','Major seventh'],24:['اکتاو درست','Perfect octave']};
    return names[value]?t(...names[value]):`${value/2} ${t('نیم‌پرده','semitones')}`;
  }
  async function answer(selected){
    if(!navigator.onLine)throw new Error(t('برای ثبت پاسخ به اینترنت متصل شوید.','Connect to the internet to submit your answer.'));
    const run=active;if(!run||run.busy||run.closed)return;
    run.busy=true;const q=run.current,c=run.exam.config,expected=c.kind==='interval'?q.expected:c.questions[q.question].correct;
    const next=structuredClone({...run,busy:false});delete next.started;delete next.selected;
    next.answers.push({...q,selected,sequence:run.answers.length+1,elapsedMs:Math.min(86400000,Math.round(performance.now()-run.started)),answeredAt:new Date().toISOString()});next.streak=expected===selected?run.streak+1:0;next.passed=next.streak>=run.exam.requiredWins;next.closed=next.passed;next.current=question(run.exam);
    try{await record('run:'+scope+':'+run.id,next);delete run.selected;Object.assign(run,next,{started:performance.now()});status.textContent=expected===selected?t('درست','Correct'):t('دوباره تلاش کنید.','Try again.');drawPlayer();if(boot.userId)upload().catch(fail);}finally{run.busy=false;}
  }
  function form(heading,submit){editor.hidden=false;editor.replaceChildren(element('h2',heading,'text-lg font-bold'));const f=element('form','','grid gap-4 sm:grid-cols-2');editor.append(f);const controls=element('div','','flex gap-2 sm:col-span-2'),save=element('button',t('ذخیره','Save'),'rounded-xl bg-indigo-600 px-4 py-3 text-white');save.type='submit';controls.append(save,button(t('بستن','Close'),()=>editor.hidden=true));f.append(controls);f.onsubmit=async e=>{e.preventDefault();save.disabled=true;try{await submit(new FormData(f));editor.hidden=true;await load();}catch(e){fail(e);}finally{save.disabled=false;}};return f;}
  const asciiNumbers=value=>String(value).replace(/[۰-۹]/g,c=>String(c.charCodeAt(0)-1776)).replace(/[٠-٩]/g,c=>String(c.charCodeAt(0)-1632)).replace(/،/g,',').replace(/٫/g,'.');
  const numericRanges={position:[0,100000],stageCount:[1,100],requiredWins:[1,100],amount:[1000,1000000000],days:[1,366],bpm:[30,240],teacherPercent:[0,100]};
  function fieldHint(name){
    if(name==='slug')return t('۱ تا ۱۰۰ نویسه؛ شروع با حرف انگلیسی، سپس حرف، عدد یا خط تیره. نمونه: ear-training. از عنوان انگلیسی پیشنهاد می‌شود.','1–100 characters: start with an English letter, then letters, digits or hyphens. Example: ear-training. Suggested from the English title.');
    if(numericRanges[name]){const [min,max]=numericRanges[name];return name==='teacherPercent'?t('درصدی از ۰ تا ۱۰۰ با حداکثر دو رقم اعشار.','Percentage from 0 to 100, with at most two decimal places.'):t(`عدد صحیح از ${min} تا ${max}، بدون جداکنندهٔ هزارگان.`,`A whole number from ${min} to ${max}, without thousands separators.`);}
    if(name==='quarterSteps')return t('اعداد صحیح ۱ تا ۲۴ با ویرگول جدا شوند؛ نمونه: 14,24. هر واحد یک ربع‌پرده است.','Comma-separated whole numbers from 1 to 24, for example 14,24. Each unit is a quarter-tone.');
    if(name==='beats')return t('۱ تا ۶۴ کشش، هرکدام از 0.125 تا 8؛ با ویرگول جدا کنید. نمونه: 1,0.5,0.5','1–64 beat lengths, each from 0.125 to 8, separated by commas. Example: 1,0.5,0.5');
    if(name==='choices')return t('۲ تا ۱۲ گزینه، هر گزینه در یک سطر و حداکثر ۲۰۰ نویسه.','2–12 choices, one per line, up to 200 characters each.');
    if(name==='correct')return t('شمارهٔ گزینهٔ درست از صفر شروع می‌شود؛ برای گزینهٔ اول 0 وارد کنید.','The correct choice index starts at zero; enter 0 for the first choice.');
    return t(`متن الزامی، حداکثر ${name.startsWith('prompt')?2000:200} نویسه.`,`Required text, up to ${name.startsWith('prompt')?2000:200} characters.`);
  }
  function fieldError(name,raw,container,input){
    const value=raw.trim();if(!value)return t('این فیلد را تکمیل کنید.','Complete this field.');
    if(name==='slug'){
      if(!/^[a-z][a-z0-9-]{0,99}$/.test(value))return t('با حرف انگلیسی شروع کنید؛ فقط حرف انگلیسی، عدد و خط تیره مجاز است. نمونه: ear-training','Start with an English letter; use only English letters, digits and hyphens. Example: ear-training');
      if(catalog.nodes.some(n=>n.slug===value&&n.id!==Number(input.dataset.nodeId||0)))return t('این نشانی قبلاً استفاده شده است؛ نشانی دیگری انتخاب کنید.','This address is already used; choose another one.');
      return '';
    }
    if(numericRanges[name]){
      const [min,max]=numericRanges[name],number=Number(value),valid=name==='teacherPercent'?/^(?:\d+(?:\.\d{0,2})?|\.\d{1,2})$/.test(value):/^\d+$/.test(value)&&Number.isSafeInteger(number);
      return valid&&number>=min&&number<=max?'':fieldHint(name);
    }
    if(['quarterSteps','beats'].includes(name)){
      const parts=value.split(',').map(v=>v.trim());
      const valid=parts.length<=(name==='beats'?64:24)&&parts.every(v=>v!==''&&Number.isFinite(Number(v))&&(name==='beats'?/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)(?:e[+-]?\d+)?$/i.test(v)&&Number(v)>=.125&&Number(v)<=8:/^\d+$/.test(v)&&Number(v)>=1&&Number(v)<=24));
      return valid?'':fieldHint(name);
    }
    if(name==='choices'){
      const choices=value.split('\n').filter(v=>v.trim());
      return choices.length>=2&&choices.length<=12&&choices.every(v=>[...v].length<=200)?'':fieldHint(name);
    }
    if(name==='correct'){
      const total=(container.querySelector('[name=choices]')?.value||'').split('\n').filter(v=>v.trim()).length;
      return /^\d+$/.test(value)&&Number(value)<total?'':t(`شماره‌ای از ۰ تا ${Math.max(0,total-1)} وارد کنید؛ ابتدا گزینه‌ها را تکمیل کنید.`,`Enter an index from 0 to ${Math.max(0,total-1)}; complete the choices first.`);
    }
    return [...value].length<=(name.startsWith('prompt')?2000:200)?'':fieldHint(name);
  }
  let fieldSequence=0;
  function field(f,name,label,value='',type='text'){
    const wrapper=element('label',label,'grid gap-2 text-sm'),input=element(type==='textarea'?'textarea':'input','','rounded-xl border bg-transparent p-3'),help=element('small');
    input.name=name;if(type!=='textarea')input.type='text';if(type==='number')input.inputMode=name==='teacherPercent'?'decimal':'numeric';
    input.value=String(value??'');input.required=true;help.id=`practice-field-help-${++fieldSequence}`;help.setAttribute('aria-live','polite');input.setAttribute('aria-describedby',help.id);
    const update=(force=false)=>{
      const focused=force||document.activeElement===input,error=fieldError(name,input.value,f,input);
      input.setCustomValidity(error);input.setAttribute('aria-invalid',error?'true':'false');
      help.textContent=focused?(error||t('مقدار واردشده قابل قبول است.','This value is acceptable.')):fieldHint(name);
      help.className='text-xs '+(focused?(error?'text-red-600 dark:text-red-400':'text-emerald-600 dark:text-emerald-400'):'text-gray-500 dark:text-gray-400');
    };
    input.addEventListener('input',()=>{
      if(numericRanges[name]||['quarterSteps','beats','correct'].includes(name))input.value=asciiNumbers(input.value);
      if(name==='slug')input.value=input.value.toLowerCase();
      update();if(name==='choices')f.querySelector('[name=correct]')?.dispatchEvent(new Event('input'));
    });
    input.addEventListener('focus',()=>update());input.addEventListener('blur',()=>update());input.addEventListener('invalid',()=>update(true));
    wrapper.append(input,help);f.prepend(wrapper);update();return input;
  }
  function select(f,name,label,options,value){const wrapper=element('label',label,'grid gap-2 text-sm'),input=element('select','','rounded-xl border bg-transparent p-3');input.name=name;for(const [key,text]of options){const o=element('option',text);o.value=key;input.append(o);}input.value=String(value);wrapper.append(input);f.prepend(wrapper);return input;}
  const nodeOptions=()=>catalog.nodes.map(n=>[n.id,`${'—'.repeat(n.depth-1)} ${title(n)}`]);
  function userPicker(f,exam){
    const selected=new Map((exam.users||[]).map(u=>[u.id,u.username])),ids=new Set(exam.userIds||[]),box=element('div','','space-y-2 sm:col-span-2'),chips=element('div','','flex flex-wrap gap-2'),choices=element('div','','flex flex-wrap gap-2'),input=element('input','','w-full rounded-xl border bg-transparent p-3'),hidden=document.createElement('input');
    hidden.type='hidden';hidden.name='userIds';input.type='search';input.placeholder=t('جستجوی نام کاربری هنرجو','Search student username');input.setAttribute('aria-label',input.placeholder);
    const draw=()=>{hidden.value=[...ids].join(',');chips.replaceChildren();for(const id of ids)chips.append(button('@'+(selected.get(id)||String(id))+' ×',()=>{ids.delete(id);draw();}));};
    let timer,sequence=0;input.onkeydown=e=>{if(e.key==='Enter')e.preventDefault();};input.oninput=()=>{clearTimeout(timer);const current=++sequence;choices.replaceChildren();if(input.value.trim().length<2)return;timer=setTimeout(async()=>{try{const data=await api('people?q='+encodeURIComponent(input.value.trim()));if(current!==sequence||data.viewerId!==Number(scope))return;choices.replaceChildren();for(const user of data.users)choices.append(button('@'+user.username,()=>{ids.add(user.id);selected.set(user.id,user.username);draw();choices.replaceChildren();input.value='';}));}catch(e){fail(e);}},250);};
    box.append(element('label',t('هنرجویان یا کاربران منتخب','Selected students or users')),input,chips,choices,hidden);f.prepend(box);draw();
  }
  function nodeForm(node={}){
    const f=form(t('بخش آزمون‌ها','Test section'),data=>api('nodes',{id:node.id||0,version:node.version,parentId:Number(data.get('parentId')),slug:data.get('slug'),titleFa:data.get('titleFa'),titleEn:data.get('titleEn'),position:Number(data.get('position')),published:data.get('published')==='1'}));
    select(f,'published',t('انتشار','Publication'),[['0',t('پیش‌نویس','Draft')],['1',t('منتشرشده','Published')]],node.published?'1':'0').disabled=!catalog.capabilities.publish;
    field(f,'position',t('ترتیب','Order'),node.position||0,'number');
    select(f,'parentId',t('بخش والد','Parent section'),[[0,t('سطح اول','First level')],...catalog.nodes.filter(n=>n.depth<3&&n.id!==node.id).map(n=>[n.id,title(n)])],node.parentId||parent||0);
    const address=field(f,'slug',t('نشانی کوتاه انگلیسی','Short English address'),node.slug||''),english=field(f,'titleEn',t('عنوان انگلیسی','English title'),node.titleEn||'');
    address.dataset.nodeId=String(node.id||0);address.dispatchEvent(new Event('input'));
    let automatic=!node.id&&!node.slug;
    address.addEventListener('input',event=>{if(event.isTrusted)automatic=address.value.trim()==='';});
    english.addEventListener('input',()=>{
      if(!automatic)return;
      let value=english.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
      if(value&&!/^[a-z]/.test(value))value='section-'+value;
      address.value=value.slice(0,100);address.dispatchEvent(new Event('input'));
    });
    field(f,'titleFa',t('عنوان فارسی','Persian title'),node.titleFa||'');
  }
  function examForm(exam={}){
    let f;f=form(t('تعریف آزمون','Test definition'),data=>{let config;const kind=data.get('kind');if(kind==='interval')config={kind,quarterSteps:String(data.get('quarterSteps')).split(',').map(Number),direction:data.get('direction'),variableRoot:data.get('variableRoot')==='1'};else config={kind,questions:[...f.querySelectorAll('[data-question]')].map(row=>({promptFa:row.querySelector('[name=promptFa]').value,promptEn:row.querySelector('[name=promptEn]').value,choices:row.querySelector('[name=choices]').value.split('\n').filter(Boolean),correct:Number(row.querySelector('[name=correct]').value),...(kind==='rhythm'?{beats:row.querySelector('[name=beats]').value.split(',').map(Number),bpm:Number(row.querySelector('[name=bpm]').value)}:{})}))};return api('exams',{id:exam.id||0,version:exam.version,nodeId:Number(data.get('nodeId')),titleFa:data.get('titleFa'),titleEn:data.get('titleEn'),position:Number(data.get('position')),stageCount:Number(data.get('stageCount')),requiredWins:Number(data.get('requiredWins')),published:data.get('published')==='1',audience:data.get('audience'),userIds:String(data.get('userIds')||'').split(',').filter(x=>x.trim()).map(Number),config});});
    const questions=element('div','','space-y-3 sm:col-span-2');f.prepend(questions);
    function addQuestion(q={}){const row=element('fieldset','','grid gap-2 rounded-xl border p-3 sm:grid-cols-2');row.dataset.question='1';questions.append(row);field(row,'promptFa',t('پرسش فارسی','Persian question'),q.promptFa||'');field(row,'promptEn',t('پرسش انگلیسی','English question'),q.promptEn||'');field(row,'choices',t('گزینه‌ها؛ هر گزینه در یک سطر','Choices, one per line'),(q.choices||[]).join('\n'),'textarea');field(row,'correct',t('شماره گزینهٔ درست (از صفر)','Correct choice index (starting at zero)'),q.correct||0,'number');const beats=field(row,'beats',t('کشش ضرب‌ها با ویرگول','Beat lengths separated by commas'),(q.beats||[1,1]).join(','));const bpm=field(row,'bpm',t('ضرب در دقیقه','Beats per minute'),q.bpm||80,'number');row.append(button(t('حذف پرسش','Remove question'),()=>row.remove()));return [beats,bpm];}
    for(const q of exam.config?.questions||[])addQuestion(q);questions.append(button(t('افزودن پرسش','Add question'),()=>addQuestion()));
    const kind=select(f,'kind',t('نوع تمرین','Practice type'),[['interval',t('تشخیص فاصله','Interval recognition')],['choice',t('چندگزینه‌ای','Multiple choice')],['rhythm',t('تشخیص ریتم','Rhythm recognition')]],exam.config?.kind||'interval');
    const intervalFields=[field(f,'quarterSteps',t('فاصله‌ها با واحد ربع‌پرده و ویرگول','Quarter-tone distances separated by commas'),(exam.config?.quarterSteps||[14,24]).join(',')),select(f,'direction',t('جهت','Direction'),[['ascending',t('بالارونده','Ascending')],['descending',t('پایین‌رونده','Descending')]],exam.config?.direction||'ascending'),select(f,'variableRoot',t('نت پایهٔ متغیر','Variable starting note'),[['0',t('خیر','No')],['1',t('بله','Yes')]],exam.config?.variableRoot?'1':'0')];
    const update=()=>{questions.hidden=kind.value==='interval';intervalFields.forEach(input=>{input.closest('label').hidden=kind.value!=='interval';input.disabled=kind.value!=='interval';});questions.querySelectorAll('input,textarea').forEach(input=>input.disabled=kind.value==='interval');};kind.onchange=update;update();
    userPicker(f,exam);
    select(f,'audience',t('مخاطبان','Audience'),[['public',t('عمومی','Public')],['users',t('کاربران منتخب / هنرجویان شخصی','Selected users / personal students')],['students',t('هنرجویان فعال مدرس','Teacher’s active students')],['subscribers',t('مشترکان مدرس','Teacher’s subscribers')]],exam.audience||'public');
    select(f,'published',t('انتشار','Publication'),[['0',t('پیش‌نویس','Draft')],['1',t('منتشرشده','Published')]],exam.published?'1':'0').disabled=!catalog.capabilities.publish;
    field(f,'stageCount',t('تعداد مراحل','Number of stages'),exam.stageCount||5,'number');field(f,'requiredWins',t('پاسخ درست پیاپی برای هر مرحله','Consecutive correct answers per stage'),exam.requiredWins||10,'number');field(f,'position',t('ترتیب','Order'),exam.position||0,'number');select(f,'nodeId',t('بخش؛ هر سه سطح مجاز است','Section; any of the three levels'),nodeOptions(),exam.nodeId||parent||catalog.nodes[0]?.id);field(f,'titleEn',t('عنوان انگلیسی','English title'),exam.titleEn||'');field(f,'titleFa',t('عنوان فارسی','Persian title'),exam.titleFa||'');
  }
  function planForm(){
    const f=form(t('پیشنهاد اشتراک آزمون‌ها','Propose test subscription'),data=>api('plans/propose',{examId:0,nodeId:Number(data.get('nodeId')),title:data.get('title'),amount:Number(data.get('amount')),days:Number(data.get('days'))}));
    select(f,'nodeId',t('محدوده اشتراک','Subscription scope'),[[0,t('همه آزمون‌های مدرس','All teacher tests')],...catalog.nodes.map(n=>[n.id,title(n)])],0);
    select(f,'days',t('مدت اشتراک','Subscription duration'),[[30,t('ماهانه','Monthly')],[90,t('سه ماهه','Quarterly')],[180,t('شش ماهه','Six months')],[365,t('سالانه','Yearly')]],30);
    field(f,'amount',t('مبلغ به تومان','Amount in toman'),1000,'number');field(f,'title',t('عنوان پیشنهاد','Offer title'));
  }
  function shareForm(exam){const f=form(t('سهم درآمد این آزمون','Revenue share for this test'),data=>api('shares',{id:exam.id,shareVersion:exam.shareVersion,teacherBps:Math.round(Number(data.get('teacherPercent'))*100)}));const input=field(f,'teacherPercent',t('درصد مدرس؛ باقیمانده سهم سرناز است','Teacher percentage; the remainder belongs to Sornaz'),(exam.teacherBps??8000)/100,'number');input.min='0';input.max='100';input.step='.01';}
  async function offers(owner,examId){
    const data=await api('plans?examId='+examId);editor.hidden=false;editor.replaceChildren(element('h2',t('خرید آزمون یا اشتراک ویژه مدرس','Individual purchase or teacher VIP'),'font-bold'));
    let rootId=catalog.exams.find(e=>e.id===examId)?.nodeId;const visited=new Set();while(rootId&&!visited.has(rootId)){visited.add(rootId);const node=catalog.nodes.find(n=>n.id===rootId);if(!node?.parentId)break;rootId=node.parentId;}
    const plans=data.plans.filter(p=>Number(p.teacher_id)===owner&&p.status==='approved'&&!p.exam_id&&(!p.node_id||visited.has(Number(p.node_id))));
    for(const plan of plans){
      const checkoutKey=crypto.randomUUID().replaceAll('-','');
      editor.append(element('p',`${plan.title} · ${plan.amount} ${t('تومان','toman')} · ${plan.exam_id?t('خرید دائمی این آزمون','Permanent access to this test'):`${plan.days} ${(plan.node_id?t('روز؛ آزمون‌های مدرس در این بخش','days; teacher tests in this section'):t('روز؛ همه آزمون‌های مدرس','days; all teacher tests'))}`}`),button(t('خرید','Purchase'),async()=>{
        const response=await api('purchase',{planId:Number(plan.id),planVersion:Number(plan.version),checkoutKey});
        const url=new URL(response.redirectUrl,location.origin);
        if(url.origin!==location.origin&&!['https://www.zarinpal.com','https://sandbox.zarinpal.com'].includes(url.origin))throw Error('Invalid checkout origin');
        location.assign(url.href);
      },true));
    }
    if(!plans.length)editor.append(element('p',t('هنوز پیشنهاد تأییدشده‌ای وجود ندارد.','No approved offers are available.')));
    editor.append(button(t('بستن','Close'),()=>editor.hidden=true));
  }
  async function reviewPlans(){const data=await api('plans');editor.hidden=false;editor.replaceChildren(element('h2',t('بررسی پلن‌ها','Review plans'),'font-bold'));for(const plan of data.plans){const row=element('div','','flex flex-wrap items-center gap-3 border-b py-3');row.append(element('span',`${plan.title} · ${plan.amount} · ${plan.days} · ${plan.status}`));const input=element('input','','w-24 rounded border p-2');input.type='number';input.min='0';input.max='100';input.step='.01';input.placeholder=t('سهم مدرس٪','Teacher %');input.setAttribute('aria-label',input.placeholder);const single=plan.exam_id&&catalog.exams.find(e=>e.id===Number(plan.exam_id));input.value=single?(single.teacherBps??8000)/100:(plan.teacher_bps===null?'80':Number(plan.teacher_bps)/100);input.readOnly=!!plan.exam_id;input.title=plan.exam_id?t('سهم خرید تکی در تنظیمات همان آزمون تعیین می‌شود.','Individual purchases use the share configured for that test.'):'';row.append(input,button(t('تأیید','Approve'),async()=>{if(input.value==='')throw new Error(t('درصد سهم مدرس را تعیین کنید.','Set the teacher share.'));await api('plans/approve',{id:Number(plan.id),version:Number(plan.version),teacherBps:Math.round(Number(input.value)*100),status:'approved'});await reviewPlans();}),button(t('رد / توقف فروش','Reject / stop sales'),async()=>{await api('plans/approve',{id:Number(plan.id),version:Number(plan.version),teacherBps:Math.round(Number(input.value||0)*100),status:'rejected'});await reviewPlans();}));editor.append(row);}for(const item of data.revenue)editor.append(element('p',`${t('سهم ثبت‌شده','Recorded share')}: ${item.amount} IRT · ${item.beneficiary} · #${item.order_id}`));}
  root.querySelector('[data-practice-manage]').onclick=()=>{managing=!managing;load().catch(fail);};
  window.addEventListener('online',()=>{load().then(()=>boot.userId&&upload()).catch(fail);});
  setInterval(()=>{if(document.visibilityState==='visible'&&!active&&!managing)load().catch(fail);},60000);
  load().then(()=>boot.userId&&upload()).catch(fail);
})();
