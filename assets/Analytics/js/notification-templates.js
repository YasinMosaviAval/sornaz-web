(function () {
    'use strict';
    const fieldClass = 'w-full border border-gray-300 rounded-2xl py-3.5 px-5';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option;
            const label = option.label ?? option.name ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return '<option value="' + escapeHtml(value) + '" ' + selected + '>' + escapeHtml(label) + '</option>';
        }).join('');
    }

    function priorityClass(priority) {
        return {
            'بالا': 'bg-red-100 text-red-700',
            'متوسط': 'bg-yellow-100 text-yellow-700',
            'کم': 'bg-blue-100 text-blue-700'
        }[priority] || 'bg-gray-100 text-gray-600';
    }

    function statusClass(status) {
        return {
            'منتشر شده': 'bg-green-100 text-green-700',
            'پیش‌نویس': 'bg-gray-100 text-gray-600',
            'منقضی': 'bg-slate-200 text-slate-600'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    window.getNotificationRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5" dir="ltr">@${escapeHtml(item.recipientUsername || '—')} <small class="text-gray-400">(${escapeHtml(item.recipientId || '—')})</small></td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-600">${escapeHtml(item.audience || 'همه')}</span></td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${priorityClass(item.priority)}">${escapeHtml(item.priority)}</span></td>
            <td class="py-4 px-5">${escapeHtml(item.date)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewNotification(${item.id})" class="text-indigo-600 hover:underline text-sm">مشاهده</button>
                    <button onclick="deleteNotification(${item.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getNotificationEmptyRowHTML = function () {
        return '<tr><td colspan="8" class="py-12 text-center text-gray-400">اعلانی یافت نشد</td></tr>';
    };

    window.getNotificationAddModalHTML = function () {
        const branches = (typeof window.getNotificationBranches === 'function' ? window.getNotificationBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const priorities = (window.notificationPrioritiesList || []).map(function (p) { return { value: p, label: p }; });
        const audiences = (window.notificationAudiencesList || []).map(function (a) { return { value: a, label: a }; });

        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ثبت اعلان جدید</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان اعلان *</label>
                        <input id="notifTitle" type="text" class="${fieldClass}" placeholder="عنوان کوتاه و واضح">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">شعبه *</label>
                            <select id="notifBranch" class="${fieldClass}">
                                ${renderOptions(branches)}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">مخاطب</label>
                            <select id="notifAudience" class="${fieldClass}">
                                ${renderOptions(audiences, 'همه')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">اولویت</label>
                            <select id="notifPriority" class="${fieldClass}">
                                ${renderOptions(priorities, 'متوسط')}
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">متن اعلان *</label>
                        <textarea id="notifBody" rows="5" class="${fieldClass}" placeholder="متن کامل اعلان..."></textarea>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button onclick="saveNotification(false)" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">انتشار</button>
                        <button onclick="saveNotification(true)" class="flex-1 border border-indigo-200 text-indigo-700 py-3.5 rounded-2xl hover:bg-indigo-50">ذخیره پیش‌نویس</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getNotificationDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-start rounded-t-3xl gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="px-2.5 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs ${priorityClass(item.priority)}">${escapeHtml(item.priority)}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-600">${escapeHtml(item.audience || 'همه')}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700">${escapeHtml(item.source || 'سیستم')}</span>
                        </div>
                        <h2 class="text-2xl font-bold leading-tight">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.date)} · ${escapeHtml(item.branchName)}</p>
                    </div>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 shrink-0">×</button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مخاطب</span><span class="font-medium">${escapeHtml(item.audience || 'همه')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اولویت</span><span class="font-medium">${escapeHtml(item.priority)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">منبع</span><span class="font-medium">${escapeHtml(item.source || 'سیستم')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ انتشار</span><span class="font-medium">${escapeHtml(item.date)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-700 mb-3 flex items-center gap-2"><i class="fas fa-bell"></i> متن اعلان</h3>
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${escapeHtml(item.body || 'بدون متن')}</div>
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        ${item.status === 'پیش‌نویس'
                            ? '<button onclick="publishNotification(' + item.id + ')" class="bg-indigo-600 text-white px-5 py-3 rounded-2xl text-sm hover:bg-indigo-700">انتشار</button>'
                            : ''}
                        ${item.status === 'منتشر شده'
                            ? '<button onclick="expireNotification(' + item.id + ')" class="border border-gray-300 px-5 py-3 rounded-2xl text-sm hover:bg-gray-50">منقضی کردن</button>'
                            : ''}
                        <button onclick="deleteNotification(${item.id}); closeModal();" class="border border-red-200 text-red-600 px-5 py-3 rounded-2xl text-sm hover:bg-red-50">حذف</button>
                        <button onclick="closeModal()" class="border border-gray-300 px-5 py-3 rounded-2xl text-sm hover:bg-gray-50">بستن</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
})();
