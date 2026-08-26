(function () {
    'use strict';
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option.name ?? option;
            const label = option.label ?? option.name ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${selected}>${escapeHtml(label)}</option>`;
        }).join('');
    }
    function statusClass(status) {
        return {
            'فعال': 'bg-green-100 text-green-700',
            'غیرفعال': 'bg-red-100 text-red-700',
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700',
            'حذف‌شده': 'bg-gray-100 text-gray-600'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    function formFields(item, prefix) {
        const id = function (n) { return prefix ? prefix + n : 'rule' + n; };
        const branches = (typeof window.getRuleBranches === 'function' ? window.getRuleBranches() : []).map(function (b) {
            return { value: b.key, label: b.name };
        });
        const types = (window.ruleTypesList || []).map(function (t) { return { value: t, label: t }; });
        const statuses = ['فعال','غیرفعال'].map(function (s) { return { value: s, label: s }; });
        const units = (window.ruleValueUnitsList || []).map(function (u) { return { value: u, label: u }; });
        const fixedOrganization = window.ruleOrganizationSelection === 'fixed';
        const showStatus = window.ruleShowStatusField !== false;
        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="${fixedOrganization ? 'hidden' : ''}">
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(branches, item.organizationKey || (branches[0] && branches[0].value))}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان قانون *</label>
                    <input id="${id('Title')}" type="text" value="${escapeHtml(item.title || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع قانون</label>
                    <select id="${id('Type')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(types, item.type || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مقدار</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input id="${id('Value')}" type="number" min="0" step="0.01" value="${escapeHtml(item.valueAmount ?? '')}" placeholder="مثال: ۲۴" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <select id="${id('ValueUnit')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${renderOptions(units, item.valueUnit || 'ساعت')}</select>
                    </div>
                </div>
                <div class="${showStatus ? '' : 'hidden'}">
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getRuleRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.type)}</td>
            <td class="py-4 px-5">${escapeHtml(item.value)}</td>
            <td class="py-4 px-5"><button type="button" ${item.canChangeStatus ? `onclick="cycleRuleStatus(${item.id})"` : 'disabled'} class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)} disabled:cursor-default">${escapeHtml(item.status)}</button></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewRule(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    ${item.canEdit ? `<button data-rule-inline-toggle="${item.id}" onclick="toggleRuleInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button><button onclick="deleteRule(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>` : ''}
                </div>
            </td>`;
    };
    window.getRuleEmptyRowHTML = function () {
        return `<tr><td colspan="6" class="py-12 text-center text-gray-400">قانونی یافت نشد</td></tr>`;
    };
    window.getRuleInlineExpandRowHTML = function (item) {
        return `<td colspan="6" class="p-5 border-t">${window.getRuleInlineEditRowHTML(item)}</td>`;
    };
    window.getRuleInlineEditRowHTML = function (item) {
        return `<div class="rule-inline-editor space-y-6">
            ${formFields(item, 'inlineRule' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineRule(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleRuleInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };
    window.getRuleAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن قانون زمان‌بندی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveRule()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getRuleEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش قانون زمان‌بندی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editRule')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedRule(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getRuleDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد قانون: #${item.id}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        ${item.canEdit ? `<button onclick="editRule(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>` : ''}
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سازمان</span><span class="font-medium">${escapeHtml(item.organizationName || item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(item.type)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مقدار</span><span class="font-medium">${escapeHtml(item.value)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getRulePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF قوانین زمان‌بندی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="rulePdfTitle" type="text" value="گزارش قوانین زمان‌بندی" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="rulePdfSubtitle" type="text" value="لیست قوانین رزرو، لغو و زمان‌بندی" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="rulePdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="rulePdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="rulePdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${(cols || []).map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="rulePdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="rulePdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="rulePdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="rulePdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="rulePdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateRulesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getRulePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const o = options;
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:${o.headerColor};"><tr>
                    ${o.selectedColumns.map(function (c) {
                        return `<th style="padding:12px 14px;text-align:right;font-weight:600;">${escapeHtml(c.label)}</th>`;
                    }).join('')}
                </tr></thead>
                <tbody>
                    ${rows.map(function (item, index) {
                        return `<tr style="background:${index % 2 === 0 ? o.evenRowColor : o.oddRowColor};">
                            ${o.selectedColumns.map(function (c) {
                                const v = c.field === 'index' ? (pageNumber - 1) * o.rowsPerPage + index + 1 : item[c.field];
                                return `<td style="padding:12px 14px;text-align:right;">${escapeHtml(v)}</td>`;
                            }).join('')}
                        </tr>`;
                    }).join('')}
                </tbody>
            </table>
            ${isFirstPage && o.footer ? `<p style="margin-top:16px;color:#6b7280;font-size:12px;">${escapeHtml(o.footer)}</p>` : ''}
            <div style="margin-top:16px;text-align:left;color:#6b7280;font-size:12px;">صفحه ${pageNumber} / ${o.totalPages}</div>
        </div>`;
    };
})();
