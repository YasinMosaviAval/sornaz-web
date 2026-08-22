(function () {
'use strict';
// ==================== حساب کاربری آموزشگاه ====================

let academyProfile = {privacy:{}};
let academyDocuments = [];
let academyDevices = [];
let academyLoginHistory = [];
let academySecurityAlerts = [];

let lastBackupMeta = null;
let pendingAccountDocuments = [];

function accountPayload(data){const json=JSON.stringify(data||{}),bytes=new TextEncoder().encode(json);let binary='';bytes.forEach(b=>binary+=String.fromCharCode(b));return btoa(binary).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');}
async function accountRequest(url,data,method='POST') {const options={method,credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':window.adminCsrfToken||''}};if(data!==undefined){const body=new URLSearchParams();body.set('_token',window.adminCsrfToken||'');body.set('payload_b64',accountPayload(data));options.body=body;}const response=await fetch(url,options),payload=await response.json(),body=payload.data??payload;if(!response.ok||body.success===false)throw new Error(body.message||'انجام عملیات ناموفق بود.');return body.data??body;}
async function loadAccountData(){try{const data=await accountRequest('/analytics/admin-account',undefined,'GET');academyProfile=data.profile||{privacy:{}};academyDocuments=data.documents||[];academyDevices=data.devices||[];academyLoginHistory=data.loginHistory||[];academySecurityAlerts=data.securityAlerts||[];lastBackupMeta=data.backup||null;window.renderAccountInfo();}catch(error){console.error(error);alert(error.message);}}
async function uploadAccountFile(kind,blob,name,meta={}){const form=new FormData();form.set('_token',window.adminCsrfToken||'');form.set('file',blob,name);Object.entries(meta).forEach(([k,v])=>form.set(k,v??''));const response=await fetch('/analytics/admin-account/media/'+kind,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':window.adminCsrfToken||''},body:form}),payload=await response.json(),body=payload.data??payload;if(!response.ok||body.success===false)throw new Error(body.message||'آپلود فایل ناموفق بود.');return body.data??body;}
window.reloadAdminAccount=loadAccountData;
window.addEventListener('admin-media-changed',event=>{if(event.detail?.source!=='account')loadAccountData();});

window.renderAccountInfo = async function () {
    const container = document.getElementById('accountInfo');
    if (!container) return;
    if (window.getAccountInfoHTML) {
        container.innerHTML = window.getAccountInfoHTML(academyProfile);
    }
    const nameEl = document.getElementById('academyName');
    if (nameEl) nameEl.textContent = academyProfile.name;
    const pageTitle = document.getElementById('accountPageTitle');
    if (pageTitle) pageTitle.textContent = 'حساب کاربری ' + (academyProfile.entityLabel || '');
    const coverLabel = document.getElementById('accountCoverLabel');
    if (coverLabel) coverLabel.textContent = 'کاور پروفایل ' + (academyProfile.entityLabel || 'حساب');
    const typeEl = document.getElementById('academyTypeLabel');
    if (typeEl) typeEl.textContent = academyProfile.type;
    const shortEl = document.getElementById('academyShortIntro');
    if (shortEl) shortEl.textContent = academyProfile.shortIntro || '—';
    const shortText = document.getElementById('accountShortIntroText');
    if (shortText) shortText.textContent = academyProfile.shortIntro || '—';
    const bioText = document.getElementById('accountBioText');
    if (bioText) bioText.textContent = academyProfile.biography || '—';

    const emailInput = document.getElementById('accountEmail');
    if (emailInput) emailInput.value = academyProfile.email || '';
    const phoneInput = document.getElementById('accountPhone');
    if (phoneInput) phoneInput.value = academyProfile.phone || '';

    // avatar
    const img = document.getElementById('accountAvatarImg');
    const icon = document.getElementById('accountAvatarIcon');
    if (img && icon) {
        if (academyProfile.avatarUrl) {
            img.src = academyProfile.avatarUrl;
            img.classList.remove('hidden');
            icon.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            icon.classList.remove('hidden');
        }
    }

    // cover
    window.renderAccountCover();

    window.renderAccountDocuments();
    window.renderAccountDevices();
    window.renderAccountLoginHistory();
    window.renderAccountSecurityAlerts();
    window.renderAccountPrivacy();
    window.renderAccountBackupStatus();
    window.loadAccountInviteLink();
};

let accountInviteData=null;
window.loadAccountInviteLink=async function(){const input=document.getElementById('accountInviteUrl');if(!input)return;try{const response=await fetch('/system/my-invite',{credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}}),payload=await response.json(),body=payload.data??payload,data=body.data??body;if(!response.ok||body.success===false)throw new Error(body.message||'دریافت لینک دعوت ناموفق بود.');accountInviteData=data;input.value=data.url;document.getElementById('accountInviteCode').textContent=data.code;document.getElementById('accountInviteCount').textContent=Number(data.invitedUsers||0)+' کاربر دعوت‌شده';}catch(error){input.value='دریافت لینک دعوت ناموفق بود';console.error(error);}};
window.copyAccountInviteLink=async function(){if(!accountInviteData)return;try{await navigator.clipboard.writeText(accountInviteData.url);alert('✅ لینک دعوت کپی شد.');}catch(_){const input=document.getElementById('accountInviteUrl');input.select();document.execCommand('copy');alert('✅ لینک دعوت کپی شد.');}};
window.shareAccountInviteLink=async function(){if(!accountInviteData)return;if(navigator.share)try{return await navigator.share({title:'دعوت به سرناز',text:'از طریق لینک دعوت من در سرناز ثبت‌نام کنید.',url:accountInviteData.url});}catch(error){if(error.name==='AbortError')return;}return window.copyAccountInviteLink();};

window.renderAccountCover = async function () {
    const coverImg = document.getElementById('accountCoverImg');
    const placeholder = document.getElementById('accountCoverPlaceholder');
    const removeBtn = document.getElementById('accountCoverRemoveBtn');
    if (!coverImg) return;
    if (academyProfile.coverUrl) {
        coverImg.src = academyProfile.coverUrl;
        coverImg.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
        if (removeBtn) removeBtn.classList.remove('hidden');
    } else {
        coverImg.src = '';
        coverImg.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
        if (removeBtn) removeBtn.classList.add('hidden');
    }
};

window.removeAccountCover = async function () {
    if (!academyProfile.coverUrl) return;
    if (!(await AppDialog.confirm('حذف کاور پروفایل؟'))) return;
    try{await accountRequest('/analytics/admin-account/media/'+academyProfile.coverId+'/delete',{});await loadAccountData();window.dispatchEvent(new CustomEvent('admin-media-changed',{detail:{source:'account'}}));alert('✅ کاور حذف شد');}catch(error){alert(error.message);}
};

// ---------- کراپ تصویر (آواتار ۱:۱ دایره‌ای / کاور ۱۶:۹) ----------
let cropState = null;
let pendingAccountCroppedMedia = null;

window.onAccountAvatarChange = async function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (event.target) event.target.value = '';
    if (!file) return;
    if (!file.type.startsWith('image/')) return alert('فقط فایل تصویری مجاز است.');
    openImageCropModal(file, 'avatar');
};

window.onAccountCoverChange = async function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (event.target) event.target.value = '';
    if (!file) return;
    if (!file.type.startsWith('image/')) return alert('فقط فایل تصویری مجاز است.');
    openImageCropModal(file, 'cover');
};

function openImageCropModal(file, mode, options={}) {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const aspect = mode === 'avatar' ? 1 : (mode === 'gallery' ? img.width / img.height : 16 / 9);
            const title = mode === 'avatar' ? 'ویرایش عکس (۱×۱)' : (mode === 'gallery' ? 'ویرایش تصویر گالری' : 'ویرایش کاور (۱۶×۹)');
            document.getElementById('modalContainer').innerHTML = window.getAccountCropModalHTML
                ? window.getAccountCropModalHTML(mode, title, options.meta||{title:(mode==='avatar'?'آواتار ':'کاور ')+(academyProfile.name||'حساب'),summary:'',description:''}) : '';
            const canvas = document.getElementById('accountCropCanvas');
            if (!canvas) return;
            cropState = {
                mode: mode,
                img: img,
                aspect: aspect,
                // مختصات کادر کراپ روی تصویر اصلی
                cx: 0,
                cy: 0,
                cw: 0,
                ch: 0,
                scale: 1,
                offsetX: 0,
                offsetY: 0,
                dragging: false,
                lastX: 0,
                lastY: 0,
                onApply: options.onApply,
                onCancel: options.onCancel,
                meta: options.meta || {}
            };
            initCropFrame();
            drawCropCanvas();
            bindCropEvents(canvas);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

window.openAdminImageEditor=function(file,mode,options={}){openImageCropModal(file,mode,options);};

function initCropFrame() {
    const s = cropState;
    const img = s.img;
    // بزرگ‌ترین کادر با نسبت aspect داخل تصویر
    if (img.width / img.height > s.aspect) {
        s.ch = img.height;
        s.cw = img.height * s.aspect;
        s.cx = (img.width - s.cw) / 2;
        s.cy = 0;
    } else {
        s.cw = img.width;
        s.ch = img.width / s.aspect;
        s.cx = 0;
        s.cy = (img.height - s.ch) / 2;
    }
    s.minCrop = Math.min(img.width, img.height) * 0.15;
}

function drawCropCanvas() {
    const s = cropState;
    if (!s) return;
    const canvas = document.getElementById('accountCropCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const maxW = Math.min(720, window.innerWidth - 64);
    const maxH = Math.min(420, window.innerHeight * 0.5);
    const scale = Math.min(maxW / s.img.width, maxH / s.img.height, 1);
    s.viewScale = scale;
    canvas.width = Math.round(s.img.width * scale);
    canvas.height = Math.round(s.img.height * scale);

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(s.img, 0, 0, canvas.width, canvas.height);

    // لایه تیره
    ctx.fillStyle = 'rgba(0,0,0,0.55)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const rx = s.cx * scale;
    const ry = s.cy * scale;
    const rw = s.cw * scale;
    const rh = s.ch * scale;

    // ناحیه روشن کراپ
    ctx.save();
    if (s.mode === 'avatar') {
        ctx.beginPath();
        ctx.arc(rx + rw / 2, ry + rh / 2, rw / 2, 0, Math.PI * 2);
        ctx.clip();
    } else {
        ctx.beginPath();
        ctx.rect(rx, ry, rw, rh);
        ctx.clip();
    }
    ctx.drawImage(s.img, 0, 0, canvas.width, canvas.height);
    ctx.restore();

    // حاشیه کادر
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    if (s.mode === 'avatar') {
        ctx.beginPath();
        ctx.arc(rx + rw / 2, ry + rh / 2, rw / 2, 0, Math.PI * 2);
        ctx.stroke();
    } else {
        ctx.strokeRect(rx, ry, rw, rh);
    }

    // دستگیره‌های گوشه (فقط برای نمایش)
    const hs = 6;
    ctx.fillStyle = '#6366f1';
    [[rx, ry], [rx + rw, ry], [rx, ry + rh], [rx + rw, ry + rh]].forEach(function (p) {
        ctx.fillRect(p[0] - hs / 2, p[1] - hs / 2, hs, hs);
    });

    const info = document.getElementById('accountCropInfo');
    if (info) {
        info.textContent = Math.round(s.cw) + ' × ' + Math.round(s.ch) + ' px · نسبت ' +
            (s.mode === 'avatar' ? '۱:۱' : '۱۶:۹');
    }
}

function bindCropEvents(canvas) {
    canvas.onmousedown = function (e) {
        if (!cropState) return;
        cropState.dragging = true;
        cropState.lastX = e.clientX;
        cropState.lastY = e.clientY;
    };
    window.onmousemove = async function (e) {
        if (!cropState || !cropState.dragging) return;
        const dx = (e.clientX - cropState.lastX) / cropState.viewScale;
        const dy = (e.clientY - cropState.lastY) / cropState.viewScale;
        cropState.lastX = e.clientX;
        cropState.lastY = e.clientY;
        cropState.cx = clamp(cropState.cx + dx, 0, cropState.img.width - cropState.cw);
        cropState.cy = clamp(cropState.cy + dy, 0, cropState.img.height - cropState.ch);
        drawCropCanvas();
    };
    window.onmouseup = async function () {
        if (cropState) cropState.dragging = false;
    };
    canvas.ontouchstart = function (e) {
        if (!cropState || !e.touches[0]) return;
        cropState.dragging = true;
        cropState.lastX = e.touches[0].clientX;
        cropState.lastY = e.touches[0].clientY;
        e.preventDefault();
    };
    canvas.ontouchmove = function (e) {
        if (!cropState || !cropState.dragging || !e.touches[0]) return;
        const dx = (e.touches[0].clientX - cropState.lastX) / cropState.viewScale;
        const dy = (e.touches[0].clientY - cropState.lastY) / cropState.viewScale;
        cropState.lastX = e.touches[0].clientX;
        cropState.lastY = e.touches[0].clientY;
        cropState.cx = clamp(cropState.cx + dx, 0, cropState.img.width - cropState.cw);
        cropState.cy = clamp(cropState.cy + dy, 0, cropState.img.height - cropState.ch);
        drawCropCanvas();
        e.preventDefault();
    };
    canvas.ontouchend = function () {
        if (cropState) cropState.dragging = false;
    };
}

function clamp(v, min, max) {
    return Math.max(min, Math.min(max, v));
}

window.zoomCrop = async function (delta) {
    const s = cropState;
    if (!s) return;
    const factor = delta > 0 ? 0.92 : 1.08; // + زوم این (کادر کوچکتر) / - زوم اوت
    let newW = s.cw * factor;
    let newH = newW / s.aspect;

    // محدودیت اندازه
    const maxW = s.img.width;
    const maxH = s.img.height;
    if (newW > maxW) { newW = maxW; newH = newW / s.aspect; }
    if (newH > maxH) { newH = maxH; newW = newH * s.aspect; }
    const minSide = s.minCrop;
    if (s.mode === 'avatar') {
        if (newW < minSide) { newW = minSide; newH = minSide; }
    } else {
        if (newW < minSide * s.aspect) { newW = minSide * s.aspect; newH = minSide; }
        if (newH < minSide) { newH = minSide; newW = newH * s.aspect; }
    }

    // مرکز کادر حفظ شود
    const centerX = s.cx + s.cw / 2;
    const centerY = s.cy + s.ch / 2;
    s.cw = newW;
    s.ch = newH;
    s.cx = clamp(centerX - s.cw / 2, 0, s.img.width - s.cw);
    s.cy = clamp(centerY - s.ch / 2, 0, s.img.height - s.ch);
    drawCropCanvas();
};

window.applyImageCrop = async function () {
    const s = cropState;
    if (!s) return;
    const out = document.createElement('canvas');
    let outW, outH;
    if (s.mode === 'avatar') {
        outW = outH = 512;
    } else if(s.mode === 'gallery') {
        outW=Math.min(1920,Math.round(s.cw));outH=Math.max(1,Math.round(outW/s.aspect));
    } else {
        outW = 1920;
        outH = 1080;
    }
    out.width = outW;
    out.height = outH;
    const ctx = out.getContext('2d');
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(s.img, s.cx, s.cy, s.cw, s.ch, 0, 0, outW, outH);

    out.toBlob(async function(blob){if(!blob)return alert('ساخت تصویر ناموفق بود.');try{if(s.onApply){const callback=s.onApply;cropState=null;await callback(blob);return;}const mode=s.mode;cropState=null;pendingAccountCroppedMedia={blob,mode};const meta={title:(mode==='avatar'?'آواتار ':'کاور ')+(academyProfile.name||'حساب'),summary:'',description:''};document.getElementById('modalContainer').innerHTML=window.getAccountMediaMetaModalHTML(mode,meta);}catch(error){alert(error.message);}},'image/jpeg',0.92);
};

window.saveAccountCroppedMedia=async function(){const pending=pendingAccountCroppedMedia;if(!pending)return;const meta={title:document.getElementById('accountMediaTitle')?.value.trim()||'',summary:document.getElementById('accountMediaSummary')?.value.trim()||'',description:document.getElementById('accountMediaDescription')?.value.trim()||''};if(!meta.title)return alert('عنوان الزامی است.');try{const mode=pending.mode;await uploadAccountFile(mode,pending.blob,mode+'.jpg',meta);pendingAccountCroppedMedia=null;closeModal();await loadAccountData();window.dispatchEvent(new CustomEvent('admin-media-changed',{detail:{source:'account'}}));alert(mode==='avatar'?'✅ عکس پروفایل ذخیره شد':'✅ کاور ذخیره شد');}catch(error){alert(error.message);}};
window.cancelAccountCroppedMedia=function(){pendingAccountCroppedMedia=null;closeModal();};

window.cancelImageCrop = async function () {
    const callback=cropState?.onCancel;cropState = null;if(callback)return callback();closeModal();
};

window.openEditProfileModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getAccountEditProfileModalHTML
        ? window.getAccountEditProfileModalHTML(academyProfile) : '';
};

window.saveProfile = async function () {
    const name = (document.getElementById('editAcademyName') && document.getElementById('editAcademyName').value || '').trim();
    const manager = (document.getElementById('editManager') && document.getElementById('editManager').value || '').trim();
    const address = (document.getElementById('editAddress') && document.getElementById('editAddress').value || '').trim();
    const phone = (document.getElementById('editProfilePhone') && document.getElementById('editProfilePhone').value || '').trim();
    const email = (document.getElementById('editProfileEmail') && document.getElementById('editProfileEmail').value || '').trim();
    const founded = (document.getElementById('editFounded') && document.getElementById('editFounded').value || '').trim();
    try{await accountRequest('/analytics/admin-account/profile',{name,address,phone,email,founded});closeModal();await loadAccountData();alert('✅ پروفایل به‌روزرسانی شد');}catch(error){alert(error.message);}
};

window.openEditBioModal = async function () {
    if (!document.getElementById('modalContainer')) return;
    document.getElementById('modalContainer').innerHTML = window.getAccountEditBioModalHTML
        ? window.getAccountEditBioModalHTML(academyProfile) : '';
};

window.saveBio = async function () {
    const shortIntro=(document.getElementById('editShortIntro')?.value||'').trim(),biography=(document.getElementById('editBiography')?.value||'').trim();try{await accountRequest('/analytics/admin-account/bio',{shortIntro,biography});closeModal();await loadAccountData();alert('✅ معرفی و بیوگرافی ذخیره شد');}catch(error){alert(error.message);}
};

window.renderAccountDocuments = async function () {
    const el = document.getElementById('accountDocumentsList');
    if (!el) return;
    el.innerHTML = window.getAccountDocumentsHTML ? window.getAccountDocumentsHTML(academyDocuments) : '';
};

window.onAccountDocumentUpload = async function (event) {
    const files = event.target && event.target.files;
    if (!files || !files.length) return;
    pendingAccountDocuments=Array.from(files);event.target.value='';
    if(document.getElementById('modalContainer')&&window.getAccountDocumentModalHTML)document.getElementById('modalContainer').innerHTML=window.getAccountDocumentModalHTML(pendingAccountDocuments);
};

window.saveAccountDocuments=async function(){if(!pendingAccountDocuments.length)return;const meta={documentType:document.getElementById('accountDocumentType')?.value||'other',documentNumber:document.getElementById('accountDocumentNumber')?.value||'',issuedAt:document.getElementById('accountDocumentIssuedAt')?.value||'',expiresAt:document.getElementById('accountDocumentExpiresAt')?.value||''};try{for(const file of pendingAccountDocuments)await uploadAccountFile('document',file,file.name,meta);pendingAccountDocuments=[];closeModal();await loadAccountData();alert('✅ سند(ها) اضافه شد');}catch(error){alert(error.message);}};

window.deleteAccountDocument = async function (id) {
    if (!(await AppDialog.confirmDelete(academyDocuments, id, 'سند'))) return;
    try{await accountRequest('/analytics/admin-account/media/'+id+'/delete',{});await loadAccountData();}catch(error){alert(error.message);}
};

window.renderAccountDevices = async function () {
    const el = document.getElementById('accountDevicesList');
    if (!el) return;
    el.innerHTML = window.getAccountDevicesHTML ? window.getAccountDevicesHTML(academyDevices) : '';
};

window.revokeAccountDevice = async function (id) {
    const device = academyDevices.find(function (d) { return d.id === id; });
    if (!(await AppDialog.confirm(`خروج دستگاه "${device?.name || device?.title || 'دستگاه #' + id}" از حساب؟`))) return;
    try{await accountRequest('/analytics/admin-account/sessions/'+id+'/end',{});await loadAccountData();alert('✅ نشست دستگاه پایان یافت');}catch(error){alert(error.message);}
};

window.revokeAllOtherDevices = async function () {
    if (!(await AppDialog.confirm('خروج از همه دستگاه‌ها به‌جز دستگاه فعلی؟'))) return;
    try{for(const d of academyDevices.filter(x=>!x.current&&x.status!=='ended'))await accountRequest('/analytics/admin-account/sessions/'+d.id+'/end',{});await loadAccountData();alert('✅ سایر نشست‌ها پایان یافتند');}catch(error){alert(error.message);}
};

window.renderAccountLoginHistory = async function () {
    const el = document.getElementById('accountLoginHistory');
    if (!el) return;
    el.innerHTML = window.getAccountLoginHistoryHTML ? window.getAccountLoginHistoryHTML(academyLoginHistory) : '';
};

window.renderAccountSecurityAlerts = async function () {
    const el = document.getElementById('accountSecurityAlerts');
    if (!el) return;
    el.innerHTML = window.getAccountSecurityAlertsHTML ? window.getAccountSecurityAlertsHTML(academySecurityAlerts) : '';
};

window.renderAccountPrivacy = async function () {
    const el = document.getElementById('accountPrivacyOptions');
    if (!el) return;
    el.innerHTML = window.getAccountPrivacyHTML ? window.getAccountPrivacyHTML(academyProfile.privacy) : '';
};

window.savePrivacySettings = async function () {
    const p = academyProfile.privacy;
    p.showPublicProfile = !!(document.getElementById('privacyShowPublic') && document.getElementById('privacyShowPublic').checked);
    p.showBranches = !!(document.getElementById('privacyShowBranches') && document.getElementById('privacyShowBranches').checked);
    p.showTeachers = !!(document.getElementById('privacyShowTeachers') && document.getElementById('privacyShowTeachers').checked);
    p.showContact = !!(document.getElementById('privacyShowContact') && document.getElementById('privacyShowContact').checked);
    p.showStats = !!(document.getElementById('privacyShowStats') && document.getElementById('privacyShowStats').checked);
    p.indexable = !!(document.getElementById('privacyIndexable') && document.getElementById('privacyIndexable').checked);
    try{await accountRequest('/analytics/admin-account/privacy',p);await loadAccountData();alert('✅ تنظیمات حریم خصوصی ذخیره شد');}catch(error){alert(error.message);}
};

window.saveAccountSettings = async function () {
    const email = document.getElementById('accountEmail') && document.getElementById('accountEmail').value;
    const phone = document.getElementById('accountPhone') && document.getElementById('accountPhone').value;
    const pass = document.getElementById('accountPassword') && document.getElementById('accountPassword').value;
    const pass2 = document.getElementById('accountPasswordConfirm') && document.getElementById('accountPasswordConfirm').value;
    if (pass || pass2) {
        if (pass !== pass2) return alert('تکرار رمز عبور مطابقت ندارد.');
        if (pass.length < 8) return alert('رمز عبور باید حداقل ۸ کاراکتر باشد.');
    }
    try{await accountRequest('/analytics/admin-account/security',{email,phone,password:pass,passwordConfirmation:pass2});if(document.getElementById('accountPassword'))document.getElementById('accountPassword').value='';if(document.getElementById('accountPasswordConfirm'))document.getElementById('accountPasswordConfirm').value='';await loadAccountData();alert('✅ تنظیمات حساب ذخیره شد');}catch(error){alert(error.message);}
};

window.createFullBackup = async function () {
    try{const data=await accountRequest('/analytics/admin-account/backups',{});lastBackupMeta={id:data.id,date:new Date().toLocaleString('fa-IR'),size:Math.max(1,Math.round((data.size||0)/1024))+' کیلوبایت'};window.renderAccountBackupStatus();location.href='/analytics/admin-account/backups/'+data.id+'/download';alert('✅ پشتیبان محدود به اطلاعات همین آموزشگاه ایجاد شد');}catch(error){alert(error.message);}
};

window.downloadLastBackup = async function () {
    if (!lastBackupMeta) return alert('هنوز پشتیبانی ایجاد نشده است.');
    location.href='/analytics/admin-account/backups/'+lastBackupMeta.id+'/download';
};

window.renderAccountBackupStatus = async function () {
    const el = document.getElementById('accountBackupStatus');
    if (!el) return;
    if (!lastBackupMeta) {
        el.textContent = 'هنوز پشتیبان خودکار ثبت نشده است.';
        return;
    }
    el.textContent = 'آخرین پشتیبان: ' + lastBackupMeta.date + ' · حجم تقریبی: ' + lastBackupMeta.size;
};

setTimeout(function () {
    if (document.getElementById('accountInfo') || document.getElementById('account')) {
        loadAccountData();
    }
}, 200);
})();
