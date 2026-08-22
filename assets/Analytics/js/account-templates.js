(function () {
    'use strict';
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    window.getAccountInfoHTML = function (p) {
        const rows = [
            ['نام', p.name],
            ['ایمیل', p.email],
            ['تلفن', p.phone],
            ['آدرس', p.address],
            [p.accountType === 'human' ? 'تاریخ تولد' : 'سال تأسیس', p.founded]
        ];
        if (p.accountType !== 'human') rows.splice(1, 0, ['مدیر مسئول', p.manager]);
        if (p.accountType !== 'human') rows.push([p.accountType === 'branch' ? 'نوع حساب' : 'تعداد شعبه‌ها', p.accountType === 'branch' ? 'شعبه' : (p.branches || 0) + ' شعبه'], ['تعداد هنرجویان', (p.students || 0) + ' نفر'], ['تعداد اساتید', (p.teachers || 0) + ' نفر']);
        return rows.map(function (r) {
            return '<div class="flex justify-between border-b pb-3 gap-4">' +
                '<span class="text-gray-500 shrink-0">' + escapeHtml(r[0]) + '</span>' +
                '<span class="font-medium text-left">' + escapeHtml(r[1]) + '</span></div>';
        }).join('');
    };

    window.getAccountEditProfileModalHTML = function (p) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش پروفایل</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">نام</label>
                            <input id="editAcademyName" type="text" value="${escapeHtml(p.name)}" class="${fieldClass}">
                        </div>
                        ${p.accountType === 'human' ? '' : `<div>
                            <label class="block text-sm font-medium mb-2">مدیر اصلی</label>
                            <input id="editManager" type="text" value="${escapeHtml(p.manager)}" disabled class="${fieldClass} bg-gray-100 text-gray-500 cursor-not-allowed">
                        </div>`}
                        <div>
                            <label class="block text-sm font-medium mb-2">ایمیل</label>
                            <input id="editProfileEmail" type="email" value="${escapeHtml(p.email)}" class="${fieldClass}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">تلفن</label>
                            <input id="editProfilePhone" type="text" value="${escapeHtml(p.phone)}" class="${fieldClass}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">${p.accountType === 'human' ? 'تاریخ تولد' : 'تاریخ تأسیس'}</label>
                            <input id="editFounded" type="date" value="${escapeHtml(p.founded)}" class="${fieldClass}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">آدرس</label>
                            <input id="editAddress" type="text" value="${escapeHtml(p.address)}" class="${fieldClass}">
                        </div>
                    </div>
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveProfile()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getAccountEditBioModalHTML = function (p) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش معرفی و بیوگرافی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">معرفی کوتاه</label>
                        <textarea id="editShortIntro" rows="2" class="${fieldClass}" placeholder="یک یا دو خط درباره حساب">${escapeHtml(p.shortIntro || '')}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">بیوگرافی کامل</label>
                        <textarea id="editBiography" rows="8" class="${fieldClass}" placeholder="متن کامل معرفی آموزشگاه">${escapeHtml(p.biography || '')}</textarea>
                    </div>
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveBio()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getAccountDocumentsHTML = function (docs) {
        if (!docs || !docs.length) {
            return '<p class="text-center text-gray-400 py-8">سندی ثبت نشده است</p>';
        }
        return docs.map(function (d) {
            const icon = d.type === 'image' ? 'fa-file-image text-blue-500' : 'fa-file-pdf text-red-500';
            return `<div class="flex items-center justify-between gap-4 border border-gray-100 rounded-2xl px-4 py-3 hover:bg-gray-50">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fas ${icon} text-xl"></i>
                    <div class="min-w-0">
                        <p class="font-medium text-sm truncate">${escapeHtml(d.name)}</p>
                        <p class="text-xs text-gray-400">${escapeHtml(d.size)} · ${escapeHtml(d.date)}${d.number?' · شماره '+escapeHtml(d.number):''}</p>
                    </div>
                </div>
                <div class="flex gap-3 shrink-0">${d.url?'<a href="'+escapeHtml(d.url)+'" target="_blank" class="text-indigo-600 text-sm hover:underline">مشاهده</a>':''}<button onclick="deleteAccountDocument(${d.id})" class="text-red-500 text-sm hover:underline">حذف</button></div>
            </div>`;
        }).join('');
    };

    window.getAccountDocumentModalHTML = function (files) {
        const names=(files||[]).map(f=>escapeHtml(f.name)).join('، ');
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl" onclick="event.stopPropagation()"><div class="px-7 py-5 border-b flex justify-between"><h2 class="text-xl font-bold">مشخصات سند آموزشگاه</h2><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-7 space-y-4"><p class="text-xs text-gray-500 break-words">${names}</p><label class="block text-sm">نوع سند<select id="accountDocumentType" class="${fieldClass} mt-2"><option value="license">مجوز فعالیت</option><option value="identity">مدرک هویتی</option><option value="statute">اساسنامه</option><option value="tax">مدرک مالیاتی</option><option value="contract">قرارداد</option><option value="certificate">گواهی</option><option value="other">سایر</option></select></label><label class="block text-sm">شماره سند<input id="accountDocumentNumber" class="${fieldClass} mt-2"></label><div class="grid grid-cols-2 gap-4"><label class="block text-sm">تاریخ صدور<input id="accountDocumentIssuedAt" type="date" class="${fieldClass} mt-2"></label><label class="block text-sm">تاریخ انقضا<input id="accountDocumentExpiresAt" type="date" class="${fieldClass} mt-2"></label></div><div class="flex gap-3 pt-2"><button onclick="saveAccountDocuments()" class="flex-1 bg-indigo-600 text-white py-3 rounded-2xl">آپلود و ثبت</button><button onclick="closeModal()" class="flex-1 border py-3 rounded-2xl">انصراف</button></div></div></div></div>`;
    };

    window.getAccountDevicesHTML = function (devices) {
        if (!devices || !devices.length) {
            return '<p class="text-center text-gray-400 py-8">دستگاهی ثبت نشده</p>';
        }
        let html = devices.map(function (d) {
            return `<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-gray-100 rounded-2xl px-4 py-3">
                <div>
                    <p class="font-medium text-sm">${escapeHtml(d.name)} ${d.current ? '<span class="text-xs text-green-600 mr-2">(دستگاه فعلی)</span>' : ''}</p>
                    <p class="text-xs text-gray-400 mt-1">${escapeHtml(d.location)} · ${escapeHtml(d.ip)} · ${escapeHtml(d.lastActive)}</p>
                </div>
                ${!d.current ? '<button onclick="revokeAccountDevice(' + d.id + ')" class="text-red-500 text-sm hover:underline shrink-0">خروج دستگاه</button>' : ''}
            </div>`;
        }).join('');
        html += '<div class="pt-2"><button onclick="revokeAllOtherDevices()" class="text-sm text-indigo-600 hover:underline">خروج از همه دستگاه‌های دیگر</button></div>';
        return html;
    };

    window.getAccountLoginHistoryHTML = function (list) {
        if (!list || !list.length) return '<p class="text-gray-400 text-sm">موردی نیست</p>';
        return list.map(function (h) {
            const color = h.ok ? 'text-green-600' : 'text-red-500';
            return `<div class="flex justify-between gap-3 border-b border-gray-50 pb-3 text-sm">
                <div>
                    <span class="font-medium ${color}">${escapeHtml(h.action)}</span>
                    <span class="text-gray-500 mr-2">${escapeHtml(h.device)}</span>
                    <p class="text-xs text-gray-400 mt-0.5">${escapeHtml(h.ip)}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">${escapeHtml(h.date)}</span>
            </div>`;
        }).join('');
    };

    window.getAccountSecurityAlertsHTML = function (list) {
        if (!list || !list.length) return '<p class="text-gray-400 text-sm">هشداری نیست</p>';
        const levelClass = { danger: 'border-red-200 bg-red-50', warning: 'border-amber-200 bg-amber-50', info: 'border-blue-200 bg-blue-50' };
        return list.map(function (a) {
            return `<div class="border rounded-2xl px-4 py-3 ${levelClass[a.level] || 'border-gray-100 bg-gray-50'}">
                <div class="flex justify-between gap-2">
                    <p class="font-medium text-sm">${escapeHtml(a.title)}</p>
                    <span class="text-xs text-gray-400">${escapeHtml(a.date)}</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">${escapeHtml(a.text)}</p>
            </div>`;
        }).join('');
    };

    window.getAccountPrivacyHTML = function (privacy) {
        privacy = privacy || {};
        const options = [
            { id: 'privacyShowPublic', key: 'showPublicProfile', label: 'نمایش پروفایل عمومی در وبسایت' },
            { id: 'privacyShowContact', key: 'showContact', label: 'نمایش اطلاعات تماس عمومی' },
            { id: 'privacyIndexable', key: 'indexable', label: 'اجازه ایندکس شدن در موتورهای جستجو' }
        ];
        if (privacy.accountType === 'academy') options.splice(1, 0,
            { id: 'privacyShowBranches', key: 'showBranches', label: 'نمایش لیست شعبه‌ها در صفحه عمومی' },
            { id: 'privacyShowTeachers', key: 'showTeachers', label: 'نمایش اساتید در صفحه عمومی' },
            { id: 'privacyShowStats', key: 'showStats', label: 'نمایش آمار (تعداد هنرجو / کلاس)' });
        if (privacy.accountType === 'branch') options.splice(1, 0,
            { id: 'privacyShowTeachers', key: 'showTeachers', label: 'نمایش اساتید در صفحه عمومی' },
            { id: 'privacyShowStats', key: 'showStats', label: 'نمایش آمار (تعداد هنرجو / کلاس)' });
        return options.map(function (o) {
            const checked = privacy[o.key] ? 'checked' : '';
            return `<label class="flex items-center gap-3 p-3 border border-gray-100 rounded-2xl hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" id="${o.id}" ${checked} class="h-4 w-4 text-indigo-600 rounded">
                <span class="text-sm text-gray-700">${escapeHtml(o.label)}</span>
            </label>`;
        }).join('');
    };

    window.getAccountCropModalHTML = function (mode, title) {
        const hint = mode === 'avatar'
            ? 'کادر دایره‌ای ۱×۱ را جابه‌جا کنید و با دکمه‌های زوم اندازه را تغییر دهید. نسبت همیشه مربعی می‌ماند.'
            : 'کادر ۱۶×۹ را جابه‌جا کنید و با زوم کوچک/بزرگ کنید. نسبت افقی ثابت می‌ماند و به هم نمی‌ریزد.';
        return `<div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) cancelImageCrop()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-6 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-6 py-4 border-b flex justify-between items-center gap-3">
                    <div>
                        <h2 class="text-xl font-bold">${escapeHtml(title || 'کراپ تصویر')}</h2>
                        <p class="text-xs text-gray-500 mt-1">${escapeHtml(hint)}</p>
                    </div>
                    <button type="button" onclick="cancelImageCrop()" class="text-3xl text-gray-300 leading-none">×</button>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="bg-gray-900 rounded-2xl overflow-hidden flex items-center justify-center min-h-[200px]">
                        <canvas id="accountCropCanvas" class="max-w-full cursor-move touch-none"></canvas>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
                        <p id="accountCropInfo" class="text-sm text-gray-500"></p>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="zoomCrop(1)" class="px-4 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-sm" title="کوچک‌تر کردن کادر (زوم این)">
                                <i class="fas fa-search-plus"></i> زوم +
                            </button>
                            <button type="button" onclick="zoomCrop(-1)" class="px-4 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-sm" title="بزرگ‌تر کردن کادر (زوم اوت)">
                                <i class="fas fa-search-minus"></i> زوم −
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-5">
                        <button type="button" onclick="applyImageCrop()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">تأیید و ذخیره</button>
                        <button type="button" onclick="cancelImageCrop()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
})();
