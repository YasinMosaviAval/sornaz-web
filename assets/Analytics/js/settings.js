(function(){
    'use strict';
    let settings=null;
    const encode=data=>btoa(unescape(encodeURIComponent(JSON.stringify(data)))).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');

    async function load(){
        const select=document.getElementById('sitePrimaryFont');
        try{
            const response=await fetch('/analytics/site-settings',{headers:{Accept:'application/json'}});
            const payload=await response.json(),body=payload.data??payload;
            if(!response.ok||body.success===false)throw new Error(body.message||'دریافت فهرست فونت‌ها ناموفق بود.');
            settings=body.data??body;
            if(!select)return;
            select.innerHTML=settings.fonts.map(font=>`<option value="${font.value}" ${font.value===settings.primaryFont?'selected':''}>${font.label}</option>`).join('');
            document.getElementById('siteFontScale').value=Number(settings.fontScale||0);
            previewSiteFont();
        }catch(error){
            if(select)select.innerHTML='<option value="">خطا در دریافت فونت‌ها</option>';
            console.error(error);
        }
    }

    window.previewSiteFont=()=>{
        if(!settings)return;
        const key=document.getElementById('sitePrimaryFont').value;
        const font=settings.fonts.find(item=>item.value===key);
        const scale=Number(document.getElementById('siteFontScale').value||0);
        const output=document.getElementById('siteFontScaleOutput');
        const persian=document.documentElement.lang!=='en',digits=value=>persian?String(value).replace(/\d/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]):String(value);
        const signed=value=>{if(value===0)return digits(0);const number=digits(Math.abs(value)),sign=value>0?'+':'-';return `<span>${sign}</span><span>${number}</span>`;};
        output.innerHTML=signed(scale);
        const marks=document.getElementById('siteFontScaleMarks');
        marks.innerHTML=[-2,-1,0,1,2].map(value=>`<span class="inline-flex min-w-5 justify-center" dir="ltr">${signed(value)}</span>`).join('');
        const preview=document.getElementById('siteFontPreview');
        preview.dataset.preserveDigits='1';
        preview.style.fontFamily=font?.fontFamily||settings.fontFamily;
        preview.style.fontSize=(16+scale)+'px';
        const english=document.documentElement.lang==='en',content=document.getElementById('siteFontPreviewContent');
        content.innerHTML=english?'<p>Music is the common language of our emotions.</p><p class="mt-1" dir="ltr">Today: 2026/08/15 — 1234567890</p>':'<p>موسیقی زبان مشترک احساس‌های ماست.</p><p class="mt-1">امروز ۱۴۰۵/۰۵/۲۴ — ۱۲۳۴۵۶۷۸۹۰</p>';
    };

    window.saveSiteSettings=async()=>{
        const primaryFont=document.getElementById('sitePrimaryFont').value;
        const fontScale=Number(document.getElementById('siteFontScale').value||0);
        try{
            const response=await fetch('/analytics/admin-settings',{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':window.adminCsrfToken||''},body:new URLSearchParams({_token:window.adminCsrfToken||'',payload_b64:encode({primaryFont,fontScale,language:document.documentElement.lang==='en'?'en':'fa'})})});
            const payload=await response.json(),body=payload.data??payload;
            if(!response.ok||body.success===false)throw new Error(body.message||'ذخیره تنظیمات ناموفق بود.');
            settings=body.data??body;
            if(typeof window.applySiteTypography==='function')window.applySiteTypography(settings);
            else{document.documentElement.style.setProperty('--site-font-family',settings.fontFamily);document.documentElement.style.setProperty('--site-root-font-size',settings.rootFontSize);}
            document.getElementById('siteSettingsMessage').textContent='تنظیمات ذخیره و روی سایت اعمال شد.';
        }catch(error){alert(error.message);}
    };
    setTimeout(load,250);
})();
