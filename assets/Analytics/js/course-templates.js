(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderOptions(options, selectedValue) {
        return options.map(option => {
            const value = option.value ?? option.id ?? option.name ?? option;
            const label = option.label ?? option.name ?? option;
            const isSelected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${isSelected}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    window.getCourseRowHTML = function (item, statusClass) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.level || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.instrument)}</td>
            <td class="py-4 px-5">${item.capacity}</td>
            <td class="py-4 px-5">${item.enrolled}</td>
            <td class="py-4 px-5">
                <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${escapeHtml(item.status)}</span>
            </td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewCourse(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6">جزئیات</button>
                    <button type="button" data-course-action="inline-edit" data-course-id="${item.id}" data-no-inline-edit class="text-gray-500 hover:text-indigo-600 text-sm leading-6">ویرایش</button>
                    <button onclick="deleteCourse(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6">حذف</button>
                </div>
            </td>
        `;
    };

    window.getCourseEmptyRowHTML = function () {
        return `<tr><td colspan="8" class="py-12 text-center text-gray-400">هیچ دوره‌ای یافت نشد</td></tr>`;
    };

    window.getCourseInlineExpandRowHTML = function (item) {
        return `<td colspan="8" class="p-5 border-t">${window.getCourseInlineEditRowHTML ? window.getCourseInlineEditRowHTML(item) : ''}</td>`;
    };

    function courseFormFields(item, prefix) {
        const id = (name) => prefix ? `${prefix}${name}` : `course${name}`;
        const branches = (window.courseBranches || []).map(b => ({ value: b.id, label: b.name }));
        const levels = (window.allCourseLevels || []).map(l => ({ value: l.id, label: l.name }));
        const branchId = item.branchId || '';
        const instruments = (window.courseLessons || []).filter(i => i.branchId == branchId).map(i => ({ value: i.id, label: i.name }));
        const statuses = [{value:'pending',label:'در انتظار'},{value:'open',label:'باز'},{value:'ongoing',label:'در حال برگزاری'},{value:'finished',label:'پایان‌یافته'}];

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام دوره *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">سطح دوره *</label>
                    <select id="${id('Level')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(levels, item.level_id || (levels[0] && levels[0].value))}
                    </select>
                    <button type="button" onclick="promptAddCourseLevel()" class="text-sm text-indigo-600 mt-1">+ سطح جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" onchange="refreshCourseLessons('${prefix}')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">شعبه را انتخاب کنید</option>${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ساز / تخصص</label>
                    <select id="${id('Instrument')}" ${branchId?'':'disabled'} class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 disabled:bg-gray-100">
                        <option value="">درس / تخصص را انتخاب کنید</option>${renderOptions(instruments, item.lesson_id)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ظرفیت</label>
                    <input id="${id('Capacity')}" type="number" min="1" value="${escapeHtml(item.capacity ?? 10)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status_code || 'pending')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مدرس</label>
                    <input id="${id('Teacher')}" type="text" value="${escapeHtml(item.teacher || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" placeholder="نام مدرس">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">خلاصه دوره</label>
                    <input id="${id('Summary')}" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">توضیحات</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>
        `;
    }

    window.getCourseInlineEditRowHTML = function (item) {
        return `
            <div class="space-y-6">
                ${courseFormFields(item, `inlineCourse${item.id}`)}
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button onclick="saveInlineCourse(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="toggleCourseInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        `;
    };

    window.getCourseAddModalHTML = function () {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">افزودن دوره جدید</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${courseFormFields({}, '')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveCourse()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره دوره</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getCourseEditModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">ویرایش دوره</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${courseFormFields(item, 'editCourse')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveEditedCourse(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getCourseDetailsModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <div>
                            <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                            <p class="text-sm text-gray-500 mt-1">کد دوره: #${item.id}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" data-course-action="edit" data-course-id="${item.id}" data-no-inline-edit class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                            <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-graduation-cap"></i> اطلاعات دوره</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام دوره</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سطح</span><span class="font-medium">${escapeHtml(item.level || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ساز / تخصص</span><span class="font-medium">${escapeHtml(item.instrument)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ظرفیت</span><span class="font-medium">${item.capacity}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ثبت‌نام‌شده</span><span class="font-medium">${item.enrolled}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مدرس</span><span class="font-medium">${escapeHtml(item.teacher || '—')}</span></div>
                            </div>
                        </div>
                        ${item.description ? `
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">توضیحات</h3>
                            <div class="border rounded-2xl p-5 bg-gray-50 text-sm text-gray-700 leading-relaxed">${escapeHtml(item.description)}</div>
                        </div>` : ''}
                    </div>
                </div>
            </div>`;
    };

    window.getCoursePDFModalHTML = function (pdfExportColumns) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">تنظیمات خروجی PDF دوره‌ها</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6" style="max-height: calc(100vh - 10rem); overflow-y: auto;">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                                <input id="coursePdfTitle" type="text" value="گزارش دوره‌های آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                                <input id="coursePdfSubtitle" type="text" value="لیست دوره‌ها، سطح و وضعیت ثبت‌نام" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">فرمت صفحه</label>
                                    <select id="coursePdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="a4">A4</option>
                                        <option value="letter">Letter</option>
                                        <option value="legal">Legal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">جهت صفحه</label>
                                    <select id="coursePdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="landscape">افقی</option>
                                        <option value="portrait">عمودی</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">متن یادداشت پایین صفحه</label>
                                <input id="coursePdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label>
                                <div class="grid grid-cols-2 gap-2">
                                    ${pdfExportColumns.map(col => `
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" id="coursePdfCol-${col.field}" value="${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                            ${col.label}
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label>
                                    <input id="coursePdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label>
                                    <input id="coursePdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label>
                                    <input id="coursePdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input id="coursePdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="coursePdfIncludeDate" class="text-sm text-gray-700">نمایش تاریخ استخراج در بالای گزارش</label>
                            </div>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button onclick="generateCoursesPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getCoursePDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const {
            title, subtitle, footer, includeDate, date,
            headerColor, evenRowColor, oddRowColor,
            selectedColumns, rowsPerPage, totalPages
        } = options;

        return `
            <div style="width:100%; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(15,23,42,.08); background: #fff;">
                ${isFirstPage ? `
                <div style="text-align: right; direction: rtl;">
                    <h1 style="margin: 0 0 6px; font-size: 28px; font-weight: 700;">${escapeHtml(title)}</h1>
                    <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px;">${escapeHtml(subtitle)}</p>
                    ${includeDate ? `<p style="margin: 0 0 16px; color: #6b7280; font-size: 12px;">تاریخ استخراج: ${escapeHtml(date)}</p>` : ''}
                </div>
                ` : ''}
                <div style="width: 100%; overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse; direction: rtl;">
                        <thead style="background: ${headerColor}; color: #000000;">
                            <tr>
                                ${selectedColumns.map(col => `<th style="padding: 12px 14px; text-align: right; font-weight: 600;">${escapeHtml(col.label)}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map((item, index) => `
                                <tr style="background: ${index % 2 === 0 ? evenRowColor : oddRowColor};">
                                    ${selectedColumns.map(col => {
                                        const value = col.field === 'index'
                                            ? (pageNumber - 1) * rowsPerPage + index + 1
                                            : item[col.field];
                                        return `<td style="padding: 12px 14px; text-align: right;">${escapeHtml(value)}</td>`;
                                    }).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                ${isFirstPage && footer ? `<p style="margin-top: 16px; color: #6b7280; font-size: 12px;">${escapeHtml(footer)}</p>` : ''}
                <div style="margin-top: 16px; display: flex; justify-content: flex-end; color: #6b7280; font-size: 12px;">صفحه ${pageNumber} / ${totalPages}</div>
            </div>`;
    };
})();
