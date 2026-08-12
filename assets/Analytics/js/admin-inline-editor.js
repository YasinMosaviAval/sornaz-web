(function () {
    const catalog = Array.isArray(window.adminInlineTranslations) ? window.adminInlineTranslations : [];
    const byKey = new Map(catalog.map(row => [row.key, row]));
    const byText = new Map();
    catalog.forEach(row => { if (row.fa) byText.set(normalize(row.fa), row); if (row.en) byText.set(normalize(row.en), row); });
    let editMode = false;

    function normalize(value) { return String(value || '').replace(/\s+/g, ' ').trim(); }
    function hash(value) { let h = 2166136261; for (let i=0;i<value.length;i++){h^=value.charCodeAt(i);h=Math.imul(h,16777619);} return (h>>>0).toString(36); }
    function eligible(node) {
        if (!node || !node.parentElement) return false;
        const el=node.parentElement;
        if (el.closest('[data-no-inline-edit],script,style,textarea,select,option,input,[contenteditable],#modalContainer,#appDialogHost')) return false;
        if (el.closest('tbody,[data-dynamic-content],#dashboardStats,#todayClassesList,#urgentItemsList,#recentPaymentsList,#recentDepositsList,#todayAbsencesList,#unreadMessagesList')) return false;
        const text=normalize(node.nodeValue);
        return text.length>0 && text.length<=500 && /[\p{L}]/u.test(text);
    }
    function mark(root=document) {
        const walker=document.createTreeWalker(root,NodeFilter.SHOW_TEXT);
        const nodes=[];while(walker.nextNode())if(eligible(walker.currentNode))nodes.push(walker.currentNode);
        nodes.forEach(node=>{const parent=node.parentElement;if(parent.dataset.inlineTranslationKey)return;const current=normalize(node.nodeValue);const row=byText.get(current);const prefix=location.pathname.startsWith('/analytics/admin-panel')?'admin':'site';const key=row?.key || `${prefix}.inline.${hash(current)}`;const marker=document.createElement('span');marker.dataset.inlineTranslationKey=key;marker.dataset.inlineOriginalText=current;if(row){marker.dataset.inlineKnown='1';marker.textContent=document.documentElement.lang==='en'?row.en:row.fa;}else marker.textContent=node.nodeValue;node.replaceWith(marker);});
        document.querySelectorAll('input[placeholder],textarea[placeholder]').forEach(el=>{if(el.closest('[data-no-inline-edit],#modalContainer'))return;const current=normalize(el.placeholder);if(!current)return;const row=byText.get(current);const prefix=location.pathname.startsWith('/analytics/admin-panel')?'admin':'site';el.dataset.inlineTranslationKey=row?.key||`${prefix}.inline.${hash(current)}`;el.dataset.inlineOriginalText=current;el.dataset.inlineAttribute='placeholder';if(row){el.dataset.inlineKnown='1';el.placeholder=document.documentElement.lang==='en'?row.en:row.fa;}});
    }
    function setMode(enabled) {
        editMode=Boolean(enabled);document.documentElement.classList.toggle('admin-inline-editing',editMode);
        document.querySelectorAll('[data-admin-edit-toggle]').forEach(input=>input.checked=editMode);
        const english=document.documentElement.lang==='en';
        document.querySelectorAll('[data-admin-edit-label]').forEach(el=>el.textContent=editMode?(english?'Edit':'ویرایش'):(english?'View':'نمایش'));
        if(editMode)mark();
    }
    function valueFor(el){return el.dataset.inlineAttribute==='placeholder'?el.placeholder:normalize(el.dataset.inlineOriginalText||el.textContent);}
    function openEditor(el){
        const key=el.dataset.inlineTranslationKey;const row=byKey.get(key)||{};const current=valueFor(el);const currentLocale=document.documentElement.lang==='en'?'en':'fa';
        const fa=row.fa|| (currentLocale==='fa'?current:'');const en=row.en|| (currentLocale==='en'?current:'');
        const host=document.getElementById('modalContainer');if(!host)return;
        host.innerHTML=`<div class="admin-inline-modal fixed inset-0 z-[100000] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" data-no-inline-edit onclick="if(event.target===this)AdminInlineEditor.close()"><section class="w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-6 shadow-2xl" role="dialog" aria-modal="true"><div class="mb-5 flex items-start justify-between gap-4"><div><h2 class="text-xl font-bold">ویرایش متن ثابت</h2><p class="mt-1 text-xs text-gray-500" dir="ltr">${escapeHtml(key)}</p></div><button type="button" onclick="AdminInlineEditor.close()" class="text-2xl text-gray-400">×</button></div><div class="space-y-4"><label class="block"><span class="mb-1.5 block text-sm font-medium">متن فارسی</span><textarea data-inline-fa rows="4" dir="rtl" class="w-full rounded-2xl border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500">${escapeHtml(fa)}</textarea></label><label class="block"><span class="mb-1.5 block text-sm font-medium">English text</span><textarea data-inline-en rows="4" dir="ltr" class="w-full rounded-2xl border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500">${escapeHtml(en)}</textarea></label></div><div class="mt-6 flex justify-end gap-3"><button type="button" onclick="AdminInlineEditor.close()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">انصراف</button><button type="button" onclick="AdminInlineEditor.save('${escapeJs(key)}')" class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-medium text-white">ذخیره ترجمه</button></div></section></div>`;
        host.dataset.inlineTargetKey=key;host.querySelector(currentLocale==='fa'?'[data-inline-fa]':'[data-inline-en]')?.focus();
    }
    async function save(key){
        const host=document.getElementById('modalContainer'),fa=host.querySelector('[data-inline-fa]')?.value.trim(),en=host.querySelector('[data-inline-en]')?.value.trim();
        if(!fa||!en){AppDialog.alert('متن فارسی و انگلیسی هر دو الزامی هستند.',{type:'error'});return;}
        const body=new FormData();body.append('_token',window.adminCsrfToken||'');body.append('key',key);body.append('fa',fa);body.append('en',en);
        try{const response=await fetch('/analytics/admin-inline-translations/save',{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body});const envelope=await response.json();const result=envelope.data||envelope;if(!response.ok||result.success===false)throw new Error(result.message||'ذخیره ترجمه ناموفق بود.');const row={key,fa,en};byKey.set(key,row);byText.set(normalize(fa),row);byText.set(normalize(en),row);document.querySelectorAll(`[data-inline-translation-key="${CSS.escape(key)}"]`).forEach(el=>{const value=document.documentElement.lang==='en'?en:fa;if(el.dataset.inlineAttribute==='placeholder')el.placeholder=value;else{el.textContent=value;el.dataset.inlineOriginalText=value;}});close();AppDialog.alert('✅ ترجمه ذخیره شد.');}catch(error){AppDialog.alert(error.message||'ذخیره ترجمه ناموفق بود.',{type:'error'});}
    }
    function close(){const host=document.getElementById('modalContainer');if(host){host.innerHTML='';delete host.dataset.inlineTargetKey;}}
    function escapeHtml(value){const d=document.createElement('div');d.textContent=String(value||'');return d.innerHTML;}
    function escapeJs(value){return String(value).replace(/\\/g,'\\\\').replace(/'/g,"\\'");}
    document.addEventListener('click',event=>{if(!editMode)return;const el=event.target.closest('[data-inline-translation-key]');if(!el||el.closest('[data-no-inline-edit],#modalContainer'))return;event.preventDefault();event.stopImmediatePropagation();openEditor(el);},true);
    async function loadCatalog(){if(catalog.length)return;try{const response=await fetch('/analytics/admin-inline-translations',{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});if(!response.ok)return;const envelope=await response.json(),result=envelope.data||envelope;for(const row of result.translations||[]){byKey.set(row.key,row);if(row.fa)byText.set(normalize(row.fa),row);if(row.en)byText.set(normalize(row.en),row);}}catch(_){} }
    document.addEventListener('DOMContentLoaded',async()=>{setMode(false);await loadCatalog();mark();new MutationObserver(records=>{records.forEach(record=>record.addedNodes.forEach(node=>{if(node.nodeType===1)mark(node);}));}).observe(document.getElementById('mainContent')||document.querySelector('main')||document.body,{childList:true,subtree:true});});
    window.SiteInlineEditor=window.AdminInlineEditor={setMode,save,close};
})();
