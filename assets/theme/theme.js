(function () {
    let digitStyle=document.documentElement.lang==='en'?'en':'fa';
    const faDigits='۰۱۲۳۴۵۶۷۸۹', arDigits='٠١٢٣٤٥٦٧٨٩', enDigits='0123456789';
    const convertDigits=(value,style)=>String(value).replace(/[0-9۰-۹٠-٩]/g,char=>{let index=enDigits.indexOf(char);if(index<0)index=faDigits.indexOf(char);if(index<0)index=arDigits.indexOf(char);return style==='fa'?faDigits[index]:enDigits[index];});
    const normalizeNode=node=>{
        if(node.nodeType===Node.TEXT_NODE){const parent=node.parentElement;if(!parent||parent.closest('script,style,code,pre,textarea,[contenteditable="true"],[data-preserve-digits]'))return;const next=convertDigits(node.nodeValue,digitStyle);if(next!==node.nodeValue)node.nodeValue=next;return;}
        if(node.nodeType!==Node.ELEMENT_NODE)return;
        const walker=document.createTreeWalker(node,NodeFilter.SHOW_TEXT);let text;while((text=walker.nextNode()))normalizeNode(text);
    };
    window.applySiteTypography=settings=>{
        if(settings.fontFamily)document.documentElement.style.setProperty('--site-font-family',settings.fontFamily);
        if(settings.rootFontSize)document.documentElement.style.setProperty('--site-root-font-size',settings.rootFontSize);
        digitStyle=document.documentElement.lang==='en'?'en':'fa';
        if(document.body)normalizeNode(document.body);
    };
    const observer=new MutationObserver(records=>records.forEach(record=>{if(record.type==='characterData')normalizeNode(record.target);else record.addedNodes.forEach(normalizeNode);}));
    const observe=()=>observer.observe(document.body,{childList:true,subtree:true,characterData:true});
    if(document.body)observe();else document.addEventListener('DOMContentLoaded',observe);
    const encode=data=>btoa(unescape(encodeURIComponent(JSON.stringify(data)))).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');
    const persist=data=>{if(!window.adminCsrfToken)return Promise.resolve();return fetch('/analytics/admin-settings',{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':window.adminCsrfToken},body:new URLSearchParams({_token:window.adminCsrfToken,payload_b64:encode(data)}),keepalive:true}).catch(()=>{});};
    const root = document.documentElement;
    const themes = ['indigo', 'emerald', 'rose', 'amber'];
    const apply = (theme, mode, shouldPersist = false) => {
        const safeTheme = themes.includes(theme) ? theme : 'indigo';
        const safeMode = ['light', 'dark'].includes(mode) ? mode : 'light';
        root.dataset.theme = safeTheme;
        root.dataset.mode = safeMode;
        localStorage.setItem('sornaz.theme', safeTheme);
        localStorage.setItem('sornaz.mode', safeMode);
        document.querySelectorAll('[data-theme-select]').forEach(el => { el.value = safeTheme; });
        document.querySelectorAll('[data-theme-mode]').forEach(button => {
            button.setAttribute('aria-pressed', String(safeMode === 'dark'));
            button.querySelector('[data-light-icon]')?.classList.toggle('hidden', safeMode === 'dark');
            button.querySelector('[data-dark-icon]')?.classList.toggle('hidden', safeMode !== 'dark');
        });
        if(shouldPersist)persist({colorTheme:safeTheme,themeMode:safeMode});
    };
    window.setSiteTheme = theme => apply(theme, root.dataset.mode || 'light', true);
    window.toggleSiteThemeMode = () => apply(root.dataset.theme || 'indigo', root.dataset.mode === 'dark' ? 'light' : 'dark', true);
    window.changeSiteLanguage = async locale => {
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            sessionStorage.setItem('sornaz.reopenMobileMenu', '1');
        }
        document.body.classList.add('language-changing');
        await persist({language:locale});
        window.location.href = `/language/${locale}`;
    };
    fetch('/analytics/site-settings', { headers: { Accept: 'application/json' } }).then(r => r.json()).then(p => { const b=p.data??p,d=b.data??b;window.applySiteTypography(d);apply(d.colorTheme,d.themeMode,false); }).catch(() => {});
    document.addEventListener('DOMContentLoaded', () => {
        apply(root.dataset.theme || 'indigo', root.dataset.mode || 'light');
        if (sessionStorage.getItem('sornaz.reopenMobileMenu') === '1') {
            sessionStorage.removeItem('sornaz.reopenMobileMenu');
            document.getElementById('mobileMenu')?.classList.remove('hidden');
            const icon = document.getElementById('mobileMenuIcon');
            if (icon) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); }
        }
    });
})();
