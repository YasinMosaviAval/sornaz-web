(function(){
'use strict';
const fa=(document.documentElement.lang||'').toLowerCase().startsWith('fa')||document.documentElement.dir==='rtl';
const libraryUrl='/assets/vendor/jalalidatepicker/jalalidatepicker.min.js';
let libraryPromise=null,pendingInput=null,started=false;
function ensureLibrary(){
    if(window.jalaliDatepicker)return Promise.resolve(window.jalaliDatepicker);
    if(libraryPromise)return libraryPromise;
    libraryPromise=new Promise((resolve,reject)=>{
        let script=document.querySelector('script[src*="jalalidatepicker.min.js"]');
        const ready=()=>window.jalaliDatepicker?resolve(window.jalaliDatepicker):reject(new Error('Jalali date picker did not initialize'));
        if(script){script.addEventListener('load',ready,{once:true});script.addEventListener('error',reject,{once:true});setTimeout(()=>{if(window.jalaliDatepicker)return resolve(window.jalaliDatepicker);const fallback=document.createElement('script');fallback.src=libraryUrl+'?v=fallback';fallback.onload=ready;fallback.onerror=reject;document.head.appendChild(fallback);},750);return;}
        script=document.createElement('script');script.src=libraryUrl+'?v=local';script.onload=ready;script.onerror=reject;document.head.appendChild(script);
    });
    return libraryPromise;
}
function latin(value){return String(value||'').replace(/[۰-۹]/g,d=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(d)).replace(/[٠-٩]/g,d=>'٠١٢٣٤٥٦٧٨٩'.indexOf(d));}
function jalali(iso){if(!/^\d{4}-\d{2}-\d{2}$/.test(iso||''))return'';const parts=new Intl.DateTimeFormat('fa-IR-u-ca-persian',{year:'numeric',month:'2-digit',day:'2-digit'}).formatToParts(new Date(iso+'T12:00:00'));const get=t=>latin(parts.find(p=>p.type===t)?.value||'');return get('year')+'/'+get('month')+'/'+get('day');}
window.formatLocalizedDate=function(iso){if(!iso)return'';if(!fa)return iso;return new Intl.DateTimeFormat('fa-IR-u-ca-persian',{year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date(iso+'T12:00:00'));};
function putPickerOnTop(){
    const picker=document.querySelector('jdp-container'),overlay=document.querySelector('jdp-overlay');
    if(!picker)return;
    picker.style.zIndex='2147483647';
    picker.style.visibility='visible';
    picker.style.display='block';
    if(overlay)overlay.style.zIndex='2147483646';
}
function openCalendar(input){
    if(!input)return;
    if(window.jalaliDatepicker){
        startPicker();
        window.jalaliDatepicker.show(input);
        putPickerOnTop();
        requestAnimationFrame(putPickerOnTop);
        return;
    }
    pendingInput=input;
    ensureLibrary().then(()=>{
        startPicker();
        if(pendingInput)openCalendar(pendingInput);
    }).catch(error=>console.error('Jalali date picker:',error));
}
window.openLocalizedDatePicker=openCalendar;
window.initLocalizedDateInputs=function(root){if(!fa)return;root=root||document;const selector='input[type="date"]:not([data-localized-ready])',inputs=[];if(root.matches?.(selector))inputs.push(root);root.querySelectorAll?.(selector).forEach(input=>inputs.push(input));inputs.forEach(function(input){if(!input.id)input.id='localizedDate'+Math.random().toString(36).slice(2);input.dataset.localizedReady='1';const wrapper=document.createElement('div');wrapper.className='localized-date-field';const visible=document.createElement('input');visible.type='text';visible.value=jalali(input.value);visible.placeholder='۱۴۰۵/۰۱/۰۱';visible.className=input.className;visible.setAttribute('data-jdp','');visible.setAttribute('data-jdp-init-date',visible.value||'');visible.setAttribute('data-jdp-target-value-input','#'+CSS.escape(input.id));visible.setAttribute('data-jdp-target-value-type','gregorian');visible.setAttribute('autocomplete','off');visible.setAttribute('aria-label',input.getAttribute('aria-label')||input.getAttribute('title')||'انتخاب تاریخ');const button=document.createElement('button');button.type='button';button.className='localized-date-trigger';button.setAttribute('aria-label','باز کردن تقویم');button.setAttribute('title','انتخاب تاریخ');button.innerHTML='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2v3M17 2v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm2 9h2v2H7v-2Zm4 0h2v2h-2v-2Zm4 0h2v2h-2v-2Zm-8 4h2v2H7v-2Zm4 0h2v2h-2v-2Z"/></svg>';input.before(wrapper);input.type='hidden';wrapper.append(visible,button,input);button.addEventListener('click',event=>{event.preventDefault();event.stopPropagation();visible.focus();openCalendar(visible);});visible.addEventListener('change',()=>{input.dispatchEvent(new Event('change',{bubbles:true}));});});};
function startPicker(){if(started||!window.jalaliDatepicker)return;started=true;window.jalaliDatepicker.startWatch({selector:'input[data-jdp]',autoShow:true,persianDigits:true,targetValueInput:'attr',targetValueType:'attr'});document.addEventListener('pointerdown',function(event){const picker=document.querySelector('jdp-container');if(!picker||getComputedStyle(picker).display==='none')return;if(event.target.closest?.('jdp-container,input[data-jdp],.localized-date-trigger'))return;window.jalaliDatepicker.hide();},true);}
function boot(){if(!fa)return;window.initLocalizedDateInputs(document);ensureLibrary().then(startPicker).catch(error=>console.error('Jalali date picker:',error));}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
new MutationObserver(records=>records.forEach(r=>r.addedNodes.forEach(n=>{if(n.nodeType===1)window.initLocalizedDateInputs(n);}))).observe(document.documentElement,{childList:true,subtree:true});
})();
