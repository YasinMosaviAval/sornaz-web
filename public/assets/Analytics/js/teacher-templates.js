(function () {
    window.getStaffRowHTML = function (item, statusClass) {
        return `
            <td class="py-4 px-5 font-medium">${item.name}</td>
            <td class="py-4 px-5">${item.typeLabel}</td>
            <td class="py-4 px-5">${item.contractTitle}</td>
            <td class="py-4 px-5">${item.branch}</td>
            <td class="py-4 px-5">${item.startDate}</td>
            <td class="py-4 px-5">${item.endDate}</td>
            <td class="py-4 px-5">${item.price.toLocaleString('fa-IR')} ${item.currency}</td>
            <td class="py-4 px-5">
                <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${item.status}</span>
            </td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewStaff(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6 align-middle">جزئیات</button>
                    <button onclick="toggleStaffInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm leading-6 align-middle">ویرایش</button>
                    <button onclick="deleteStaff(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6 align-middle">حذف</button>
                </div>
            </td>
        `;
    };

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
            const value = option.value ?? option.id ?? '';
            const label = option.label ?? option.name ?? '';
            const isSelected = value === selectedValue ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${isSelected}>${escapeHtml(label)}</option>`;
        }).join('');
    }


    function lessonTypeOptions(selected) {
        return renderOptions((window.lessonTypes || []), selected);
    }
    function lessonLevelOptions(selected) {
        return renderOptions((window.lessonLevels || []), selected);
    }
    function profileVisibilityOptions(selected) {
        return renderOptions((window.profileVisibilities || [
            { value: 'public', label: 'عمومی' },
            { value: 'private', label: 'خصوصی' }
        ]), selected || 'public');
    }
    function getLessonTypeLabel(value) {
        const t = (window.lessonTypes || []).find(x => x.value === value);
        return t ? t.label : (value || '—');
    }
    function getLessonLevelLabel(value) {
        const t = (window.lessonLevels || []).find(x => x.value === value);
        return t ? t.label : (value || '—');
    }

    window.getStaffLessonRowHTML = function (prefix, index, lesson, isFirst) {
        lesson = lesson || {};
        const removeBtn = isFirst
            ? '<button type="button" disabled class="opacity-40 cursor-not-allowed text-gray-400 text-sm px-3 py-2" title="مورد اول قابل حذف نیست">حذف</button>'
            : `<button type="button" onclick="removeStaffLessonRow(this)" class="text-red-500 hover:underline text-sm px-3 py-2">حذف</button>`;
        return `<div data-lesson-row class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end border border-gray-100 rounded-2xl p-3 bg-gray-50/50">
            <div class="sm:col-span-5">
                <label class="block text-xs font-medium mb-1 text-gray-500">نوع درس *</label>
                <select data-lesson-type class="w-full border border-gray-300 rounded-2xl py-3 px-4">${lessonTypeOptions(lesson.type)}</select>
            </div>
            <div class="sm:col-span-5">
                <label class="block text-xs font-medium mb-1 text-gray-500">سطح درس *</label>
                <select data-lesson-level class="w-full border border-gray-300 rounded-2xl py-3 px-4">${lessonLevelOptions(lesson.level)}</select>
            </div>
            <div class="sm:col-span-2 flex justify-end">${removeBtn}</div>
        </div>`;
    };

    function staffLessonsBlock(prefix, item, typeSelectId) {
        const isTeacher = (item && item.type === 'teacher') || false;
        const lessons = (item && item.lessons && item.lessons.length) ? item.lessons : [{ type: 'piano', level: 'beginner' }];
        const rows = lessons.map((l, i) => window.getStaffLessonRowHTML(prefix, i, l, i === 0)).join('');
        return `<div id="${prefix}LessonsBox" class="${isTeacher ? '' : 'hidden'} space-y-3 border border-indigo-100 rounded-2xl p-4 bg-indigo-50/30">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-indigo-700">دروس و سطوح (ویژه استاد)</p>
                    <p class="text-xs text-gray-500 mt-0.5">حداقل یک مورد الزامی است؛ مورد اول قابل حذف نیست.</p>
                </div>
                <button type="button" onclick="addStaffLessonRow('${prefix}')" class="text-sm bg-white border border-indigo-200 text-indigo-700 px-3 py-2 rounded-xl hover:bg-indigo-50">
                    <i class="fas fa-plus ml-1"></i> افزودن درس
                </button>
            </div>
            <div id="${prefix}LessonsContainer" class="space-y-3">${rows}</div>
        </div>`;
    }

    window.getStaffEmptyRowHTML = function () {
        return `<tr><td colspan="9" class="py-12 text-center text-gray-400">هیچ پرسنلی یافت نشد</td></tr>`;
    };

    window.getStaffInlineExpandRowHTML = function (item) {
        return `<td colspan="9" class="p-5 border-t">${window.getStaffInlineEditRowHTML ? window.getStaffInlineEditRowHTML(item) : ''}</td>`;
    };

    window.getStaffInlineEditRowHTML = function (item) {
        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                        <input id="inlineStaffName-${item.id}" type="text" value="${escapeHtml(item.name)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                        <input id="inlineStaffPhone-${item.id}" type="tel" value="${escapeHtml(item.phone)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                        <select id="inlineStaffType-${item.id}" onchange="toggleStaffLessonFields('inlineStaffType-${item.id}', 'inlineStaff${item.id}LessonsBox')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${renderOptions(staffTypes, item.type)}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                        <select id="inlineStaffBranch-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${renderOptions(branches.map(branch => ({ value: branch, label: branch })), item.branch)}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                        <input id="inlineStaffContractTitle-${item.id}" type="text" value="${escapeHtml(item.contractTitle)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                        <select id="inlineStaffCurrency-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${renderOptions(contractCurrencies, item.currencyId)}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                        <input id="inlineStaffPrice-${item.id}" type="number" value="${escapeHtml(item.price)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                        <input id="inlineStaffStartDate-${item.id}" type="date" value="${escapeHtml(item.startDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                        <input id="inlineStaffEndDate-${item.id}" type="date" value="${escapeHtml(item.endDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                        <textarea id="inlineStaffContractDescription-${item.id}" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.contractDescription)}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شروع فعالیت</label>
                        <input id="inlineStaffActivityStart-${item.id}" type="date" value="${escapeHtml(item.activityStart || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نمایش پروفایل</label>
                        <select id="inlineStaffProfileVisibility-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${profileVisibilityOptions(item.profileVisibility)}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="inlineStaffStatus-${item.id}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            ${renderOptions(staffStatuses.map(status => ({ value: status, label: status })), item.status)}
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">نوع قرارداد (تأثیر روی دروس)</label>
                        <p class="text-xs text-gray-400 mb-2">با تغییر نوع به «استاد»، بخش دروس نمایش داده می‌شود.</p>
                    </div>
                </div>
                ${staffLessonsBlock('inlineStaff' + item.id, item)}
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button onclick="saveInlineStaff(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="toggleStaffInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        `;
    };

    window.getStaffPDFModalHTML = function (pdfExportColumns) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">تنظیمات خروجی PDF</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6" style="max-height: calc(100vh - 10rem); overflow-y: auto;">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                                <input id="pdfTitle" type="text" value="گزارش پرسنل آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                                <input id="pdfSubtitle" type="text" value="لیست پرسنل و وضعیت قراردادها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">فرمت صفحه</label>
                                    <select id="pdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="a4">A4</option>
                                        <option value="letter">Letter</option>
                                        <option value="legal">Legal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">جهت صفحه</label>
                                    <select id="pdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="landscape">افقی</option>
                                        <option value="portrait">عمودی</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">متن یادداشت پایین صفحه</label>
                                <input id="pdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="grid grid-cols-1 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        ${pdfExportColumns.map(col => `
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="checkbox" id="pdfCol-${col.field}" value="${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                                ${col.label}
                                            </label>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label>
                                        <input id="pdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label>
                                        <input id="pdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label>
                                        <input id="pdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input id="pdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="pdfIncludeDate" class="text-sm text-gray-700">نمایش تاریخ استخراج در بالای گزارش</label>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button onclick="generateStaffPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getStaffPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const {
            title,
            subtitle,
            footer,
            includeDate,
            date,
            headerColor,
            evenRowColor,
            oddRowColor,
            selectedColumns,
            rowsPerPage,
            totalPages,
            orientation
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
                                        const value = col.field === 'index' ? (pageNumber - 1) * rowsPerPage + index + 1 : (col.field === 'price' ? `${item.price.toLocaleString('fa-IR')} ${item.currency}` : item[col.field]);
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

    window.getStaffAddModalHTML = function () {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">افزودن پرسنل جدید</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                                <input id="staffName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                                <input id="staffPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                                <select id="staffType" onchange="toggleStaffLessonFields('staffType', 'staffLessonsBox')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(staffTypes)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                                <select id="staffBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(branches.map(branch => ({ value: branch, label: branch })))}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                                <input id="staffContractTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                                <select id="staffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(contractCurrencies)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                                <input id="staffPrice" type="number" value="5000000" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                                <input id="staffStartDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                                <input id="staffEndDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                                <textarea id="staffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شروع فعالیت</label>
                                <input id="staffActivityStart" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نمایش پروفایل</label>
                                <select id="staffProfileVisibility" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${profileVisibilityOptions('public')}
                                </select>
                            </div>
                        </div>
                        ${staffLessonsBlock('staff', { type: 'teacher', lessons: [{ type: 'piano', level: 'beginner' }] })}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveStaff()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره پرسنل</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getStaffDetailsModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <div>
                            <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                            <p class="text-sm text-gray-500 mt-1">کد پرسنل: #${item.id}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="editStaff(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                            <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات شخصی</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره تماس</span><span class="font-medium">${escapeHtml(item.phone)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع پرسنل</span><span class="font-medium">${escapeHtml(item.typeLabel)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شروع فعالیت</span><span class="font-medium">${escapeHtml(item.activityStart || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نمایش پروفایل</span><span class="font-medium">${item.profileVisibility === 'private' ? 'خصوصی' : 'عمومی'}</span></div>
                            </div>
                        </div>
                        ${item.type === 'teacher' ? `
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-music"></i> دروس و سطوح</h3>
                            <div class="flex flex-wrap gap-2">
                                ${(item.lessons && item.lessons.length ? item.lessons : []).map(l =>
                                    `<span class="px-3 py-1.5 rounded-full text-xs bg-indigo-50 text-indigo-700 border border-indigo-100">${escapeHtml(getLessonTypeLabel(l.type))} · ${escapeHtml(getLessonLevelLabel(l.level))}</span>`
                                ).join('') || '<span class="text-sm text-gray-400">درسی ثبت نشده</span>'}
                            </div>
                        </div>` : ''}
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-file-contract"></i> اطلاعات قرارداد</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عنوان قرارداد</span><span class="font-medium">${escapeHtml(item.contractTitle)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branch)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ شروع</span><span class="font-medium">${escapeHtml(item.startDate)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ خاتمه</span><span class="font-medium">${escapeHtml(item.endDate)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مبلغ قرارداد</span><span class="font-medium">${item.price.toLocaleString('fa-IR')} ${escapeHtml(item.currency)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">واحد پول</span><span class="font-medium">${escapeHtml(item.currency)}</span></div>
                            </div>
                            <div class="mt-4 border rounded-2xl p-5 bg-gray-50 text-sm text-gray-700">
                                <div class="font-medium mb-2">شرح قرارداد</div>
                                <div class="leading-relaxed">${escapeHtml(item.contractDescription)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getStaffEditModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">ویرایش پرسنل</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                                <input id="editStaffName" type="text" value="${escapeHtml(item.name)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                                <input id="editStaffPhone" type="tel" value="${escapeHtml(item.phone)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                                <select id="editStaffType" onchange="toggleStaffLessonFields('editStaffType', 'editStaffLessonsBox')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(staffTypes, item.type)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شعبه آموزشگاه *</label>
                                <select id="editStaffBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(branches.map(branch => ({ value: branch, label: branch })), item.branch)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                                <input id="editStaffContractTitle" type="text" value="${escapeHtml(item.contractTitle)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                                <select id="editStaffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(contractCurrencies, item.currencyId)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                                <input id="editStaffPrice" type="number" value="${escapeHtml(item.price)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                                <input id="editStaffStartDate" type="date" value="${escapeHtml(item.startDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                                <input id="editStaffEndDate" type="date" value="${escapeHtml(item.endDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                                <textarea id="editStaffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.contractDescription)}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شروع فعالیت</label>
                                <input id="editStaffActivityStart" type="date" value="${escapeHtml(item.activityStart || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">نمایش پروفایل</label>
                                <select id="editStaffProfileVisibility" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${profileVisibilityOptions(item.profileVisibility)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">وضعیت</label>
                                <select id="editStaffStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(staffStatuses.map(status => ({ value: status, label: status })), item.status)}
                                </select>
                            </div>
                        </div>
                        ${staffLessonsBlock('editStaff', item)}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveEditedStaff(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };
})();
