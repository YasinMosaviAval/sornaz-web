(function(root){
'use strict';
const text=(locale,fa,en)=>locale==='fa'?fa:en;
function dialog(title){const box=document.createElement('dialog');box.className='notation-web-dialog';const heading=document.createElement('h2');heading.textContent=title;box.append(heading);document.body.append(box);return box;}
function button(box,label,value,resolve){const b=document.createElement('button');b.type='button';b.textContent=label;b.onclick=()=>{box.close();box.remove();resolve(value);};box.append(b);return b;}
function preferences(){try{const raw=JSON.parse(localStorage.getItem('notation_web_preferences')||'{}');return {durationMode:[1,2,3].includes(raw.durationMode)?raw.durationMode:1,pianoLabelMode:[1,2].includes(raw.pianoLabelMode)?raw.pianoLabelMode:1};}catch(_){return {durationMode:1,pianoLabelMode:1};}}
function settings(config,apply,container){
 const locale=config.locale,box=container||dialog(text(locale,'تنظیمات نت‌نویسی','Notation settings'));
 if(container){container.replaceChildren();container.classList.add('notation-settings-form');}
 const fields=[['durationMode',text(locale,'حالت انتخاب کشش نت','Note duration mode'),[text(locale,'حالت اول','Mode one'),text(locale,'حالت دوم','Mode two'),text(locale,'حالت سوم','Mode three')]],['pianoLabelMode',text(locale,'نام نت‌های کلاویه','Piano note labels'),[text(locale,'همه نت‌ها','All notes'),text(locale,'نت‌های گام انتخاب‌شده','Selected scale notes')]]];
 const values=preferences();
 for(const [name,title,options] of fields){const label=document.createElement('label');label.textContent=title;const select=document.createElement('select');select.name=name;options.forEach((caption,i)=>select.add(new Option(caption,i+1)));select.value=String(config[name]||values[name]);label.append(select);box.append(label);}
 const hint=document.createElement('p');hint.textContent=text(locale,'نت‌های محلی فقط در همین مرورگر و حساب ذخیره می‌شوند. برای نگهداری مستقل، فایل JSON دانلود کنید؛ محل دانلود را مرورگر تعیین می‌کند.','Local sheets stay in this browser and account. Download JSON for an independent backup; the browser controls the download location.');box.append(hint);
 const status=document.createElement('p');status.setAttribute('role','status');box.append(status);
 const save=document.createElement('button');save.type='button';save.className='primary';save.textContent=text(locale,'ذخیره تنظیمات','Save settings');save.onclick=()=>{const next={durationMode:Number(box.querySelector('[name=durationMode]').value),pianoLabelMode:Number(box.querySelector('[name=pianoLabelMode]').value)};try{localStorage.setItem('notation_web_preferences',JSON.stringify(next));apply(next);if(container)status.textContent=text(locale,'تنظیمات ذخیره شد.','Settings saved.');else{box.close();box.remove();}}catch(_){status.textContent=text(locale,'ذخیره تنظیمات انجام نشد.','Could not save settings.');}};box.append(save);
 if(!container){button(box,text(locale,'انصراف','Cancel'),null,()=>{});box.addEventListener('cancel',()=>box.remove());box.showModal();}
}
function ask(title,options={}){return new Promise(resolve=>{
 const locale=document.documentElement.lang,box=dialog(title),heading=box.querySelector('h2');box.classList.add('notation-list-create');box.setAttribute('aria-label',title);
 const finish=value=>{box.close();box.remove();resolve(value);};
 const header=document.createElement('header');header.className='dialog-header';header.append(heading);
 const close=document.createElement('button');close.type='button';close.className='dialog-close';close.textContent='×';close.setAttribute('aria-label',text(locale,'بستن','Close'));close.onclick=()=>finish(null);header.append(close);
 const form=document.createElement('form'),label=document.createElement('label'),input=document.createElement('input');label.textContent=options.label||text(locale,'نام لیست','List name');input.maxLength=options.maxLength||80;input.value=options.value||'';input.required=true;input.setAttribute('aria-label',label.textContent);label.append(input);form.append(label);
 const actions=document.createElement('footer');actions.className='dialog-actions';
 const save=document.createElement('button');save.type='submit';save.className='primary';save.textContent=text(locale,'ذخیره','Save');
 const cancel=document.createElement('button');cancel.type='button';cancel.textContent=text(locale,'انصراف','Cancel');cancel.onclick=()=>finish(null);actions.append(save,cancel);form.append(actions);
 form.onsubmit=event=>{event.preventDefault();if(input.value.trim())finish(input.value.trim());else input.focus();};
 box.append(header,form);box.addEventListener('cancel',event=>{event.preventDefault();finish(null);});box.showModal();input.focus();
});}
function chooseList(lists,locale){return new Promise(resolve=>{const box=dialog(text(locale,'افزودن به لیست','Add to list'));button(box,text(locale,'لیست جدید','New list'),'__new__',resolve);for(const item of lists)button(box,item.favorite?text(locale,'علاقه‌مندی','Favorite'):item.title,item.id,resolve);button(box,text(locale,'انصراف','Cancel'),null,resolve);box.addEventListener('cancel',()=>{box.remove();resolve(null);});box.showModal();});}
root.NotationWebTools={preferences,settings,ask,chooseList};
})(globalThis);
