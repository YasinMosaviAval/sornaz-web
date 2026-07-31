(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option.name ?? option.title ?? option;
            const label = option.label ?? option.name ?? option.title ?? option;
            const selected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${selected}>${escapeHtml(label)}</option>`;
        }).join('');
    }
    function statusClass(status) {
        return {
            'فعال': 'bg-green-100 text-green-700',
            'غیرفعال': 'bg-gray-100 text-gray-600',
            'تأیید شده': 'bg-blue-100 text-blue-700',
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700',
            'رد شده': 'bg-red-100 text-red-700',
            'حذف‌شده': 'bg-gray-200 text-gray-500',
            'پایان یافته': 'bg-purple-100 text-purple-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }
    function typeClass(type) {
        return type === 'خصوصی' ? 'bg-indigo-100 text-indigo-700'
            : type === 'گروهی' ? 'bg-amber-100 text-amber-700'
            : 'bg-emerald-100 text-emerald-700';
    }

    window.getScheduleRowHTML = function (item) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title || item.day)}</td>
            <td class="py-4 px-5">${escapeHtml(item.day)}</td>
            <td class="py-4 px-5 font-mono text-sm">${escapeHtml(item.startTime || '')} - ${escapeHtml(item.endTime || '')}</td>
            <td class="py-4 px-5">${escapeHtml(item.student)}</td>
            <td class="py-4 px-5">${escapeHtml(item.teacher)}</td>
            <td class="py-4 px-5">${escapeHtml(item.instrument)}</td>
            <td class="py-4 px-5">${escapeHtml(item.classroom)}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${typeClass(item.type)}">${escapeHtml(item.type)}</span></td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass(item.status)}">${escapeHtml(item.status)}</span></td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewSchedule(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleScheduleInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteSchedule(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>`;
    };

    window.getScheduleEmptyRowHTML = function () {
        return `<tr><td colspan="10" class="py-12 text-center text-gray-400">برنامه‌ای یافت نشد</td></tr>`;
    };

    window.getScheduleInlineExpandRowHTML = function (item) {
        return `<td colspan="10" class="p-5 border-t">${window.getScheduleInlineEditRowHTML ? window.getScheduleInlineEditRowHTML(item) : ''}</td>`;
    };

    function formFields(item, prefix) {
        const id = function (n) { return prefix ? prefix + n : 'sch' + n; };
        const branches = (typeof getScheduleBranches === 'function' ? getScheduleBranches() : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const days = (typeof scheduleDays !== 'undefined' ? scheduleDays : []).map(function (d) { return { value: d, label: d }; });
        const types = (typeof scheduleTypes !== 'undefined' ? scheduleTypes : []).map(function (t) { return { value: t, label: t }; });
        const statuses = (typeof scheduleStatuses !== 'undefined' ? scheduleStatuses : []).map(function (s) { return { value: s, label: s }; });
        const students = (typeof getScheduleStudentOptions === 'function' ? getScheduleStudentOptions() : []);
        const teachers = (typeof getScheduleTeacherOptions === 'function' ? getScheduleTeacherOptions() : []);
        const instruments = (typeof getScheduleInstrumentOptions === 'function' ? getScheduleInstrumentOptions() : []);
        const classrooms = (typeof getScheduleClassroomOptions === 'function' ? getScheduleClassroomOptions() : []);

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان</label>
                    <input id="${id('Title')}" type="text" value="${escapeHtml(item.title || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="عنوان برنامه">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">روز *</label>
                    <select id="${id('Day')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(days, item.day)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ساعت شروع *</label>
                    <input id="${id('StartTime')}" type="time" value="${escapeHtml(item.startTime || '10:00')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ساعت پایان *</label>
                    <input id="${id('EndTime')}" type="time" value="${escapeHtml(item.endTime || '11:00')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع کلاس</label>
                    <select id="${id('Type')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(types, item.type || 'خصوصی')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">هنرجو *</label>
                    <select id="${id('Student')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب هنرجو</option>
                        ${renderOptions(students, item.studentId || item.student || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">استاد *</label>
                    <select id="${id('Teacher')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب استاد</option>
                        ${renderOptions(teachers, item.teacherId || item.teacher || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ساز</label>
                    <select id="${id('Instrument')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب ساز</option>
                        ${renderOptions(instruments, item.instrumentId || item.instrument || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">کلاس فیزیکی</label>
                    <select id="${id('Classroom')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب کلاس</option>
                        ${renderOptions(classrooms, item.classroomId || item.classroom || '')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getScheduleInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineSch' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineSchedule(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleScheduleInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };

    window.getScheduleAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن برنامه زمانی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveSchedule()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getScheduleEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش برنامه زمانی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editSch')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedSchedule(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getScheduleDetailsModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.title || (item.day + ' ' + (item.startTime || '')))}</h2>
                        <p class="text-sm text-gray-500 mt-1">کد برنامه: #${item.id}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editSchedule(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">روز</span><span class="font-medium">${escapeHtml(item.day)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساعت</span><span class="font-medium">${escapeHtml(item.startTime || '')} - ${escapeHtml(item.endTime || '')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span class="font-medium">${escapeHtml(item.type)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">هنرجو</span><span class="font-medium">${escapeHtml(item.student)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">استاد</span><span class="font-medium">${escapeHtml(item.teacher)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساز</span><span class="font-medium">${escapeHtml(item.instrument)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کلاس</span><span class="font-medium">${escapeHtml(item.classroom)}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getSchedulePDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF برنامه زمانی</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="schedulePdfTitle" type="text" value="گزارش برنامه زمانی کلاس‌ها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="schedulePdfSubtitle" type="text" value="لیست جلسات، اساتید و هنرجویان" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="schedulePdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="schedulePdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="schedulePdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${cols.map(function (c) {
                            return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="schedulePdfCol-${c.field}" checked> ${c.label}</label>`;
                        }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="schedulePdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="schedulePdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="schedulePdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="schedulePdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateSchedulesPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };

    window.getSchedulePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
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
