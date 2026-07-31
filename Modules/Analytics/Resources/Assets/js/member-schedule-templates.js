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
        const id = function (n) { return prefix ? prefix + n : 'ms' + n; };
        const branches = (typeof window.getMemberScheduleBranches === 'function' ? window.getMemberScheduleBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const members = (typeof window.getMemberScheduleMemberOptions === 'function' ? window.getMemberScheduleMemberOptions() : []);
        const roles = (window.memberScheduleRolesList || []).map(function (r) { return { value: r, label: r }; });
        const days = (window.memberScheduleDaysList || []).map(function (d) { return { value: d, label: d }; });
        const statuses = (window.memberScheduleStatusesList || []).map(function (s) { return { value: s, label: s }; });
        const repeats = (window.memberScheduleRepeatList || []).map(function (r) { return { value: r, label: r }; });
        const timezones = (window.memberScheduleTimezoneList || []).map(function (tz) {
            return { value: tz.value, label: tz.label };
        });
        const branchId = item.branchId || (branches[0] && branches[0].value) || 1;
        const repeatVal = item.repeatPeriod || 'هفتگی';
        const showDate = (repeatVal === 'ماهانه' || repeatVal === 'سالانه');
        const slotsHtml = typeof window.buildMemberScheduleTimeSlotsHTML === 'function'
            ? window.buildMemberScheduleTimeSlotsHTML(id('TimeSlots'), branchId, item.slots || [])
            : '';

        return `
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                            onchange="window.refreshMemberScheduleTimeSlots('${id('TimeSlots')}', this.value, [])">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عضو *</label>
                    <select id="${id('Member')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                            onchange="if(this.value==='__new__'){window.promptAddMemberScheduleMember('${id('Member')}');}">
                        <option value="">انتخاب عضو</option>
                        ${renderOptions(members, item.memberId || item.name || '')}
                        <option value="__new__">+ افزودن عضو جدید</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نقش</label>
                    <select id="${id('Role')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(roles, item.role || 'استاد')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">روز *</label>
                    <select id="${id('Day')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(days, item.day || 'شنبه')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">دوره تکرار</label>
                    <select id="${id('Repeat')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                            onchange="window.toggleMemberScheduleRepeatDate('${id('RepeatDateWrap')}', this.value)">
                        ${renderOptions(repeats, repeatVal)}
                    </select>
                </div>
                <div id="${id('RepeatDateWrap')}" class="${showDate ? '' : 'hidden'}">
                    <label class="block text-sm font-medium mb-2">تاریخ مرجع (ماهانه/سالانه)</label>
                    <input id="${id('RepeatDate')}" type="date" value="${escapeHtml(item.repeatDate || '')}"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">منطقه زمانی</label>
                    <select id="${id('Timezone')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(timezones, item.timezone || 'Asia/Tehran')}
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium mb-2">ساعات کاری (هر نیم‌ساعت)</label>
                    <div id="${id('TimeSlots')}" class="border border-gray-200 rounded-2xl p-4 max-h-48 overflow-y-auto">
                        ${slotsHtml}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">ساعات پیاپی به‌صورت یک بازه ذخیره می‌شوند؛ بازه‌های با فاصله به‌صورت آیتم جدا در جدول نمایش داده می‌شوند.</p>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getMemberScheduleRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.role)}</td>
            <td class="py-4 px-5">${escapeHtml(item.day)}</td>
            <td class="py-4 px-5 font-mono text-sm">${escapeHtml(item.timeLabel || item.time || '—')}</td>
            <td class="py-4 px-5 text-sm">${escapeHtml(item.repeatPeriod || 'هفتگی')}</td>
            <td class="py-4 px-5 text-xs text-gray-500">${escapeHtml(item.timezone || 'Asia/Tehran')}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewMemberSchedule(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleMemberScheduleInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteMemberSchedule(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>`;
    };
    window.getMemberScheduleEmptyRowHTML = function () {
        return `<tr><td colspan="9" class="py-12 text-center text-gray-400">زمان‌بندی‌ای یافت نشد</td></tr>`;
    };
    window.getMemberScheduleInlineExpandRowHTML = function (item) {
        return `<td colspan="9" class="p-5 border-t">${window.getMemberScheduleInlineEditRowHTML(item)}</td>`;
    };
    window.getMemberScheduleInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineMs' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineMemberSchedule(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleMemberScheduleInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };
    window.getMemberScheduleAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن زمان‌بندی عضو</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveMemberSchedule()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getMemberScheduleEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش زمان‌بندی عضو</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editMs')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedMemberSchedule(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getMemberScheduleDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(item.role)} · ${escapeHtml(item.day)} · ${escapeHtml(item.timeLabel || item.time || '')}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editMemberSchedule(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نقش</span><span class="font-medium">${escapeHtml(item.role)}</span></div>
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
    window.getMemberSchedulePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF زمان‌بندی اعضا</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="msPdfTitle" type="text" value="گزارش زمان‌بندی اعضا" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="msPdfSubtitle" type="text" value="برنامه زمانی اساتید و پرسنل" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="msPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="msPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="msPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${(cols || []).map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="msPdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="msPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="msPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="msPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="msPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateMemberSchedulesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getMemberSchedulePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
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
