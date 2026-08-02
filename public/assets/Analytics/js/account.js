(function () {
'use strict';
// ==================== حساب کاربری آموزشگاه ====================

let academyProfile = {
    name: 'موزیک آکادمی',
    type: 'آموزشگاه موسیقی',
    manager: 'علی رضایی',
    email: 'admin@musicacademy.ir',
    phone: '۰۲۱-۸۸۷۷۶۶۵۵',
    address: 'تهران، خیابان ولیعصر، پلاک ۱۲۳',
    founded: '۱۳۹۵',
    branches: 4,
    students: 248,
    teachers: 28,
    avatarUrl: '',
    coverUrl: '',
    shortIntro: 'آموزش تخصصی موسیقی برای همه سنین با اساتید مجرب در چندین شعبه.',
    biography: 'موزیک آکادمی از سال ۱۳۹۵ با هدف ارتقای آموزش موسیقی استاندارد در تهران تأسیس شد.\n\nما دوره‌های پیانو، گیتار، ویولن، آواز و سایر سازها را به‌صورت خصوصی و گروهی ارائه می‌دهیم و با برگزاری کنسرت‌ها و مسترکلاس‌ها مسیر رشد هنرجویان را هموار می‌کنیم.',
    privacy: {
        showPublicProfile: true,
        showBranches: true,
        showTeachers: true,
        showContact: true,
        showStats: false,
        indexable: true
    }
};

let academyDocuments = [
    { id: 1, name: 'مجوز فعالیت آموزشگاه.pdf', size: '1.2 مگابایت', date: '۱۴۰۳/۰۲/۱۵', type: 'pdf' },
    { id: 2, name: 'اساسنامه.pdf', size: '850 کیلوبایت', date: '۱۴۰۲/۱۱/۰۱', type: 'pdf' },
    { id: 3, name: 'کارت ملی مدیر.jpg', size: '420 کیلوبایت', date: '۱۴۰۳/۰۱/۲۰', type: 'image' }
];

let academyDevices = [
    { id: 1, name: 'Chrome · Windows', location: 'تهران', lastActive: 'همین الان', current: true, ip: '185.XX.XX.12' },
    { id: 2, name: 'Safari · iPhone', location: 'تهران', lastActive: '۲ ساعت پیش', current: false, ip: '185.XX.XX.44' },
    { id: 3, name: 'Firefox · macOS', location: 'کرج', lastActive: 'دیروز', current: false, ip: '91.XX.XX.8' }
];

let academyLoginHistory = [
    { id: 1, action: 'ورود', device: 'Chrome · Windows', ip: '185.XX.XX.12', date: '۱۴۰۴/۰۵/۱۲ — ۱۴:۳۰', ok: true },
    { id: 2, action: 'خروج', device: 'Chrome · Windows', ip: '185.XX.XX.12', date: '۱۴۰۴/۰۵/۱۱ — ۲۲:۱۰', ok: true },
    { id: 3, action: 'ورود', device: 'Safari · iPhone', ip: '185.XX.XX.44', date: '۱۴۰۴/۰۵/۱۱ — ۰۹:۱۵', ok: true },
    { id: 4, action: 'تلاش ناموفق', device: 'Unknown', ip: '45.XX.XX.99', date: '۱۴۰۴/۰۵/۱۰ — ۰۳:۲۲', ok: false },
    { id: 5, action: 'ورود', device: 'Firefox · macOS', ip: '91.XX.XX.8', date: '۱۴۰۴/۰۵/۰۹ — ۱۸:۴۰', ok: true }
];

let academySecurityAlerts = [
    { id: 1, level: 'warning', title: 'ورود از IP جدید', text: 'ورود موفق از آدرس ناشناخته ثبت شد.', date: '۱۴۰۴/۰۵/۱۰' },
    { id: 2, level: 'info', title: 'تغییر رمز عبور', text: 'رمز عبور حساب مدیریت به‌روزرسانی شد.', date: '۱۴۰۴/۰۴/۲۸' },
    { id: 3, level: 'danger', title: 'چند تلاش ناموفق ورود', text: 'بیش از ۳ تلاش ناموفق در بازه کوتاه شناسایی شد.', date: '۱۴۰۴/۰۴/۱۵' }
];

let lastBackupMeta = null;

window.renderAccountInfo = function () {
    const container = document.getElementById('accountInfo');
    if (!container) return;
    if (window.getAccountInfoHTML) {
        container.innerHTML = window.getAccountInfoHTML(academyProfile);
    }
    const nameEl = document.getElementById('academyName');
    if (nameEl) nameEl.textContent = academyProfile.name;
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
};

window.renderAccountCover = function () {
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

window.removeAccountCover = function () {
    if (!academyProfile.coverUrl) return;
    if (!confirm('حذف کاور پروفایل؟')) return;
    academyProfile.coverUrl = '';
    window.renderAccountCover();
    alert('✅ کاور حذف شد');
};

// ---------- کراپ تصویر (آواتار ۱:۱ دایره‌ای / کاور ۱۶:۹) ----------
let cropState = null;

window.onAccountAvatarChange = function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (event.target) event.target.value = '';
    if (!file) return;
    if (!file.type.startsWith('image/')) return alert('فقط فایل تصویری مجاز است.');
    openImageCropModal(file, 'avatar');
};

window.onAccountCoverChange = function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (event.target) event.target.value = '';
    if (!file) return;
    if (!file.type.startsWith('image/')) return alert('فقط فایل تصویری مجاز است.');
    openImageCropModal(file, 'cover');
};

function openImageCropModal(file, mode) {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const aspect = mode === 'avatar' ? 1 : 16 / 9;
            const title = mode === 'avatar' ? 'کراپ عکس پروفایل (۱×۱ دایره‌ای)' : 'کراپ کاور (۱۶×۹ افقی)';
            document.getElementById('modalContainer').innerHTML = window.getAccountCropModalHTML
                ? window.getAccountCropModalHTML(mode, title) : '';
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
                lastY: 0
            };
            initCropFrame();
            drawCropCanvas();
            bindCropEvents(canvas);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

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
    window.onmousemove = function (e) {
        if (!cropState || !cropState.dragging) return;
        const dx = (e.clientX - cropState.lastX) / cropState.viewScale;
        const dy = (e.clientY - cropState.lastY) / cropState.viewScale;
        cropState.lastX = e.clientX;
        cropState.lastY = e.clientY;
        cropState.cx = clamp(cropState.cx + dx, 0, cropState.img.width - cropState.cw);
        cropState.cy = clamp(cropState.cy + dy, 0, cropState.img.height - cropState.ch);
        drawCropCanvas();
    };
    window.onmouseup = function () {
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

window.zoomCrop = function (delta) {
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

window.applyImageCrop = function () {
    const s = cropState;
    if (!s) return;
    const out = document.createElement('canvas');
    let outW, outH;
    if (s.mode === 'avatar') {
        outW = outH = 512;
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

    const dataUrl = out.toDataURL('image/jpeg', 0.92);
    if (s.mode === 'avatar') {
        academyProfile.avatarUrl = dataUrl;
        window.renderAccountInfo();
        alert('✅ عکس پروفایل ذخیره شد');
    } else {
        academyProfile.coverUrl = dataUrl;
        window.renderAccountCover();
        alert('✅ کاور ذخیره شد');
    }
    cropState = null;
    closeModal();
};

window.cancelImageCrop = function () {
    cropState = null;
    closeModal();
};

window.openEditProfileModal = function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getAccountEditProfileModalHTML
        ? window.getAccountEditProfileModalHTML(academyProfile) : '';
};

window.saveProfile = function () {
    const name = (document.getElementById('editAcademyName') && document.getElementById('editAcademyName').value || '').trim();
    const manager = (document.getElementById('editManager') && document.getElementById('editManager').value || '').trim();
    const address = (document.getElementById('editAddress') && document.getElementById('editAddress').value || '').trim();
    const phone = (document.getElementById('editProfilePhone') && document.getElementById('editProfilePhone').value || '').trim();
    const email = (document.getElementById('editProfileEmail') && document.getElementById('editProfileEmail').value || '').trim();
    const founded = (document.getElementById('editFounded') && document.getElementById('editFounded').value || '').trim();
    if (name) academyProfile.name = name;
    if (manager) academyProfile.manager = manager;
    if (address) academyProfile.address = address;
    if (phone) academyProfile.phone = phone;
    if (email) academyProfile.email = email;
    if (founded) academyProfile.founded = founded;
    window.renderAccountInfo();
    closeModal();
    alert('✅ پروفایل به‌روزرسانی شد');
};

window.openEditBioModal = function () {
    if (!document.getElementById('modalContainer')) return;
    document.getElementById('modalContainer').innerHTML = window.getAccountEditBioModalHTML
        ? window.getAccountEditBioModalHTML(academyProfile) : '';
};

window.saveBio = function () {
    academyProfile.shortIntro = (document.getElementById('editShortIntro') && document.getElementById('editShortIntro').value || '').trim();
    academyProfile.biography = (document.getElementById('editBiography') && document.getElementById('editBiography').value || '').trim();
    window.renderAccountInfo();
    closeModal();
    alert('✅ معرفی و بیوگرافی ذخیره شد');
};

window.renderAccountDocuments = function () {
    const el = document.getElementById('accountDocumentsList');
    if (!el) return;
    el.innerHTML = window.getAccountDocumentsHTML ? window.getAccountDocumentsHTML(academyDocuments) : '';
};

window.onAccountDocumentUpload = function (event) {
    const files = event.target && event.target.files;
    if (!files || !files.length) return;
    Array.from(files).forEach(function (file) {
        academyDocuments.unshift({
            id: Date.now() + Math.random(),
            name: file.name,
            size: (file.size / 1024 > 1024 ? (file.size / 1024 / 1024).toFixed(1) + ' مگابایت' : Math.round(file.size / 1024) + ' کیلوبایت'),
            date: new Date().toLocaleDateString('fa-IR'),
            type: file.type.indexOf('image/') === 0 ? 'image' : 'pdf'
        });
    });
    event.target.value = '';
    window.renderAccountDocuments();
    alert('✅ سند(ها) اضافه شد');
};

window.deleteAccountDocument = function (id) {
    if (!confirm('حذف این سند؟')) return;
    academyDocuments = academyDocuments.filter(function (d) { return d.id !== id; });
    window.renderAccountDocuments();
};

window.renderAccountDevices = function () {
    const el = document.getElementById('accountDevicesList');
    if (!el) return;
    el.innerHTML = window.getAccountDevicesHTML ? window.getAccountDevicesHTML(academyDevices) : '';
};

window.revokeAccountDevice = function (id) {
    if (!confirm('خروج این دستگاه از حساب؟')) return;
    academyDevices = academyDevices.filter(function (d) { return d.id !== id; });
    window.renderAccountDevices();
    alert('✅ دستگاه خارج شد');
};

window.revokeAllOtherDevices = function () {
    if (!confirm('خروج از همه دستگاه‌ها به‌جز دستگاه فعلی؟')) return;
    academyDevices = academyDevices.filter(function (d) { return d.current; });
    window.renderAccountDevices();
    alert('✅ سایر دستگاه‌ها خارج شدند');
};

window.renderAccountLoginHistory = function () {
    const el = document.getElementById('accountLoginHistory');
    if (!el) return;
    el.innerHTML = window.getAccountLoginHistoryHTML ? window.getAccountLoginHistoryHTML(academyLoginHistory) : '';
};

window.renderAccountSecurityAlerts = function () {
    const el = document.getElementById('accountSecurityAlerts');
    if (!el) return;
    el.innerHTML = window.getAccountSecurityAlertsHTML ? window.getAccountSecurityAlertsHTML(academySecurityAlerts) : '';
};

window.renderAccountPrivacy = function () {
    const el = document.getElementById('accountPrivacyOptions');
    if (!el) return;
    el.innerHTML = window.getAccountPrivacyHTML ? window.getAccountPrivacyHTML(academyProfile.privacy) : '';
};

window.savePrivacySettings = function () {
    const p = academyProfile.privacy;
    p.showPublicProfile = !!(document.getElementById('privacyShowPublic') && document.getElementById('privacyShowPublic').checked);
    p.showBranches = !!(document.getElementById('privacyShowBranches') && document.getElementById('privacyShowBranches').checked);
    p.showTeachers = !!(document.getElementById('privacyShowTeachers') && document.getElementById('privacyShowTeachers').checked);
    p.showContact = !!(document.getElementById('privacyShowContact') && document.getElementById('privacyShowContact').checked);
    p.showStats = !!(document.getElementById('privacyShowStats') && document.getElementById('privacyShowStats').checked);
    p.indexable = !!(document.getElementById('privacyIndexable') && document.getElementById('privacyIndexable').checked);
    alert('✅ تنظیمات حریم خصوصی ذخیره شد');
};

window.saveAccountSettings = function () {
    const email = document.getElementById('accountEmail') && document.getElementById('accountEmail').value;
    const phone = document.getElementById('accountPhone') && document.getElementById('accountPhone').value;
    const pass = document.getElementById('accountPassword') && document.getElementById('accountPassword').value;
    const pass2 = document.getElementById('accountPasswordConfirm') && document.getElementById('accountPasswordConfirm').value;
    if (email) academyProfile.email = email;
    if (phone) academyProfile.phone = phone;
    if (pass || pass2) {
        if (pass !== pass2) return alert('تکرار رمز عبور مطابقت ندارد.');
        if (pass.length < 6) return alert('رمز عبور باید حداقل ۶ کاراکتر باشد.');
        academySecurityAlerts.unshift({
            id: Date.now(),
            level: 'info',
            title: 'تغییر رمز عبور',
            text: 'رمز عبور حساب مدیریت به‌روزرسانی شد.',
            date: new Date().toLocaleDateString('fa-IR')
        });
        if (document.getElementById('accountPassword')) document.getElementById('accountPassword').value = '';
        if (document.getElementById('accountPasswordConfirm')) document.getElementById('accountPasswordConfirm').value = '';
    }
    window.renderAccountInfo();
    alert('✅ تنظیمات حساب ذخیره شد');
};

window.createFullBackup = function () {
    lastBackupMeta = {
        date: new Date().toLocaleString('fa-IR'),
        size: (2.4 + Math.random()).toFixed(1) + ' مگابایت',
        id: Date.now()
    };
    // شبیه‌سازی دانلود JSON
    const payload = {
        profile: academyProfile,
        documents: academyDocuments.map(function (d) { return { name: d.name, date: d.date }; }),
        generatedAt: new Date().toISOString(),
        note: 'پشتیبان نمونه — در نسخه واقعی شامل تمام جداول دیتابیس است'
    };
    const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'backup_academy_' + lastBackupMeta.id + '.json';
    link.click();
    window.renderAccountBackupStatus();
    alert('✅ پشتیبان کامل ایجاد و دانلود شد');
};

window.downloadLastBackup = function () {
    if (!lastBackupMeta) return alert('هنوز پشتیبانی ایجاد نشده است.');
    window.createFullBackup();
};

window.renderAccountBackupStatus = function () {
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
        window.renderAccountInfo();
    }
}, 200);
})();
