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
            active: 'bg-green-100 text-green-700', inactive: 'bg-red-100 text-red-700',
            pending: 'bg-yellow-100 text-yellow-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }
    const statusLabels = { pending: 'در انتظار تأیید', active: 'فعال', inactive: 'غیرفعال' };
    function formatLessonStartDate(value) {
        const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) return value || '—';
        const year = Number(match[1]), month = Number(match[2]), day = Number(match[3]);
        const today = new Date();
        const elapsedDays = Math.max(0, Math.floor(
            (Date.UTC(today.getFullYear(), today.getMonth(), today.getDate()) - Date.UTC(year, month - 1, day)) / 86400000
        ));
        let totalMonths = (today.getFullYear() - year) * 12 + (today.getMonth() + 1 - month);
        if (today.getDate() < day) totalMonths--;
        totalMonths = Math.max(0, totalMonths);
        const fullYears = Math.floor(totalMonths / 12);
        const date = `${match[1]}/${match[2]}/${match[3]}`;
        const isEnglish = String(window.adminLocale || document.documentElement.lang || 'fa').toLowerCase().startsWith('en');
        if (isEnglish) {
            const duration = elapsedDays === 0
                ? 'Today'
                : totalMonths < 1
                    ? `${elapsedDays} ${elapsedDays === 1 ? 'day' : 'days'}`
                    : fullYears >= 1
                        ? `${fullYears} ${fullYears === 1 ? 'year' : 'years'}`
                        : `${totalMonths} ${totalMonths === 1 ? 'month' : 'months'}`;
            return `${date}  (${duration})`;
        }
        const duration = elapsedDays === 0
            ? 'امروز'
            : totalMonths < 1
                ? `${elapsedDays} روز`
                : fullYears >= 1 ? `${fullYears} سال` : `${totalMonths} ماه`;
        return `(${duration})  ${date}`;
    }

    window.getLessonRowHTML = function (item, levelTitle) {
        const sc = statusBadge(item.status);
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.title)}</td>
            <td class="py-4 px-5">${escapeHtml(levelTitle || '—')}</td>
            <td class="py-4 px-5 text-center" dir="ltr">${escapeHtml(formatLessonStartDate(item.start_date))}</td>
            <td class="py-4 px-5">${item.is_primary ? '<span class="px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">اصلی</span>' : '—'}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${item.canChangeStatus === false
                ? `<span class="px-3 py-1 rounded-full text-xs ${sc}">${escapeHtml(statusLabels[item.status] || item.status || 'در انتظار تأیید')}</span>`
                : `<button type="button" onclick="cycleLessonStatus(${item.id})" class="px-3 py-1 rounded-full text-xs ${sc}" title="برای تغییر وضعیت کلیک کنید">${escapeHtml(statusLabels[item.status] || item.status || 'در انتظار تأیید')}</button>`}</td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewLesson(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    ${item.canChangeStatus === false ? '' : `<button data-lesson-inline-edit-id="${item.id}" onclick="toggleLessonInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="deleteLesson(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>`}
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
        const organizations = (window.branchOfferingData?.organizations || []).filter(function (b) { return !b.read_only; }).map(function (b) { return { value: b.user_id, label: b.name }; });
        const fixedOrganization = window.branchOfferingData?.organization_selection === 'fixed';
        const statusMode = window.branchOfferingData?.lesson_status_mode || 'pending';
        const lessons = (typeof window.sampleLessons !== 'undefined' ? window.sampleLessons : []).map(function (i) { return { value: i.id, label: i.title }; });
        const levels = (typeof window.branchLessonLevels !== 'undefined' ? window.branchLessonLevels : []).filter(function (l) { return !l.type || l.type === 'learning'; }).map(function (l) { return { value: l.level_id, label: l.title }; });
        const statuses = [{ value: 'active', label: 'فعال' }, { value: 'inactive', label: 'غیرفعال' }];
        const selectedOrganizationUserId = item.organization_user_id || item.user_id || organizations[0]?.value;
        const hasPrimary = (window.allUserLessons || []).some(function (lesson) { return lesson.user_id === selectedOrganizationUserId && lesson.is_primary && lesson.id !== item.id; });
        const primaryDisabled = hasPrimary && !item.is_primary;
        const prohibitedCursor = `url(data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20width=%2724%27%20height=%2724%27%3E%3Ccircle%20cx=%2712%27%20cy=%2712%27%20r=%279%27%20fill=%27white%27%20stroke=%27%23dc2626%27%20stroke-width=%273%27/%3E%3Cpath%20d=%27M6%2018L18%206%27%20stroke=%27%23dc2626%27%20stroke-width=%273%27/%3E%3C/svg%3E) 12 12, not-allowed`;
        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                ${fixedOrganization ? '' : `<div>
                    <label class="block text-sm font-medium mb-2">سازمان *</label>
                    <select id="${id('Organization')}" onchange="updateLessonPrimaryAvailability('${id('Organization')}','${id('Primary')}',${item.id || 0})" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(organizations, selectedOrganizationUserId)}
                    </select>
                </div>`}
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
                    <label class="block text-sm font-medium mb-2">زمان شروع *</label>
                    <input id="${id('StartDate')}" type="date" value="${escapeHtml(item.start_date || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    ${statusMode === 'pending'
                        ? `<input type="text" value="در انتظار تأیید" disabled class="w-full cursor-not-allowed rounded-2xl border border-gray-200 bg-gray-100 py-3.5 px-5 text-gray-500">`
                        : `<select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${renderOptions(statuses, item.status === 'inactive' ? 'inactive' : 'active')}</select>`}
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 pb-3 text-sm ${primaryDisabled ? 'text-gray-400' : ''}" ${primaryDisabled ? `style="cursor:${prohibitedCursor}"` : ''}>
                        <input type="checkbox" id="${id('Primary')}" class="h-4 w-4 ${primaryDisabled ? 'border-gray-300 bg-gray-200 accent-gray-400' : ''}" ${item.is_primary ? 'checked' : ''} ${primaryDisabled ? 'disabled' : ''}>
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
                        ${item.canChangeStatus === false ? '' : `<button onclick="editLesson(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>`}
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    ${item.summary ? `<p class="text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                    ${item.description ? `<p class="text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span class="font-medium">${escapeHtml(levelTitle || '—')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">زمان شروع</span><span class="font-medium">${escapeHtml(formatLessonStartDate(item.start_date))}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">اصلی</span><span class="font-medium">${item.is_primary ? 'بله' : 'خیر'}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(statusLabels[item.status] || item.status || 'در انتظار')}</span></div>
                        <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سازمان</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
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
