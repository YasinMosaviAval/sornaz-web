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
            'غیرفعال': 'bg-gray-100 text-gray-600',
            'پر شده': 'bg-amber-100 text-amber-700',
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    function formFields(item, prefix) {
        const id = function (n) { return prefix ? prefix + n : 'bs' + n; };
        const branches = (typeof window.getBranchScheduleOrganizations === 'function' ? window.getBranchScheduleOrganizations() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const days = (window.branchScheduleDaysList || []).map(function (d) { return { value: d, label: d }; });
        const statuses = (window.branchScheduleStatusesList || []).map(function (s) { return { value: s, label: s }; });
        const timezones = (window.branchScheduleTimezoneList || []).map(function (tz) {
            return { value: tz.value, label: tz.label };
        });
        const branchId = item.branchId || (branches[0] && branches[0].value) || 1;
        const fixedOrganization = (window.branchOfferingData && window.branchOfferingData.organization_selection === 'fixed') || window.branchScheduleOrganizationSelection === 'fixed';
        const editing = Boolean(item.id || (prefix && (prefix.indexOf('editBs') === 0 || prefix.indexOf('inlineBs') === 0)));
        const inlineEditing = Boolean(prefix && prefix.indexOf('inlineBs') === 0);
        const slotsHtml = typeof window.buildBranchScheduleTimeSlotsHTML === 'function'
            ? window.buildBranchScheduleTimeSlotsHTML(id('TimeSlots'), branchId, item.slots || [], item.rangeStatuses || [], item.ranges || [])
            : '';

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                ${inlineEditing ? '' : `<div class="${fixedOrganization ? 'hidden' : ''}">
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <select id="${id('Branch')}" ${editing ? 'disabled' : ''} class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                            onchange="window.loadExistingBranchScheduleDay('${prefix}',false)">
                        ${renderOptions(branches, item.user_id || item.organizationUserId || item.branchId)}
                    </select>
                </div>`}
                ${inlineEditing ? '' : `<div>
                    <label class="block text-sm font-medium mb-2">روز *</label>
                    <select id="${id('Day')}" ${editing ? 'disabled' : ''} onchange="window.loadExistingBranchScheduleDay('${prefix}',false)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                        ${renderOptions(days, item.day || 'شنبه')}
                    </select>
                </div>`}
                <div>
                    <label class="block text-sm font-medium mb-2">منطقه زمانی</label>
                    <select id="${id('Timezone')}" data-previous-timezone="${escapeHtml(item.timezone || 'Asia/Tehran')}" onchange="changeBranchScheduleTimezone(this, '${prefix}')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(timezones, item.timezone || 'Asia/Tehran')}
                    </select>
                </div>
                <div class="sm:col-span-2 rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5 space-y-5">
                    <div><label class="block text-sm font-medium mb-2">ساعات کاری</label><div id="${id('TimeSlots')}">${slotsHtml}</div></div>
                    <div><label class="block text-sm font-medium mb-2">خلاصه</label><input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border rounded-2xl py-3.5 px-5 bg-white"></div>
                    <div><label class="block text-sm font-medium mb-2">توضیحات</label><textarea id="${id('Description')}" rows="3" class="w-full border rounded-2xl py-3.5 px-5 bg-white">${escapeHtml(item.description || '')}</textarea></div>
                </div>
            </div>`;
    }

    window.getBranchScheduleRowHTML = function (item) {
        return `
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.day)}</td>
            <td class="py-4 px-5 font-mono text-sm">${escapeHtml(item.timeLabel || item.time || '—')}</td>
            <td class="py-4 px-5 text-sm">${escapeHtml(item.repeatPeriod || 'هفتگی')}</td>
            <td class="py-4 px-5 text-xs text-gray-500">${escapeHtml(item.timezone || 'Asia/Tehran')}</td>
            <td class="py-4 px-5">${item.readOnly?`<span class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span>`:`<button type="button" onclick="cycleBranchScheduleStatus(${item.id})" class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</button>`}</td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewBranchSchedule(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    ${item.readOnly?'':`<button onclick="toggleBranchScheduleInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button><button onclick="deleteBranchSchedule(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>`}
                </div>
            </td>`;
    };
    window.getBranchScheduleEmptyRowHTML = function () {
        return `<tr><td colspan="7" class="py-12 text-center text-gray-400">زمان‌بندی‌ای یافت نشد</td></tr>`;
    };
    window.getBranchScheduleInlineExpandRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getBranchScheduleInlineEditRowHTML(item)}</td>`;
    };
    window.getBranchScheduleInlineEditRowHTML = function (item) {
        return `<div class="bs-inline-editor space-y-6">
            ${formFields(item, 'inlineBs' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineBranchSchedule(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleBranchScheduleInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };
    window.getBranchScheduleAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن برنامه زمانی سازمان</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveBranchSchedule()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getBranchScheduleEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش برنامه زمانی سازمان</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editBs')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedBranchSchedule(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getBranchScheduleDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.branchName)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.day)} · ${escapeHtml(item.timeLabel || item.time || '')}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        ${item.readOnly?'':`<button onclick="editBranchSchedule(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>`}
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سازمان</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">روز</span><span class="font-medium">${escapeHtml(item.day)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساعت</span><span class="font-medium">${escapeHtml(item.timeLabel || item.time || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">دوره تکرار</span><span class="font-medium">${escapeHtml(item.repeatPeriod || 'هفتگی')}</span></div>
                        ${(item.repeatPeriod === 'ماهانه' || item.repeatPeriod === 'سالانه') && item.repeatDate
                            ? `<div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ مرجع</span><span class="font-medium">${escapeHtml(item.repeatDate)}</span></div>` : ''}
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">منطقه زمانی</span><span class="font-medium">${escapeHtml(item.timezone || 'Asia/Tehran')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getBranchSchedulePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF برنامه زمانی سازمان</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="bsPdfTitle" type="text" value="گزارش برنامه زمانی سازمان" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="bsPdfSubtitle" type="text" value="ساعات کاری و برنامه زمانی سازمان‌ها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="bsPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="bsPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="bsPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${(cols || []).map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="bsPdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="bsPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="bsPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="bsPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="bsPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateBranchSchedulesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getBranchSchedulePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
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
