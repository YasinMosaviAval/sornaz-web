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
        return (options || []).map(option => {
            const value = option.value ?? option.id ?? option.name ?? option;
            const label = option.label ?? option.name ?? option;
            const isSelected = String(value) === String(selectedValue) ? 'selected' : '';
            return `<option value="${escapeHtml(value)}" ${isSelected}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    function statusBadgeClass(status) {
        return {
            'در حال برگزاری': 'bg-green-100 text-green-700',
            'پایان‌یافته': 'bg-gray-100 text-gray-600',
            'در انتظار': 'bg-yellow-100 text-yellow-700',
            'تعلیق‌شده': 'bg-red-100 text-red-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    window.getTermRowHTML = function (item, statusClass) {
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.course || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.start || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.end || '—')}</td>
            <td class="py-4 px-5">
                <span class="px-3 py-1 rounded-full text-xs ${statusClass || statusBadgeClass(item.status)}">${escapeHtml(item.status)}</span>
            </td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewTerm(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button onclick="toggleTermInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button onclick="toggleTermInlineAttendance(${item.id})" class="text-emerald-600 hover:underline text-sm">حضور و غیاب</button>
                    <button onclick="deleteTerm(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>
        `;
    };

    window.getTermEmptyRowHTML = function () {
        return `<tr><td colspan="7" class="py-12 text-center text-gray-400">هیچ ترمی یافت نشد</td></tr>`;
    };

    window.getTermInlineExpandRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getTermInlineEditRowHTML ? window.getTermInlineEditRowHTML(item) : ''}</td>`;
    };

    window.getTermInlineAttendanceRowHTML = function (item) {
        return `<td colspan="7" class="p-5 border-t">${window.getTermAttendancePanelHTML ? window.getTermAttendancePanelHTML(item, true) : ''}</td>`;
    };

    window.getTermTeacherFieldHTML = function (item) {
        item = item || {};
        const teachers = (typeof getTermTeacherOptions === 'function') ? getTermTeacherOptions() : [];
        return `
            <div class="border border-gray-200 rounded-2xl p-4 mb-3 term-teacher-item">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label class="text-xs text-gray-500 mb-1 block">استاد</label>
                        <select class="term-teacher-select w-full border border-gray-300 rounded-2xl py-3 px-4">
                            <option value="">انتخاب استاد</option>
                            ${renderOptions(teachers, item.id || item.name || '')}
                        </select>
                    </div>
                    <button type="button" onclick="this.closest('.term-teacher-item').remove()" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
                </div>
            </div>`;
    };

    window.getTermStudentFieldHTML = function (item) {
        item = item || {};
        const students = (typeof getTermStudentOptions === 'function') ? getTermStudentOptions() : [];
        return `
            <div class="border border-gray-200 rounded-2xl p-4 mb-3 term-student-item">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label class="text-xs text-gray-500 mb-1 block">هنرجو</label>
                        <select class="term-student-select w-full border border-gray-300 rounded-2xl py-3 px-4">
                            <option value="">انتخاب هنرجو</option>
                            ${renderOptions(students, item.id || item.name || '')}
                        </select>
                    </div>
                    <button type="button" onclick="this.closest('.term-student-item').remove()" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
                </div>
            </div>`;
    };

    window.getTermInstallmentFieldHTML = function (item) {
        item = item || {};
        return `
            <div class="border border-gray-200 rounded-2xl p-4 mb-3 term-installment-item">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label class="text-xs text-gray-500 mb-1 block">مبلغ قسط</label>
                        <input type="number" min="0" class="term-installment-amount w-full border border-gray-300 rounded-2xl py-3 px-4" value="${escapeHtml(item.amount ?? '')}" placeholder="مبلغ">
                    </div>
                    <button type="button" onclick="this.closest('.term-installment-item').remove()" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
                </div>
            </div>`;
    };

    window.getTermSessionFieldsHTML = function (sessions) {
        sessions = sessions || [];
        const list = sessions.length ? sessions : [{ date: '' }];
        return list.map(function (s, i) {
            return `
            <div class="mb-3">
                <label class="text-xs text-gray-500 mb-1 block">جلسه ${i + 1}</label>
                <input type="date" class="term-session-date w-full border border-gray-300 rounded-2xl py-3 px-4"
                       data-session-index="${i}" value="${escapeHtml(s.date || '')}">
            </div>`;
        }).join('');
    };

    function termFormFields(item, prefix) {
        const id = function (name) { return prefix ? (prefix + name) : ('term' + name); };
        const branches = (typeof allBranches !== 'undefined' ? allBranches : []).map(function (b) {
            return { value: b.id, label: b.name };
        });
        const courses = (typeof getTermCourseOptions === 'function') ? getTermCourseOptions() : [];
        const currencies = (typeof allTermCurrencies !== 'undefined' ? allTermCurrencies : []).map(function (c) {
            return { value: c.name, label: c.name };
        });
        const discounts = (typeof allTermDiscounts !== 'undefined' ? allTermDiscounts : []).map(function (d) {
            return { value: d.name, label: d.name };
        });
        const classrooms = (typeof getTermClassroomOptions === 'function') ? getTermClassroomOptions() : [];
        const statuses = (typeof termStatuses !== 'undefined' ? termStatuses : ['در حال برگزاری', 'در انتظار', 'پایان‌یافته', 'تعلیق‌شده']).map(function (s) {
            return { value: s, label: s };
        });

        const teachers = (item.teachers && item.teachers.length) ? item.teachers : [{}];
        const students = (item.students && item.students.length) ? item.students : [{}];
        const installments = (item.installments && item.installments.length) ? item.installments : [{}];
        const sessions = item.sessions || [];
        const sessionCount = sessions.length || 8;

        const tContainer = prefix ? (prefix + 'TeachersContainer') : 'termTeachersContainer';
        const sContainer = prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer';
        const iContainer = prefix ? (prefix + 'InstallmentsContainer') : 'termInstallmentsContainer';
        const sessContainer = prefix ? (prefix + 'SessionsContainer') : 'termSessionsContainer';

        const sessionList = sessions.length ? sessions : Array.from({ length: sessionCount }, function () { return { date: '' }; });

        return `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام ترم *</label>
                    <input id="${id('Name')}" type="text" value="${escapeHtml(item.name || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="${id('Branch')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(branches, item.branchId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">دوره مرتبط *</label>
                    <select id="${id('Course')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب دوره</option>
                        ${renderOptions(courses, item.courseId || item.course || '')}
                    </select>
                    <button type="button" onclick="promptAddTermCourse()" class="text-sm text-indigo-600 mt-1">+ دوره جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع پول</label>
                    <select id="${id('Currency')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(currencies, item.currency || (currencies[0] && currencies[0].value))}
                    </select>
                    <button type="button" onclick="promptAddTermCurrency()" class="text-sm text-indigo-600 mt-1">+ واحد پول جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تخفیف</label>
                    <select id="${id('Discount')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(discounts, item.discount || (discounts[0] && discounts[0].value))}
                    </select>
                    <button type="button" onclick="promptAddTermDiscount()" class="text-sm text-indigo-600 mt-1">+ تخفیف جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">کلاس برگزاری</label>
                    <select id="${id('Classroom')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="">انتخاب کلاس</option>
                        ${renderOptions(classrooms, item.classroomId || item.classroom || '')}
                    </select>
                    <button type="button" onclick="promptAddTermClassroom()" class="text-sm text-indigo-600 mt-1">+ کلاس جدید</button>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">هزینه ترم</label>
                    <input id="${id('Cost')}" type="number" min="0" value="${escapeHtml(item.cost ?? '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="${id('Status')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${renderOptions(statuses, item.status || 'در حال برگزاری')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تعداد جلسات</label>
                    <input id="${id('SessionCount')}" type="number" min="1" max="40" value="${sessionCount}"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"
                           onchange="rebuildTermSessions('${prefix}')">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">خلاصه ترم</label>
                    <input id="${id('Summary')}" type="text" value="${escapeHtml(item.summary || '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium mb-2">شرح ترم</label>
                    <textarea id="${id('Description')}" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${escapeHtml(item.description || '')}</textarea>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">جلسات (تاریخ هر جلسه)</label>
                <p class="text-xs text-gray-400 mb-2">تاریخ اولین جلسه = شروع ترم · تاریخ آخرین جلسه = پایان ترم</p>
                <div id="${sessContainer}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    ${window.getTermSessionFieldsHTML(sessionList)}
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">استادها</label>
                <div id="${tContainer}">${teachers.map(function (t) { return window.getTermTeacherFieldHTML(t); }).join('')}</div>
                <button type="button" onclick="addTermTeacherField('${tContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن استاد</button>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">هنرجویان</label>
                <div id="${sContainer}">${students.map(function (s) { return window.getTermStudentFieldHTML(s); }).join('')}</div>
                <button type="button" onclick="addTermStudentField('${sContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن هنرجو</button>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">اقساط</label>
                <div id="${iContainer}">${installments.map(function (x) { return window.getTermInstallmentFieldHTML(x); }).join('')}</div>
                <button type="button" onclick="addTermInstallmentField('${iContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن قسط</button>
            </div>
        `;
    }

    window.getTermInlineEditRowHTML = function (item) {
        return `
            <div class="space-y-6">
                ${termFormFields(item, 'inlineTerm' + item.id)}
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button onclick="saveInlineTerm(${item.id})" class="w-full sm:w-auto min-w-[140px] bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره</button>
                    <button onclick="toggleTermInlineEdit(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                </div>
            </div>
        `;
    };

    window.getTermAddModalHTML = function () {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">افزودن ترم جدید</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${termFormFields({}, '')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveTerm()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره ترم</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getTermEditModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">ویرایش ترم</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                        ${termFormFields(item, 'editTerm')}
                        <div class="flex gap-4 pt-4">
                            <button onclick="saveEditedTerm(${item.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره تغییرات</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    function attendanceStatsHTML(item) {
        const stats = (typeof getTermAttendanceStats === 'function')
            ? getTermAttendanceStats(item)
            : { present: 0, absent: 0, total: 0, rate: 0 };
        return `
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-gray-50 rounded-2xl py-3"><div class="text-xs text-gray-400">کل رکورد</div><div class="font-bold mt-1">${stats.total}</div></div>
                <div class="bg-green-50 rounded-2xl py-3"><div class="text-xs text-gray-400">حاضر</div><div class="font-bold mt-1 text-green-700">${stats.present}</div></div>
                <div class="bg-red-50 rounded-2xl py-3"><div class="text-xs text-gray-400">غایب</div><div class="font-bold mt-1 text-red-600">${stats.absent}</div></div>
                <div class="bg-indigo-50 rounded-2xl py-3"><div class="text-xs text-gray-400">درصد حضور</div><div class="font-bold mt-1 text-indigo-700">${stats.rate}٪</div></div>
            </div>`;
    }

    window.getTermDetailsModalHTML = function (item) {
        const teachers = (item.teachers || []).map(function (t) { return escapeHtml(t.name || t); }).join('، ') || '—';
        const students = (item.students || []).map(function (s) { return escapeHtml(s.name || s); }).join('، ') || '—';
        const installments = (item.installments || []).map(function (x, i) {
            return `<div class="flex justify-between border-b pb-2 text-sm"><span>قسط ${i + 1}</span><span class="font-medium">${Number(x.amount || 0).toLocaleString('fa-IR')}</span></div>`;
        }).join('') || '<p class="text-sm text-gray-400">قسطی ثبت نشده</p>';
        const sessions = (item.sessions || []).map(function (s, i) {
            return `<div class="flex justify-between border-b pb-2 text-sm"><span>جلسه ${i + 1}</span><span class="font-medium">${escapeHtml(s.date || '—')}</span></div>`;
        }).join('') || '<p class="text-sm text-gray-400">جلسه‌ای ثبت نشده</p>';

        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <div>
                            <h2 class="text-2xl font-bold">${escapeHtml(item.name)}</h2>
                            <p class="text-sm text-gray-500 mt-1">کد ترم: #${item.id}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="editTerm(${item.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm">ویرایش</button>
                            <button onclick="openTermAttendanceModal(${item.id})" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm">حضور و غیاب</button>
                            <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-4">اطلاعات ترم</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نام</span><span class="font-medium">${escapeHtml(item.name)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span class="font-medium">${escapeHtml(item.branchName)}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">دوره</span><span class="font-medium">${escapeHtml(item.course || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">کلاس</span><span class="font-medium">${escapeHtml(item.classroom || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شروع</span><span class="font-medium">${escapeHtml(item.start || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">پایان</span><span class="font-medium">${escapeHtml(item.end || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">هزینه</span><span class="font-medium">${item.cost != null ? Number(item.cost).toLocaleString('fa-IR') + ' ' + escapeHtml(item.currency || '') : '—'}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">تخفیف</span><span class="font-medium">${escapeHtml(item.discount || '—')}</span></div>
                                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">وضعیت</span><span class="font-medium">${escapeHtml(item.status)}</span></div>
                            </div>
                            ${item.summary ? `<p class="mt-4 text-indigo-600 font-medium">${escapeHtml(item.summary)}</p>` : ''}
                            ${item.description ? `<p class="mt-2 text-sm text-gray-600 leading-relaxed">${escapeHtml(item.description)}</p>` : ''}
                        </div>
                        <div>
                            <h3 class="font-semibold text-indigo-700 mb-3">آمار حضور و غیاب</h3>
                            ${attendanceStatsHTML(item)}
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><h3 class="font-semibold text-indigo-700 mb-3">استادها</h3><p class="text-sm">${teachers}</p></div>
                            <div><h3 class="font-semibold text-indigo-700 mb-3">هنرجویان</h3><p class="text-sm">${students}</p></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><h3 class="font-semibold text-indigo-700 mb-3">جلسات</h3><div class="space-y-2">${sessions}</div></div>
                            <div><h3 class="font-semibold text-indigo-700 mb-3">اقساط</h3><div class="space-y-2">${installments}</div></div>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getTermAttendancePanelHTML = function (item, isInline) {
        const sessions = item.sessions || [];
        const teachers = item.teachers || [];
        const students = item.students || [];
        const att = item.attendance || {};

        if (!sessions.length) {
            return '<p class="text-center text-gray-400 py-8">جلسه‌ای برای این ترم تعریف نشده است.</p>';
        }

        const sessionBlocks = sessions.map(function (s, si) {
            const key = String(si);
            const row = att[key] || { teachers: {}, students: {} };
            const teacherRows = teachers.map(function (t) {
                const tid = String(t.id || t.name);
                const checked = row.teachers && row.teachers[tid] ? 'checked' : '';
                return `<label class="flex items-center gap-2 text-sm py-1">
                    <input type="checkbox" class="att-teacher" data-session="${si}" data-id="${escapeHtml(tid)}" ${checked}>
                    <span>${escapeHtml(t.name || t)}</span>
                    <span class="text-xs text-gray-400">(استاد)</span>
                </label>`;
            }).join('') || '<p class="text-xs text-gray-400">استادی ثبت نشده</p>';

            const studentRows = students.map(function (st) {
                const sid = String(st.id || st.name);
                const checked = row.students && row.students[sid] ? 'checked' : '';
                return `<label class="flex items-center gap-2 text-sm py-1">
                    <input type="checkbox" class="att-student" data-session="${si}" data-id="${escapeHtml(sid)}" ${checked}>
                    <span>${escapeHtml(st.name || st)}</span>
                    <span class="text-xs text-gray-400">(هنرجو)</span>
                </label>`;
            }).join('') || '<p class="text-xs text-gray-400">هنرجویی ثبت نشده</p>';

            return `
                <div class="border border-gray-200 rounded-2xl p-4 mb-4">
                    <div class="font-medium mb-3 flex justify-between">
                        <span>جلسه ${si + 1}</span>
                        <span class="text-sm text-gray-500">${escapeHtml(s.date || 'بدون تاریخ')}</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-medium text-indigo-600 mb-2">اساتید</div>
                            ${teacherRows}
                        </div>
                        <div>
                            <div class="text-xs font-medium text-indigo-600 mb-2">هنرجویان</div>
                            ${studentRows}
                        </div>
                    </div>
                </div>`;
        }).join('');

        const actions = isInline
            ? `<div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button onclick="saveTermAttendance(${item.id}, true)" class="w-full sm:w-auto min-w-[140px] bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-4 rounded-2xl font-medium">ذخیره حضور و غیاب</button>
                    <button onclick="toggleTermInlineAttendance(${item.id})" class="w-full sm:w-auto min-w-[140px] border border-gray-300 px-5 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
               </div>`
            : `<div class="flex gap-4 pt-4">
                    <button onclick="saveTermAttendance(${item.id}, false)" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-medium">ذخیره حضور و غیاب</button>
                    <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">بستن</button>
               </div>`;

        return `
            <div class="space-y-4" id="termAttendancePanel-${item.id}">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg">حضور و غیاب — ${escapeHtml(item.name)}</h3>
                </div>
                ${attendanceStatsHTML(item)}
                <div class="max-h-[50vh] overflow-y-auto pr-1">${sessionBlocks}</div>
                ${actions}
            </div>`;
    };

    window.getTermAttendanceModalHTML = function (item) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-3xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="bg-white px-8 py-5 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">حضور و غیاب</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500">×</button>
                    </div>
                    <div class="p-8">
                        ${window.getTermAttendancePanelHTML(item, false)}
                    </div>
                </div>
            </div>`;
    };

    window.getTermPDFModalHTML = function (pdfExportColumns) {
        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
                    <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl">
                        <h2 class="text-2xl font-bold">تنظیمات خروجی PDF ترم‌ها</h2>
                        <button onclick="closeModal()" class="text-3xl text-gray-300 hover:text-gray-500 leading-none">×</button>
                    </div>
                    <div class="p-8 space-y-6" style="max-height: calc(100vh - 10rem); overflow-y: auto;">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-2">عنوان گزارش</label>
                                <input id="termPdfTitle" type="text" value="گزارش ترم‌های آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">زیرعنوان</label>
                                <input id="termPdfSubtitle" type="text" value="لیست ترم‌ها، دوره‌ها و وضعیت برگزاری" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">فرمت صفحه</label>
                                    <select id="termPdfFormat" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="a4">A4</option>
                                        <option value="letter">Letter</option>
                                        <option value="legal">Legal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">جهت صفحه</label>
                                    <select id="termPdfOrientation" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                                        <option value="landscape">افقی</option>
                                        <option value="portrait">عمودی</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">یادداشت پایین صفحه</label>
                                <input id="termPdfFooter" type="text" value="تولید شده توسط سیستم مدیریت آموزشگاه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">ستون‌های خروجی PDF</label>
                                <div class="grid grid-cols-2 gap-2">
                                    ${pdfExportColumns.map(function (col) {
                                        return `<label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" id="termPdfCol-${col.field}" checked class="text-indigo-600 border-gray-300 rounded">
                                            ${col.label}
                                        </label>`;
                                    }).join('')}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطر عنوان</label>
                                    <input id="termPdfHeaderColor" type="color" value="#eff6ff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای زوج</label>
                                    <input id="termPdfEvenRowColor" type="color" value="#ffffff" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">رنگ سطرهای فرد</label>
                                    <input id="termPdfOddRowColor" type="color" value="#f8fafc" class="w-full h-12 border border-gray-300 rounded-2xl p-2">
                                </div>
                            </div>
                            <label class="flex items-center gap-3">
                                <input id="termPdfIncludeDate" type="checkbox" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">نمایش تاریخ استخراج در بالای گزارش</span>
                            </label>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button onclick="generateTermsPDF()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ایجاد PDF</button>
                            <button onclick="closeModal()" class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    window.getTermPDFPageHTML = function (pageNumber, rows, isFirstPage, options) {
        const title = options.title;
        const subtitle = options.subtitle;
        const footer = options.footer;
        const includeDate = options.includeDate;
        const date = options.date;
        const headerColor = options.headerColor;
        const evenRowColor = options.evenRowColor;
        const oddRowColor = options.oddRowColor;
        const selectedColumns = options.selectedColumns;
        const rowsPerPage = options.rowsPerPage;
        const totalPages = options.totalPages;

        return `
            <div style="width:100%; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(15,23,42,.08); background: #fff;">
                ${isFirstPage ? `
                <div style="text-align: right; direction: rtl;">
                    <h1 style="margin: 0 0 6px; font-size: 28px; font-weight: 700;">${escapeHtml(title)}</h1>
                    <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px;">${escapeHtml(subtitle)}</p>
                    ${includeDate ? `<p style="margin: 0 0 16px; color: #6b7280; font-size: 12px;">تاریخ استخراج: ${escapeHtml(date)}</p>` : ''}
                </div>` : ''}
                <table style="width:100%; border-collapse: collapse; direction: rtl;">
                    <thead style="background: ${headerColor};">
                        <tr>
                            ${selectedColumns.map(function (col) {
                                return `<th style="padding: 12px 14px; text-align: right; font-weight: 600;">${escapeHtml(col.label)}</th>`;
                            }).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map(function (item, index) {
                            return `<tr style="background: ${index % 2 === 0 ? evenRowColor : oddRowColor};">
                                ${selectedColumns.map(function (col) {
                                    const value = col.field === 'index'
                                        ? (pageNumber - 1) * rowsPerPage + index + 1
                                        : item[col.field];
                                    return `<td style="padding: 12px 14px; text-align: right;">${escapeHtml(value)}</td>`;
                                }).join('')}
                            </tr>`;
                        }).join('')}
                    </tbody>
                </table>
                ${isFirstPage && footer ? `<p style="margin-top: 16px; color: #6b7280; font-size: 12px;">${escapeHtml(footer)}</p>` : ''}
                <div style="margin-top: 16px; text-align: left; color: #6b7280; font-size: 12px;">صفحه ${pageNumber} / ${totalPages}</div>
            </div>`;
    };
})();
