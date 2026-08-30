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
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    function formFields(item, prefix) {
        const id = function (n) { return prefix ? prefix + n : 'hl' + n; };
        const branches = (typeof window.getHolidayLeaveBranches === 'function' ? window.getHolidayLeaveBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const members = (typeof window.getHolidayLeaveMemberOptions === 'function' ? window.getHolidayLeaveMemberOptions() : []);
        const types = (window.holidayLeaveTypeList || []).map(function (t) { return { value: t.value, label: t.label }; });
        const timezones = (window.holidayLeaveTimezoneList || []).map(function (tz) {
            return { value: tz.value, label: tz.label };
        });
        const branchId = item.organizationUserId || (branches[0] && branches[0].value) || '';
        const fixedOrganization=typeof window.isHolidayLeaveOrganizationFixed==='function'&&window.isHolidayLeaveOrganizationFixed();
        const slotsHtml = typeof window.buildHolidayLeaveTimeSlotsHTML === 'function'
            ? window.buildHolidayLeaveTimeSlotsHTML(id('TimeSlots'), branchId, item.slots || [])
            : '';

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <input id="${id('RecordId')}" type="hidden" value="${escapeHtml(item.id||'')}">
                <div class="${fixedOrganization?'hidden':''}">
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                            onchange="window.refreshHolidayLeaveMembers('${prefix}');window.refreshHolidayLeaveTimeSlots('${id('TimeSlots')}', this.value, [])">
                        ${renderOptions(branches, branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عضو *</label>
                    <select id="${id('Member')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                            onchange="window.refreshHolidayLeaveConflicts('${prefix}')">
                        <option value="">انتخاب عضو</option>
                        ${renderOptions(members, item.membershipId || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ *</label>
                    <input id="${id('Date')}" type="date" value="${escapeHtml(item.date || '')}" onchange="window.refreshHolidayLeaveConflicts('${prefix}')"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع</label>
                    <select id="${id('Type')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(types, item.type || 'vacation')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">منطقه زمانی</label>
                    <select id="${id('Timezone')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(timezones, item.timezone || 'Asia/Tehran')}
                    </select>
                </div>
                <div class="sm:col-span-2 rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5 space-y-5">
                    <div><label class="block text-sm font-medium mb-2">ساعات کاری</label>
                    <div id="${id('TimeSlots')}" class="max-h-64 overflow-y-auto">
                        ${slotsHtml}
                    </div></div>
                <div>
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 bg-white">${escapeHtml(item.description || '')}</textarea>
                </div></div>
                </div>
            </div>`;
    }

    window.getHolidayLeaveRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(window.formatLocalizedDate?window.formatLocalizedDate(item.date):(item.date||'—'))}</td>
            <td class="py-4 px-5 font-mono text-sm">${escapeHtml(item.timeLabel || item.time || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.typeLabel || '—')}</td>
            <td class="py-4 px-5 text-xs text-gray-500">${escapeHtml(item.timezone || 'Asia/Tehran')}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5"><button type="button" onclick="cycleHolidayLeaveStatus(${item.id})" ${item.readOnly?'disabled title="این مورد متعلق به سازمان دیگری است"':'title="تغییر وضعیت"'} class="min-w-[92px] px-3 py-1.5 rounded-full text-xs font-medium disabled:cursor-not-allowed ${statusClass(item.status)}">${escapeHtml(item.status)}</button></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewHolidayLeave(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    ${item.readOnly?'':`<button onclick="toggleHolidayLeaveInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button><button onclick="deleteHolidayLeave(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>`}
                </div>
            </td>`;
    };
    window.getHolidayLeaveEmptyRowHTML = function () {
        return `<tr><td colspan="8" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`;
    };
    window.getHolidayLeaveInlineExpandRowHTML = function (item) {
        return `<td colspan="8" class="p-5 border-t">${window.getHolidayLeaveInlineEditRowHTML(item)}</td>`;
    };
    window.getHolidayLeaveInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineHl' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineHolidayLeave(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleHolidayLeaveInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };
    window.getHolidayLeaveAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن تعطیل / مرخصی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveHolidayLeave()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getHolidayLeaveEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش تعطیل / مرخصی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editHl')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedHolidayLeave(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getHolidayLeaveDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(window.formatLocalizedDate?window.formatLocalizedDate(item.date):(item.date||''))} · ${escapeHtml(item.timeLabel || item.time || '')}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        ${item.readOnly?'':`<button onclick="editHolidayLeave(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>`}
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ</span><span class="font-medium">${escapeHtml(window.formatLocalizedDate?window.formatLocalizedDate(item.date):(item.date||'—'))}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(item.typeLabel || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساعت</span><span class="font-medium">${escapeHtml(item.timeLabel || item.time || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">منطقه زمانی</span><span class="font-medium">${escapeHtml(item.timezone || 'Asia/Tehran')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getHolidayLeavePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF تعطیلات و مرخصی‌ها</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="hlPdfTitle" type="text" value="گزارش تعطیلات و مرخصی‌ها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="hlPdfSubtitle" type="text" value="ثبت تعطیلات شعبه‌ها و مرخصی اعضا" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="hlPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="hlPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="hlPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${(cols || []).map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="hlPdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="hlPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="hlPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="hlPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="hlPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateHolidayLeavesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getHolidayLeavePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
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
