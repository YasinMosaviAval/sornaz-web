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

window.onAccountAvatarChange = function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        alert('فقط فایل تصویری مجاز است.');
        return;
    }
    academyProfile.avatarUrl = URL.createObjectURL(file);
    window.renderAccountInfo();
    alert('✅ عکس پروفایل به‌روزرسانی شد');
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

window.onAccountCoverChange = function (event) {
    const file = event.target && event.target.files && event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        alert('فقط فایل تصویری مجاز است.');
        return;
    }
    // قالب پیشنهادی ۱۹۲۰×۱۰۸۰ (۱۶:۹ افقی)
    academyProfile.coverUrl = URL.createObjectURL(file);
    window.renderAccountCover();
    if (event.target) event.target.value = '';
    alert('✅ کاور پروفایل به‌روزرسانی شد');
};

window.removeAccountCover = function () {
    if (!academyProfile.coverUrl) return;
    if (!confirm('حذف کاور پروفایل؟')) return;
    academyProfile.coverUrl = '';
    window.renderAccountCover();
    alert('✅ کاور حذف شد');
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
