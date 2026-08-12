(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function renderOptions(options, selectedValue) {
        return (options || []).map(function (option) {
            const value = option.value ?? option.id ?? option.level_id ?? option.name ?? option.title ?? option;
            const label = option.label ?? option.name ?? option.title ?? option;
            const isSelected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${isSelected}>${escapeHtml(label)}</option>`;
        }).join('');
    }
    function statusBadge(status) {
        return {
            'فعال': 'bg-green-100 text-green-700',
            'غیرفعال': 'bg-gray-100 text-gray-600',
            'در انتظار': 'bg-yellow-100 text-yellow-700',
            'حذف‌شده': 'bg-red-100 text-red-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    window.getLessonRowHTML = function (item, levelTitle) {
        const sc = statusBadge(item.status);
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5">${escapeHtml(levelTitle || '—')}</td>
            <td class="py-4 px-5">${item.years_of_experience ?? '—'}</td>
            <td class="py-4 px-5">${item.is_primary ? '<span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">اصلی</span>' : '—'}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${sc}">${escapeHtml(item.status || 'فعال')}</span></td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewLesson(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleLessonInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteLesson(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>`;
    };
    window.getLessonEmptyRowHTML = function () {
        return `<tr><td colspan="7" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`;
    };
    window.getLessonInlineExpandRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getLessonInlineEditRowHTML ? window.getLessonInlineEditRowHTML(item) : ''}</td>`;
    };

    function formFields(item, prefix) {
        const id = function (n) { return prefix ? prefix + n : 'lesn' + n; };
        const branches = (typeof allBranches !== 'undefined' ? allBranches : []).map(function (b) { return { value: b.id, label: b.name }; });
        const lessons = (typeof window.sampleLessons !== 'undefined' ? window.sampleLessons : []).map(function (i) { return { value: i.id, label: i.title }; });
        const levels = (typeof window.branchLessonLevels !== 'undefined' ? window.branchLessonLevels : []).map(function (l) { return { value: l.level_id, label: l.title + (l.type ? ' (' + l.type + ')' : '') }; });
        const statuses = ['فعال', 'غیرفعال', 'در انتظار', 'حذف‌شده'].map(function (s) { return { value: s, label: s }; });
        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">درس *</label>
                    <select id="${id('Select')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(lessons, item.lesson_id)}
                    </select>
                    <button type="button" onclick="promptAddLessonType()" class="text-sm text-indigo-600 mt-1">+ درس جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سطح</label>
                    <select id="${id('Level')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(levels, item.level_id)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سابقه (سال)</label>
                    <input id="${id('Years')}" type="number" min="0" value="${escapeHtml(item.years_of_experience ?? 1)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm pb-3">
                        <input type="checkbox" id="${id('Primary')}" ${item.is_primary ? 'checked' : ''}>
                        درس اصلی (فقط یکی برای هر کاربر)
                    </label>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">خلاصه</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Desc')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>`;
    }

    window.getLessonInlineEditRowHTML = function (item) {
        return `<div class="space-y-6">
            ${formFields(item, 'inlineLesn' + item.id)}
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                <button onclick="saveInlineLesson(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                <button onclick="toggleLessonInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
            </div>
        </div>`;
    };
    window.getLessonAddModalHTML = function () {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">افزودن درس</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields({}, '')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveLesson()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getLessonEditModalHTML = function (item) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="px-8 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold">ویرایش درس</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                </div>
                <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                    ${formFields(item, 'editLesn')}
                    <div class="flex gap-4 pt-4">
                        <button onclick="saveEditedLesson(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                        <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getLessonDetailsModalHTML = function (item, levelTitle) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <div>
                        <h2 class="text-2xl font-bold">${escapeHtml(item.title)}</h2>
                        <p class="text-sm text-gray-500 mt-1">${escapeHtml(levelTitle || '')} ${item.is_primary ? '— درس اصلی' : ''}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editLesson(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span class="font-medium">${escapeHtml(levelTitle || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سابقه</span><span class="font-medium">${item.years_of_experience ?? 0} سال</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اصلی</span><span class="font-medium">${item.is_primary ? 'بله' : 'خیر'}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status || 'فعال')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getLessonPDFModalHTML = function (cols) {
        return `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()">
            <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                    <h2 class="text-2xl font-bold">تنظیمات خروجی PDF درسها</h2>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
                <div class="p-8 space-y-6" style="max-height:calc(100vh - 10rem);overflow-y:auto;">
                    <input id="lesnPdfTitle" type="text" value="گزارش درسهای آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <input id="lesnPdfSubtitle" type="text" value="لیست درسها، سطح و وضعیت" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-5">
                        <select id="lesnPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="a4">A4</option><option value="letter">Letter</option></select>
                        <select id="lesnPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="landscape">افقی</option><option value="portrait">عمودی</option></select>
                    </div>
                    <input id="lesnPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    <div class="grid grid-cols-2 gap-2">
                        ${cols.map(function (c) { return `<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" id="lesnPdfCol-${c.field}" checked> ${c.label}</label>`; }).join('')}
                    </div>
                    <div class="grid grid-cols-3 gap-5">
                        <input id="lesnPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="lesnPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border rounded-2xl p-2">
                        <input id="lesnPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border rounded-2xl p-2">
                    </div>
                    <label class="flex items-center gap-3"><input id="lesnPdfIncludeDate" type="checkbox" checked> <span class="text-sm">نمایش تاریخ استخراج</span></label>
                    <div class="flex gap-4 pt-4">
                        <button onclick="generateLessonsPDF()" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl">ایجاد PDF</button>
                        <button onclick="closeModal()" class="flex-1 border py-4 rounded-2xl">انصراف</button>
                    </div>
                </div>
            </div>
        </div>`;
    };
    window.getLessonPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const o = options;
        return `<div style="width:100%;padding:24px;background:#fff;direction:rtl;">
            ${isFirstPage ? `<h1 style="margin:0 0 6px;font-size:28px;font-weight:700;">${escapeHtml(o.title)}</h1>
            <p style="margin:0 0 16px;color:#4b5563;font-size:14px;">${escapeHtml(o.subtitle)}</p>
            ${o.includeDate ? `<p style="margin:0 0 16px;color:#6b7280;font-size:12px;">تاریخ استخراج: ${escapeHtml(o.date)}</p>` : ''}` : ''}
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:${o.headerColor};"><tr>
                    ${o.selectedColumns.map(function (c) { return `<th style="padding:12px 14px;text-align:right;font-weight:600;">${escapeHtml(c.label)}</th>`; }).join('')}
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
