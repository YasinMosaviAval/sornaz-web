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

    function equipmentListText(equipment) {
        if (!Array.isArray(equipment) || !equipment.length) return '—';
        return equipment.map(item => `${item.name} (${item.qty})`).join('، ');
    }

    window.getClassroomRowHTML = function (item, statusClass) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.typeLabel || item.type || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${item.capacity} نفر</td>
            <td class="py-4 px-5 text-sm text-gray-600 max-w-[220px] truncate" title="${escapeHtml(equipmentListText(item.equipment))}">${escapeHtml(equipmentListText(item.equipment))}</td>
            <td class="py-4 px-5">
                <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${escapeHtml(item.status)}</span>
            </td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewClassroom(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6">جزئیات</button>
                    <button onclick="toggleClassroomInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm leading-6">ویرایش</button>
                    <button onclick="deleteClassroom(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6">حذف</button>
                </div>
            </td>
        `;
    };

    window.getClassroomEmptyRowHTML = function () {
        return `<tr><td colspan="7" class="py-12 text-center text-gray-400">هیچ کلاسی یافت نشد</td></tr>`;
    };

    window.getClassroomInlineExpandRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getClassroomInlineEditRowHTML ? window.getClassroomInlineEditRowHTML(item) : ''}</td>`;
    };

    window.getClassroomEquipmentFieldHTML = function (item = {}) {
        return `
            <div class="border border-gray-200 rounded-2xl p-4 mb-3 equipment-item">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="sm:col-span-2">
                        <label class="text-xs text-gray-500 mb-1 block">نام تجهیز</label>
                        <input type="text" class="equip-name w-full border border-gray-300 rounded-2xl py-3 px-4" value="${escapeHtml(item.name || '')}" placeholder="مثلاً پیانو">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">تعداد</label>
                        <input type="number" min="1" class="equip-qty w-full border border-gray-300 rounded-2xl py-3 px-4" value="${escapeHtml(item.qty || 1)}">
                    </div>
                    <div class="sm:col-span-3 flex justify-end">
                        <button type="button" onclick="window.removeClassroomEquipmentField(this)" class="text-red-500 hover:text-red-700 text-sm">حذف تجهیز</button>
                    </div>
                </div>
            </div>`;
    };

    function classroomFormFields(item, prefix) {
        const id = (name) => prefix ? `${prefix}${name}` : `classroom${name}`;
        const branches = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => ({ value: b.id, label: b.name }));
        const types = (typeof allClassroomTypes !== 'undefined' ? allClassroomTypes : []).map(t => ({ value: t.name, label: t.name }));
        const statuses = (typeof classroomStatuses !== 'undefined' ? classroomStatuses : ['فعال', 'تعمیر', 'غیرفعال']).map(s => ({ value: s, label: s }));
        const equipment = (item.equipment && item.equipment.length) ? item.equipment : [{}];
        const equipContainer = prefix ? `${prefix}EquipmentContainer` : 'classroomEquipmentContainer';
        const equipHtml = equipment.map(e => window.getClassroomEquipmentFieldHTML(e)).join('');

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام کلاس *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع کلاس *</label>
                    <select id="${id('Type')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(types, item.type || (types[0] && types[0].value))}
                    </select>
                    <button type="button" onclick="promptAddClassroomType()" class="text-sm text-indigo-600 mt-1">+ نوع جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">ظرفیت</label>
                    <input id="${id('Capacity')}" type="number" min="1" value="${escapeHtml(item.capacity ?? 8)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'فعال')}
                    </select>
                </div>
            </div>
            <div class="mt-5">
                <label class="block text-sm font-medium mb-2">تجهیزات</label>
                <div id="${equipContainer}">${equipHtml}</div>
                <button type="button" onclick="addClassroomEquipmentField('${equipContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن تجهیز</button>
            </div>
        `;
    }

    window.getClassroomInlineEditRowHTML = function (item) {
        return `
            <div class="space-y-6">
                ${classroomFormFields(item, `inlineClassroom${item.id}`)}
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button onclick="saveInlineClassroom(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="toggleClassroomInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        `;
    };

    window.getClassroomAddModalHTML = function () {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">افزودن کلاس جدید</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${classroomFormFields({}, '')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveClassroom()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره کلاس</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getClassroomEditModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">ویرایش کلاس</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${classroomFormFields(item, 'editClassroom')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveEditedClassroom(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getClassroomDetailsModalHTML = function (item) {
        const equipRows = (item.equipment || []).length
            ? item.equipment.map(e => `
                <div class="flex justify-between border-b pb-2 text-sm">
                    <span>${escapeHtml(e.name)}</span>
                    <span class="font-medium">${escapeHtml(e.qty)} عدد</span>
                </div>`).join('')
            : '<p class="text-sm text-gray-400">تجهیزی ثبت نشده</p>';

        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-5xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <div>
                            <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                            <p class="text-sm text-gray-500 mt-1">کد کلاس: #${item.id}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="editClassroom(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                            <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-door-open"></i> اطلاعات کلاس</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع کلاس</span><span class="font-medium">${escapeHtml(item.typeLabel || item.type || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ظرفیت</span><span class="font-medium">${item.capacity} نفر</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-tools"></i> تجهیزات</h3>
                            <div class="space-y-2">${equipRows}</div>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getClassroomPDFModalHTML = function (pdfExportColumns) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">تنظیمات خروجی PDF کلاس‌ها</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6" style="max-height: calc(100vh - 10rem); overflow-y: auto;">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                                <input id="classroomPdfTitle" type="text" value="گزارش کلاس‌های فیزیکی" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                                <input id="classroomPdfSubtitle" type="text" value="لیست کلاس‌ها، تجهیزات و وضعیت" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">فرمت صفحه</label>
                                    <select id="classroomPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="a4">A4</option>
                                        <option value="letter">Letter</option>
                                        <option value="legal">Legal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">جهت صفحه</label>
                                    <select id="classroomPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="landscape">افقی</option>
                                        <option value="portrait">عمودی</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">متن یادداشت پایین صفحه</label>
                                <input id="classroomPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label>
                                <div class="grid grid-cols-2 gap-2">
                                    ${pdfExportColumns.map(col => `
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" id="classroomPdfCol-${col.field}" value="${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                            ${col.label}
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label>
                                    <input id="classroomPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label>
                                    <input id="classroomPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label>
                                    <input id="classroomPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input id="classroomPdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="classroomPdfIncludeDate" class="text-sm text-gray-700">نمایش تاریخ استخراج در بالای گزارش</label>
                            </div>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button onclick="generateClassroomsPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getClassroomPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
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
                                        let value;
                                        if (col.field === 'index') value = (pageNumber - 1) * rowsPerPage + index + 1;
                                        else if (col.field === 'equipment') value = equipmentListText(item.equipment);
                                        else if (col.field === 'capacity') value = `${item.capacity} نفر`;
                                        else value = item[col.field];
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
