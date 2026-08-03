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

    function typeBadge(type) {
        return {
            specialized: 'bg-purple-100 text-purple-700',
            custom: 'bg-amber-100 text-amber-700',
            general: 'bg-blue-100 text-blue-700'
        }[type] || 'bg-blue-100 text-blue-700';
    }

    function statusBadge(status) {
        return status === 'فعال'
            ? 'bg-green-100 text-green-700'
            : 'bg-gray-100 text-gray-600';
    }

    function categoryLabel(cat) {
        return (window.pointCategoryLabels && window.pointCategoryLabels[cat]) || cat || '—';
    }

    function typeLabel(type) {
        return (window.pointTypeLabels && window.pointTypeLabels[type]) || type || '—';
    }

    function formFields(item, prefix) {
        item = item || {};
        const id = function (n) { return prefix + n; };
        const branches = (typeof window.getPointBranches === 'function' ? window.getPointBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const categories = Object.keys(window.pointCategoryLabels || {}).map(function (k) {
            return { value: k, label: window.pointCategoryLabels[k] };
        });
        const statuses = (window.pointStatusesList || []).map(function (s) { return { value: s, label: s }; });
        const types = [
            { value: 'general', label: 'عمومی' },
            { value: 'specialized', label: 'تخصصی' },
            { value: 'custom', label: 'اختصاصی' }
        ];

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="${id('Title')}" type="text" value="${escapeHtml(item.title || '')}" class="${fieldClass}" placeholder="مثلاً حضور در جلسه کلاس">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="${fieldClass}" placeholder="یک خط کوتاه">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مقدار امتیاز *</label>
                    <input id="${id('Value')}" type="number" min="0" value="${escapeHtml(item.points != null ? item.points : 10)}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع</label>
                    <select id="${id('Type')}" class="${fieldClass}">
                        ${renderOptions(types, item.type || 'general')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">دسته</label>
                    <select id="${id('Category')}" class="${fieldClass}">
                        ${renderOptions(categories, item.category || 'profile')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="${fieldClass}">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">کد عملیات (action) *</label>
                    <input id="${id('Action')}" type="text" value="${escapeHtml(item.action || '')}" class="${fieldClass}" placeholder="class_attendance">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="${fieldClass}">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع مرجع</label>
                    <input id="${id('RefType')}" type="text" value="${escapeHtml(item.reference_type || '')}" class="${fieldClass}" placeholder="user / session / event">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شناسه مرجع</label>
                    <input id="${id('RefId')}" type="number" value="${escapeHtml(item.reference_id || '')}" class="${fieldClass}" placeholder="اختیاری">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Desc')}" rows="2" class="${fieldClass}">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getPointRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${typeBadge(item.type)}">${escapeHtml(typeLabel(item.type))}</span></td>
            <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-600">${escapeHtml(categoryLabel(item.category))}</span></td>
            <td class="py-4 px-5 font-bold text-indigo-600">+${item.points}</td>
            <td class="py-4 px-5 text-sm text-gray-500 font-mono text-xs">${escapeHtml(item.action || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status || 'فعال')}</span></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewPoint(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="togglePointInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deletePoint(${item.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getPointEmptyRowHTML = function () {
        return '<tr><td colspan="8" class="py-12 text-center text-gray-400">قانون امتیازی یافت نشد</td></tr>';
    };

    window.getPointInlineExpandRowHTML = function (item) {
        return '<td colspan="8" class="p-5 border-t">' + (window.getPointInlineEditRowHTML ? window.getPointInlineEditRowHTML(item) : '') + '</td>';
    };

    window.getPointInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlinePoint' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlinePoint(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="togglePointInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };

    window.getPointAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-4xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن قانون امتیاز</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6">
                    ${formFields({}, 'point')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="savePoint()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getPointEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-4xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش قانون امتیاز</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6">
                    ${formFields(item, 'editPoint')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveEditedPoint(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getPointDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-start rounded-t-3xl gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="px-2.5 py-1 rounded-full text-xs ${typeBadge(item.type)}">${escapeHtml(typeLabel(item.type))}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-600">${escapeHtml(categoryLabel(item.category))}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs ${statusBadge(item.status)}">${escapeHtml(item.status || 'فعال')}</span>
                        </div>
                        <h2 class="text-2xl font-bold leading-tight">${escapeHtml(item.title)}</h2>
                        <p class="text-indigo-600 font-bold text-xl mt-1">+${item.points} امتیاز</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="editPoint(${item.id})" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? '<p class="text-indigo-600 font-medium">' + escapeHtml(item.summary) + '</p>' : ''}
                    ${item.description ? '<div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 text-sm text-gray-700 leading-relaxed">' + escapeHtml(item.description) + '</div>' : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(typeLabel(item.type))}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">دسته</span><span class="font-medium">${escapeHtml(categoryLabel(item.category))}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عملیات</span><span class="font-medium font-mono text-xs">${escapeHtml(item.action || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع مرجع</span><span class="font-medium">${escapeHtml(item.reference_type || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شناسه مرجع</span><span class="font-medium">${item.reference_id != null ? item.reference_id : '—'}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status || 'فعال')}</span></div>
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <button onclick="togglePointStatus(${item.id})" class="border border-gray-300 px-5 py-3 rounded-2xl text-sm hover:bg-gray-50">
                            ${item.status === 'فعال' ? 'غیرفعال کردن' : 'فعال کردن'}
                        </button>
                        <button onclick="deletePoint(${item.id}); closeModal();" class="border border-red-200 text-red-600 px-5 py-3 rounded-2xl text-sm hover:bg-red-50">حذف</button>
                        <button onclick="closeModal()" class="bg-indigo-600 text-white px-5 py-3 rounded-2xl text-sm hover:bg-indigo-700">بستن</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
})();
