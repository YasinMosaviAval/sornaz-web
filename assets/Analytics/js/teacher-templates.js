(function () {
  window.getStaffRowHTML = function (item, statusClass) {
    return window.AdminPanelTable.cellsHTML([{ html: `${item.name}`, attributes: ` class="py-4 px-5 font-medium"` }, { html: `${item.typeLabel}`, attributes: ` class="py-4 px-5"` }, { html: `${item.contractTitle}`, attributes: ` class="py-4 px-5"` }, { html: `${item.branch}`, attributes: ` class="py-4 px-5"` }, { html: `${item.type === 'teacher' ? escapeHtml(item.lessonName || '—') : '—'}`, attributes: ` class="py-4 px-5"` }, { html: `${item.startDate}`, attributes: ` class="py-4 px-5"` }, { html: `${item.endDate}`, attributes: ` class="py-4 px-5"` }, { html: `${item.price.toLocaleString('fa-IR')} ${item.currency}`, attributes: ` class="py-4 px-5"` }, { html: `
                <button type="button" onclick="cycleStaffStatus(${item.id},this)" class="px-3 py-1 rounded-full text-xs transition ${statusClass}" title="برای تغییر وضعیت کلیک کنید">${item.status}</button>
            `, attributes: ` class="py-4 px-5"` }, { html: `
                <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
                    <button onclick="viewStaff(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6 align-middle">جزئیات</button>
                    <button onclick="toggleStaffInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm leading-6 align-middle">ویرایش</button>
                    <button onclick="deleteStaff(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6 align-middle">حذف</button>
                </div>
            `, attributes: ` class="py-4 px-5 text-left"` }]);
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
    return options
      .map((option) => {
        const value = option.value ?? option.id ?? '';
        const label = option.label ?? option.name ?? '';
        const isSelected = String(value) === String(selectedValue ?? '') ? 'selected' : '';
        return `<option value="${escapeHtml(value)}" ${isSelected}>${escapeHtml(label)}</option>`;
      })
      .join('');
  }

  function lessonTypeOptions(selected) {
    return renderOptions(window.lessonTypes || [], selected);
  }
  function lessonLevelOptions(selected) {
    return renderOptions(window.lessonLevels || [], selected);
  }
  function profileVisibilityOptions(selected) {
    return renderOptions(
      window.profileVisibilities || [
        { value: 'public', label: 'عمومی' },
        { value: 'private', label: 'خصوصی' },
      ],
      selected || 'public'
    );
  }
  function getLessonTypeLabel(value) {
    const t = (window.lessonTypes || []).find((x) => x.value === value);
    return t ? t.label : value || '—';
  }
  function getLessonLevelLabel(value) {
    const t = (window.lessonLevels || []).find((x) => x.value === value);
    return t ? t.label : value || '—';
  }

  window.getStaffLessonRowHTML = function (prefix, index, lesson, isFirst) {
    lesson = lesson || {};
    return `<div data-lesson-row class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-500">نوع درس *</label>
                <select data-lesson-type onchange="updateStaffLessonLevels('${prefix}')" class="w-full border border-gray-300 rounded-2xl py-3 px-4">${lessonTypeOptions(lesson.type)}</select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1 text-gray-500">سطح درس *</label>
                <select data-lesson-level class="w-full border border-gray-300 rounded-2xl py-3 px-4">${lessonLevelOptions(lesson.level)}</select>
            </div>
            <button type="button" onclick="removeStaffLessonRow(this)" class="text-sm text-red-600 sm:col-span-2">${document.documentElement.lang.startsWith('en') ? 'Remove lesson' : 'حذف درس'}</button>
        </div>`;
  };

  function staffLessonsBlock(prefix, item, typeSelectId) {
    const isTeacher = (item && item.type === 'teacher') || false;
    const lessons = item?.lessons?.length ? item.lessons : [{ type: item?.lessonId, level: item?.levelId }];
    const rows = lessons.map((lesson, index) => window.getStaffLessonRowHTML(prefix, index, lesson, index === 0)).join('');
    return `<div id="${prefix}LessonsBox" class="${isTeacher ? '' : 'hidden'} space-y-3 border border-indigo-100 rounded-2xl p-4 bg-indigo-50/30">
            <p class="text-sm font-semibold text-indigo-700">درس و سطح تدریس (ویژه استاد)</p>
            <div id="${prefix}LessonsContainer" class="space-y-3">${rows}</div>
            <button type="button" onclick="addStaffLessonRow('${prefix}')" class="text-sm text-indigo-600">${document.documentElement.lang.startsWith('en') ? 'Add another lesson' : '+ افزودن درس دیگر'}</button>
        </div>`;
  }

  function staffOrganizationOptions(selected) {
    return renderOptions(
      (window.staffCatalog?.organizations || []).map((x) => ({ value: x.user_id, label: x.name })),
      selected
    );
  }
  function staffRoleOptions(selected) {
    return renderOptions(
      [{ value: '', label: document.documentElement.lang.startsWith('en') ? 'Select a role' : 'انتخاب نقش' }, ...(window.staffCatalog?.roles || []).map((x) => ({ value: x.id, label: x.title }))],
      selected
    );
  }
  window.updateStaffRole = function (roleId, type, organizationId, selected) {
    const role = document.getElementById(roleId),
      org = document.getElementById(organizationId),
      organization = (window.staffCatalog?.organizations || []).find(
        (x) => String(x.user_id) === String(org?.value)
      );
    if (!role) return;
    const branch = Boolean(organization?.branch_id),
      needles =
        type === 'receptionist'
          ? ['receptionist', 'reception']
          : type === 'other'
            ? ['employee', 'staff', 'other']
            : [type],
      roles = window.staffCatalog?.roles || [];
    const scoped = (x) =>
      branch ? x.name.includes('branch') : x.name.includes('academy') && !x.name.includes('branch');
    let matches = roles.filter(
      (x) => scoped(x) && needles.some((needle) => x.name.includes(needle))
    );
    if (!matches.length && type === 'other') matches = roles.filter(scoped);
    const current = selected ?? role.value;
    const english = document.documentElement.lang.startsWith('en');
    role.innerHTML = renderOptions(
      [{ value: '', label: matches.length ? (english ? 'Select a role' : 'انتخاب نقش') : (english ? 'No roles available' : 'نقش قابل انتخابی وجود ندارد') }, ...matches.map((x) => ({ value: x.id, label: x.title }))],
      current
    );
  };
  window.updateStaffLessonLevels = function (prefix) {
    const org = document.getElementById(prefix + 'Organization');
    document.querySelectorAll('#' + prefix + 'LessonsContainer [data-lesson-row]').forEach((row) => {
      const lesson = row.querySelector('[data-lesson-type]'), level = row.querySelector('[data-lesson-level]');
      if (!lesson || !level) return;
      const selected = level.value;
      const offered = (window.staffCatalog?.lessons || []).find((x) =>
        String(x.organization_user_id) === String(org?.value) && String(x.lesson_id) === String(lesson.value));
      const ids = offered?.level_ids || [];
      const english = document.documentElement.lang.startsWith('en');
      level.innerHTML = renderOptions([{ value: '', label: english ? 'Select a level' : 'انتخاب سطح' },
        ...(window.staffCatalog?.levels || []).filter((x) => ids.some((id) => String(id) === String(x.id)))
          .map((x) => ({ value: x.id, label: x.name }))], selected);
    });
  };
  window.updateStaffOrganization = function (prefix) {
    const org = document.getElementById(prefix + 'Organization'),
      lesson = document.querySelector('#' + prefix + 'LessonsContainer [data-lesson-type]');
    if (lesson) {
      const selected = lesson.value;
      lesson.innerHTML = renderOptions(
        [{ value: '', label: document.documentElement.lang.startsWith('en') ? 'Select a lesson' : 'انتخاب درس' }, ...(window.staffCatalog?.lessons || [])
          .filter((x) => String(x.organization_user_id) === String(org?.value))
          .map((x) => ({ value: x.lesson_id, label: x.name }))],
        selected
      );
    }
    window.updateStaffLessonLevels(prefix);
    window.updateStaffRole(
      prefix + 'Role',
      document.getElementById(prefix + 'Type')?.value,
      prefix + 'Organization'
    );
  };

  window.getStaffEmptyRowHTML = function () {
    return window.AdminPanelTable.rowHTML([{ html: `هیچ کارمندی یافت نشد`, attributes: ` colspan="10" class="py-12 text-center text-gray-400"` }], ``);
  };

  window.getStaffInlineExpandRowHTML = function (item) {
    return window.AdminPanelTable.cellsHTML([{ html: `${window.getStaffInlineEditRowHTML ? window.getStaffInlineEditRowHTML(item) : ''}`, attributes: ` colspan="10" class="p-5 border-t"` }]);
  };

  window.getStaffInlineEditRowHTML = function (item) {
    const p = 'inlineStaff' + item.id;
    return `<div class="space-y-6"><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div><label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label><input id="${p}Name" value="${escapeHtml(item.name)}" class="w-full border rounded-2xl py-3.5 px-5"></div>
        <div><label class="block text-sm font-medium mb-2">جنسیت *</label><select id="${p}Gender" class="w-full border rounded-2xl py-3.5 px-5">${renderOptions(
          [
            { value: 'male', label: 'مرد' },
            { value: 'female', label: 'زن' },
            { value: 'other', label: 'سایر' },
          ],
          item.gender
        )}</select></div>
        <div><label class="block text-sm font-medium mb-2">کد ملی *</label><input id="${p}NationalId" value="${escapeHtml(item.nationalId || '')}" class="w-full border rounded-2xl py-3.5 px-5"></div>
        <div data-member-identity-field class="${item.birthDate ? 'hidden' : ''}"><label class="block text-sm font-medium mb-2">تاریخ تولد *</label><input id="${p}BirthDate" type="date" value="${escapeHtml(item.birthDate || '')}" class="w-full border rounded-2xl py-3.5 px-5"></div>
        <div><label class="block text-sm font-medium mb-2">شماره تماس *</label><input id="${p}Phone" value="${escapeHtml(item.phone)}" class="w-full border rounded-2xl py-3.5 px-5"></div>
        <div><label class="block text-sm font-medium mb-2">نمایش پروفایل</label><select id="${p}ProfileVisibility" class="w-full border rounded-2xl py-3.5 px-5">${profileVisibilityOptions(item.profileVisibility)}</select></div>
        <div class="${window.staffCatalog?.organization_selection === 'fixed' ? 'hidden' : ''}"><label class="block text-sm font-medium mb-2">سازمان *</label><select data-panel-organization-field id="${p}Organization" onchange="updateStaffOrganization('${p}')" class="w-full border rounded-2xl py-3.5 px-5">${staffOrganizationOptions(item.organizationUserId)}</select></div>
<div><label class="block text-sm font-medium mb-2">نوع قرارداد *</label><select id="${p}Type" onchange="toggleStaffLessonFields('${p}Type','${p}LessonsBox')" class="w-full border rounded-2xl py-3.5 px-5">${renderOptions(staffTypes, item.type)}</select></div>
        <div><label class="block text-sm font-medium mb-2">نقش *</label><select id="${p}Role" class="w-full border rounded-2xl py-3.5 px-5">${staffRoleOptions(item.roleId)}</select></div>

        <div><label class="block text-sm font-medium mb-2">عنوان قرارداد *</label><input id="${p}ContractTitle" value="${escapeHtml(item.contractTitle)}" class="w-full border rounded-2xl py-3.5 px-5"></div>
        <div><label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label><input id="${p}StartDate" type="date" value="${escapeHtml(item.startDate)}" class="w-full border rounded-2xl py-3.5 px-5"></div>
<div><label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label><input id="${p}EndDate" type="date" value="${escapeHtml(item.endDate)}" class="w-full border rounded-2xl py-3.5 px-5"></div>
<div><label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label><select id="${p}Currency" class="w-full border rounded-2xl py-3.5 px-5">${renderOptions(window.contractCurrencies || contractCurrencies, item.currencyId)}</select></div>
        <div><label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label><input id="${p}Price" type="number" value="${escapeHtml(item.price)}" class="w-full border rounded-2xl py-3.5 px-5"></div>


        <div class="sm:col-span-2 lg:col-span-3">${staffLessonsBlock(p, item)}</div>
<div class="sm:col-span-2 lg:col-span-3"><label class="block text-sm font-medium mb-2">شرح قرارداد</label><textarea id="${p}ContractDescription" rows="3" class="w-full border rounded-2xl py-3.5 px-5">${escapeHtml(item.contractDescription)}</textarea></div></div>
        <div class="flex gap-4"><button onclick="saveInlineStaff(${item.id})" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl">ذخیره</button><button onclick="toggleStaffInlineEdit(${item.id})" class="border px-6 py-3 rounded-2xl">انصراف</button></div></div>`;
  };

  window.getStaffPDFModalHTML = function (pdfExportColumns) {
    return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[calc(100vh-2rem)] shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                    <div class="shrink-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">تنظیمات خروجی PDF</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6 min-h-0 flex-1 overflow-y-auto">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                                <input id="pdfTitle" type="text" value="گزارش کارمند آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                                <input id="pdfSubtitle" type="text" value="لیست کارمند و وضعیت قراردادها" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
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
                                        ${pdfExportColumns
                                          .map(
                                            (col) => `
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="checkbox" id="pdfCol-${col.field}" value="${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                                ${col.label}
                                            </label>
                                        `
                                          )
                                          .join('')}
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

                    </div>
                    <div class="shrink-0 flex gap-4 px-8 py-5 border-t bg-white">
                            <button onclick="generateStaffPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
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
      orientation,
    } = options;

    return `
            <div style="width:100%; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(15,23,42,.08); background: #fff;">
                ${
                  isFirstPage
                    ? `
                <div style="text-align: right; direction: rtl;">
                    <h1 style="margin: 0 0 6px; font-size: 28px; font-weight: 700;">${escapeHtml(title)}</h1>
                    <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px;">${escapeHtml(subtitle)}</p>
                    ${includeDate ? `<p style="margin: 0 0 16px; color: #6b7280; font-size: 12px;">تاریخ استخراج: ${escapeHtml(date)}</p>` : ''}
                </div>
                `
                    : ''
                }
                <div style="width: 100%; overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse; direction: rtl;">
                        <thead style="background: ${headerColor}; color: #000000;">
                            <tr>
                                ${selectedColumns.map((col) => `<th style="padding: 12px 14px; text-align: right; font-weight: 600;">${escapeHtml(col.label)}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rows
                              .map(
                                (item, index) => `
                                <tr style="background: ${index % 2 === 0 ? evenRowColor : oddRowColor};">
                                    ${selectedColumns
                                      .map((col) => {
                                        const value =
                                          col.field === 'index'
                                            ? (pageNumber - 1) * rowsPerPage + index + 1
                                            : col.field === 'price'
                                              ? `${item.price.toLocaleString('fa-IR')} ${item.currency}`
                                              : item[col.field];
                                        return `<td style="padding: 12px 14px; text-align: right;">${escapeHtml(value)}</td>`;
                                      })
                                      .join('')}
                                </tr>
                            `
                              )
                              .join('')}
                        </tbody>
                    </table>
                </div>
                ${isFirstPage && footer ? `<p style="margin-top: 16px; color: #6b7280; font-size: 12px;">${escapeHtml(footer)}</p>` : ''}
                <div style="margin-top: 16px; display: flex; justify-content: flex-end; color: #6b7280; font-size: 12px;">صفحه ${pageNumber} / ${totalPages}</div>
            </div>`;
  };

  window.getStaffAddModalHTML = function () {
    return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[calc(100vh-2rem)] shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                    <div class="shrink-0 bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">افزودن کارمند جدید</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6 min-h-0 flex-1 overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                                <input id="staffName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div><label class="block text-sm font-medium mb-2">جنسیت *</label><select id="staffGender" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"><option value="male">مرد</option><option value="female">زن</option><option value="other">سایر</option></select></div>
                            <div><label class="block text-sm font-medium mb-2">کد ملی *</label><input id="staffNationalId" type="text" inputmode="numeric" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></div>
                            <div data-member-identity-field><label class="block text-sm font-medium mb-2">تاریخ تولد *</label><input id="staffBirthDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                                <input id="staffPhone" type="tel" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div><label class="block text-sm font-medium mb-2">نمایش پروفایل</label><select id="staffProfileVisibility" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${profileVisibilityOptions('public')}</select></div>
                            <div class="${window.staffCatalog?.organization_selection === 'fixed' ? 'hidden' : ''}"><label class="block text-sm font-medium mb-2">سازمان *</label><select data-panel-organization-field id="staffOrganization" onchange="updateStaffOrganization('staff')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${staffOrganizationOptions()}</select></div>
<div>
                                <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                                <select id="staffType" onchange="toggleStaffLessonFields('staffType', 'staffLessonsBox')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(staffTypes)}
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium mb-2">نقش *</label><select id="staffRole" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${staffRoleOptions()}</select></div>

                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                                <input id="staffContractTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                                <input id="staffStartDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
<div>
                                <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                                <input id="staffEndDate" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
<div>
                                <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                                <select id="staffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(window.contractCurrencies || contractCurrencies)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                                <input id="staffPrice" type="number" value="5000000" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>


                            <div class="sm:col-span-2 lg:col-span-3">${staffLessonsBlock('staff', { type: 'teacher', lessons: [] })}</div>
<div class="lg:col-span-3">
                                <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                                <textarea id="staffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="shrink-0 flex gap-4 px-8 py-5 border-t bg-white">
                            <button onclick="saveStaff()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره کارمند</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                </div>
            </div>`;
  };

  window.getStaffDetailsModalHTML = function (item) {
    return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[calc(100vh-2rem)] shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                    <div class="shrink-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <div>
                            <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                            <p class="text-sm text-gray-500 mt-1">کد کارمند: #${item.id}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="editStaff(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                            <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                        </div>
                    </div>
                    <div class="p-8 space-y-8 min-h-0 flex-1 overflow-y-auto">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> اطلاعات شخصی</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">جنسیت</span><span class="font-medium">${item.gender === 'male' ? 'مرد' : item.gender === 'female' ? 'زن' : 'سایر'}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کد ملی</span><span class="font-medium">${escapeHtml(item.nationalId || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ تولد</span><span class="font-medium">${escapeHtml(item.birthDate || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شماره تماس</span><span class="font-medium">${escapeHtml(item.phone)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع قرارداد</span><span class="font-medium">${escapeHtml(item.typeLabel)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نمایش پروفایل</span><span class="font-medium">${item.profileVisibility === 'private' ? 'خصوصی' : 'عمومی'}</span></div>
                            </div>
                        </div>
                        ${
                          item.type === 'teacher'
                            ? `
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-music"></i> درس و سطح تدریس</h3>
                            <div class="flex flex-wrap gap-2">
                                ${
                                  (item.lessons && item.lessons.length ? item.lessons : [])
                                    .map(
                                      (l) =>
                                        `<span class="px-3 py-1.5 rounded-full text-xs bg-indigo-50 text-indigo-700 border border-indigo-100">${escapeHtml(getLessonTypeLabel(l.type))} · ${escapeHtml(getLessonLevelLabel(l.level))}</span>`
                                    )
                                    .join('') ||
                                  '<span class="text-sm text-gray-400">درسی ثبت نشده</span>'
                                }
                            </div>
                        </div>`
                            : ''
                        }
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-file-contract"></i> اطلاعات قرارداد</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">عنوان قرارداد</span><span class="font-medium">${escapeHtml(item.contractTitle)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">سازمان</span><span class="font-medium">${escapeHtml(item.branch)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ شروع</span><span class="font-medium">${escapeHtml(item.startDate)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تاریخ خاتمه</span><span class="font-medium">${escapeHtml(item.endDate)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">مبلغ قرارداد</span><span class="font-medium">${item.price.toLocaleString('fa-IR')} ${escapeHtml(item.currency)}</span></div>
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
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[calc(100vh-2rem)] shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                    <div class="shrink-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">ویرایش کارمند</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8 space-y-6 min-h-0 flex-1 overflow-y-auto">
                        <input type="hidden" id="editStaffContractId" value="${Number(item.contractId || 0)}">
                        ${item.contracts?.length > 1 ? `<label class="block text-sm">${document.documentElement.lang.startsWith('en') ? 'Contract to edit' : 'قرارداد مورد ویرایش'}<select class="mt-2 w-full rounded-2xl border px-4 py-3" onchange="editStaffContract(${Number(item.id)},this.value)">${item.contracts.map(contract => `<option value="${Number(contract.contractId)}" ${Number(contract.contractId) === Number(item.contractId) ? 'selected' : ''}>${escapeHtml(contract.typeLabel)} — ${escapeHtml(contract.contractTitle)}</option>`).join('')}</select></label>` : ''}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                                <input id="editStaffName" type="text" value="${escapeHtml(item.name)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div><label class="block text-sm font-medium mb-2">جنسیت *</label><select id="editStaffGender" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${renderOptions(
                              [
                                { value: 'male', label: 'مرد' },
                                { value: 'female', label: 'زن' },
                                { value: 'other', label: 'سایر' },
                              ],
                              item.gender
                            )}</select></div>
                            <div><label class="block text-sm font-medium mb-2">کد ملی *</label><input id="editStaffNationalId" type="text" value="${escapeHtml(item.nationalId || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></div>
                            <div data-member-identity-field class="${item.birthDate ? 'hidden' : ''}"><label class="block text-sm font-medium mb-2">تاریخ تولد *</label><input id="editStaffBirthDate" type="date" value="${escapeHtml(item.birthDate || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></div>
                            <div>
                                <label class="block text-sm font-medium mb-2">شماره تماس *</label>
                                <input id="editStaffPhone" type="tel" value="${escapeHtml(item.phone)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div><label class="block text-sm font-medium mb-2">نمایش پروفایل</label><select id="editStaffProfileVisibility" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${profileVisibilityOptions(item.profileVisibility)}</select></div>
                            <div class="${window.staffCatalog?.organization_selection === 'fixed' ? 'hidden' : ''}"><label class="block text-sm font-medium mb-2">سازمان *</label><select data-panel-organization-field id="editStaffOrganization" onchange="updateStaffOrganization('editStaff')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${staffOrganizationOptions(item.organizationUserId)}</select></div>
