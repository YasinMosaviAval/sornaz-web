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

    function branchOptions(selected) {
        const branches = (typeof window.getPermissionBranches === 'function' ? window.getPermissionBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        return '<option value="all"' + (selected === 'all' || selected === undefined ? ' selected' : '') + '>همه شعبه‌ها</option>' +
            renderOptions(branches, selected === 'all' ? '' : selected);
    }

    function formFields(item, prefix) {
        item = item || {};
        const id = function (n) { return prefix + n; };
        const groups = (window.permissionGroupsList || []).map(function (g) { return { value: g, label: g }; });
        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="${fieldClass}">${branchOptions(item.branchId)}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">گروه</label>
                    <select id="${id('Group')}" class="${fieldClass}">${renderOptions(groups, item.group || 'عمومی')}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="${fieldClass}" placeholder="request_add_lesson">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان *</label>
                    <input id="${id('Title')}" type="text" value="${escapeHtml(item.title || '')}" class="${fieldClass}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان انگلیسی</label>
                    <input id="${id('TitleEn')}" type="text" value="${escapeHtml(item.title_en || '')}" class="${fieldClass}">
                </div>
            </div>`;
    }

    window.getPermissionRowHTML = function (item) {
        return `
            <td class="py-4 px-4 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-4">${escapeHtml(item.title)}</td>
            <td class="py-4 px-4 text-gray-500">${escapeHtml(item.title_en)}</td>
            <td class="py-4 px-4"><span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">${escapeHtml(item.group)}</span></td>
            <td class="py-4 px-4">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-4 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewPermission(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="togglePermissionInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deletePermission(${item.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getPermissionEmptyRowHTML = function () {
        return '<tr><td colspan="6" class="py-12 text-center text-gray-400">دسترسی‌ای یافت نشد</td></tr>';
    };

    window.getPermissionInlineExpandRowHTML = function (item) {
        return '<td colspan="6" class="p-5 border-t">' + (window.getPermissionInlineEditRowHTML ? window.getPermissionInlineEditRowHTML(item) : '') + '</td>';
    };

    window.getPermissionInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">${formFields(item, 'inlinePerm' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlinePermission(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="togglePermissionInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div></div>`;
    };

    window.getPermissionAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-2xl font-bold">افزودن دسترسی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div>
                <div class="p-8 space-y-6">${formFields({}, 'perm')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="savePermission()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ثبت مجوز</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div></div>`;
    };

    window.getPermissionEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-2xl font-bold">ویرایش دسترسی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div>
                <div class="p-8 space-y-6">${formFields(item, 'editPerm')}
                    <div class="flex gap-4 pt-2">
                        <button onclick="saveEditedPermission(${item.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div></div>`;
    };

    window.getPermissionDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.name)}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="editPermission(${item.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-4 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عنوان</span><span class="font-medium">${escapeHtml(item.title)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عنوان انگلیسی</span><span class="font-medium">${escapeHtml(item.title_en)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">گروه</span><span class="font-medium">${escapeHtml(item.group)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                    </div>
                </div>
            </div></div>`;
    };

    window.getPermissionPDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-2xl font-bold">تنظیمات خروجی PDF دسترسی‌ها</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div>
                <div class="p-8 space-y-5" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="permPdfTitle" type="text" value="گزارش دسترسی‌ها" class="${fieldClass}">
                    <input id="permPdfSubtitle" type="text" value="لیست مجوزهای سیستم" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-4">
                        <select id="permPdfFormat" class="${fieldClass}"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="permPdfOrientation" class="${fieldClass}"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="permPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="${fieldClass}">
                    <div class="grid grid-cols-2 gap-2">${(cols || []).map(function (c) {
                        return '<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="permPdfCol-' + c.field + '" checked> ' + c.label + '</label>';
                    }).join('')}</div>
                    <div class="grid grid-cols-3 gap-4">
                        <input id="permPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="permPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="permPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="permPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4"><button onclick="generatePermissionsPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                    <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button></div>
                </div>
            </div></div>`;
    };

    window.getPermissionPDFPageHTML = function (pageNumber, rows, isFirstPage, o) {
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;"><thead style="background:${o.headerColor};"><tr>
                ${o.selectedColumns.map(function (c) { return '<th style="padding:12px 14px;text-align:right;font-weight:600;">' + escapeHtml(c.label) + '</th>'; }).join('')}
            </tr></thead><tbody>
                ${rows.map(function (item, index) {
                    return '<tr style="background:' + (index % 2 === 0 ? o.evenRowColor : o.oddRowColor) + ';">' +
                        o.selectedColumns.map(function (c) {
                            const v = c.field === 'index' ? (pageNumber - 1) * o.rowsPerPage + index + 1 : item[c.field];
                            return '<td style="padding:12px 14px;text-align:right;">' + escapeHtml(v) + '</td>';
                        }).join('') + '</tr>';
                }).join('')}
            </tbody></table>
            ${isFirstPage && o.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px;">${escapeHtml(o.footer)}</p>` : ''}
            <div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px;">صفحه ${pageNumber} / ${o.totalPages}</div>
        </div>`;
    };
})();