<div>
                                <label class="block text-sm font-medium mb-2">نوع قرارداد *</label>
                                <select id="editStaffType" onchange="toggleStaffLessonFields('editStaffType', 'editStaffLessonsBox')" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(staffTypes, item.type)}
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium mb-2">نقش *</label><select id="editStaffRole" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${staffRoleOptions(item.roleId)}</select></div>

                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                                <input id="editStaffContractTitle" type="text" value="${escapeHtml(item.contractTitle)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">تاریخ شروع قرارداد *</label>
                                <input id="editStaffStartDate" type="date" value="${escapeHtml(item.startDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
<div>
                                <label class="block text-sm font-medium mb-2">تاریخ خاتمه قرارداد *</label>
                                <input id="editStaffEndDate" type="date" value="${escapeHtml(item.endDate)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
<div>
                                <label class="block text-sm font-medium mb-2">نوع پول پرداختی *</label>
                                <select id="editStaffCurrency" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                    ${renderOptions(window.contractCurrencies || contractCurrencies, item.currencyId)}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">مبلغ قرارداد *</label>
                                <input id="editStaffPrice" type="number" value="${escapeHtml(item.price)}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>


                            <div class="sm:col-span-2 lg:col-span-3">${staffLessonsBlock('editStaff', item)}</div>
<div class="lg:col-span-3">
                                <label class="block text-sm font-medium mb-2">شرح قرارداد</label>
                                <textarea id="editStaffContractDescription" rows="4" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.contractDescription)}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="shrink-0 flex gap-4 px-8 py-5 border-t bg-white">
                            <button onclick="saveEditedStaff(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                </div>
            </div>`;
  };
})();
