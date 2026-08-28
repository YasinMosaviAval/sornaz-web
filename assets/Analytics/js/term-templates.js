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
            'باز': 'bg-green-100 text-green-700',
            'در حال برگزاری': 'bg-gray-100 text-gray-700',
            'پایان یافته': 'bg-red-100 text-red-700',
            'در انتظار تأیید': 'bg-yellow-100 text-yellow-700'
        }[status] || 'bg-gray-100 text-gray-600';
    }

    window.getTermRowHTML = function (item, statusClass) {
        const dayNames=['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
        const days=[...new Set((item.sessions||[]).filter(s=>s.date).map(s=>dayNames[new Date(s.date+'T12:00:00').getDay()]))].join('، ')||'—';
        const times=[...new Set((item.sessions||[]).filter(s=>s.startTime).map(s=>s.startTime+(s.endTime?' تا '+s.endTime:'')))].join('، ')||'—';
        return `
            <td class="py-4 px-5 font-medium">${escapeHtml(item.name)}</td>
            <td class="py-4 px-5">${escapeHtml(item.branchName)}</td>
            <td class="py-4 px-5">${escapeHtml(item.course || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.start || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(item.end || '—')}</td>
            <td class="py-4 px-5">${escapeHtml(days)}</td>
            <td class="py-4 px-5">${escapeHtml(times)}</td>
            <td class="py-4 px-5">
                ${window.termPermissions?.isReceptionist?`<span class="px-3 py-1 rounded-full text-xs ${statusClass || statusBadgeClass(item.status)}">${escapeHtml(item.status)}</span>`:`<button type="button" onclick="cycleTermStatus(${item.id})" class="px-3 py-1 rounded-full text-xs ${statusClass || statusBadgeClass(item.status)}">${escapeHtml(item.status)}</button>`}
            </td>
            <td class="py-4 px-5 text-left">
                <div class="inline-flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button onclick="viewTerm(${item.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                    <button type="button" data-term-action="inline-edit" data-term-id="${item.id}" onclick="toggleTermInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm">ویرایش</button>
                    <button type="button" data-term-action="inline-attendance" data-term-id="${item.id}" onclick="toggleTermInlineAttendance(${item.id})" class="text-emerald-600 hover:underline text-sm">حضور و غیاب</button>
                    <button onclick="deleteTerm(${item.id})" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </div>
            </td>
        `;
    };

    window.getTermEmptyRowHTML = function () {
        return `<tr><td colspan="9" class="py-12 text-center text-gray-400">هیچ ترمی یافت نشد</td></tr>`;
    };

    window.getTermInlineExpandRowHTML = function (item) {
        return `<td colspan="9" class="p-5 border-t">${window.getTermInlineEditRowHTML ? window.getTermInlineEditRowHTML(item) : ''}</td>`;
    };

    window.getTermInlineAttendanceRowHTML = function (item) {
        return `<td colspan="9" class="p-5 border-t">${window.getTermAttendancePanelHTML ? window.getTermAttendancePanelHTML(item, true) : ''}</td>`;
    };

    window.getTermTeacherFieldHTML = function (item) {
        item = item || {};
        const teachers = (typeof getTermTeacherOptions === 'function') ? getTermTeacherOptions() : [];
        return `
            <div class="border border-gray-200 rounded-2xl p-4 mb-3 term-teacher-item">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label class="text-xs text-gray-500 mb-1 block">استاد</label>
                        <select onchange="window.refreshTermSelectionOptionsForInput(this)" class="term-teacher-select w-full border border-gray-300 rounded-2xl py-3 px-4">
                            <option value="">انتخاب استاد</option>
                            ${renderOptions(teachers, item.id || item.name || '')}
                        </select>
                    </div>
                    <button type="button" onclick="if (document.querySelectorAll('.term-teacher-item').length > 1) this.closest('.term-teacher-item').remove(); else alert('حداقل یک استاد لازم است')" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
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
                        <select onchange="window.refreshTermSelectionOptionsForInput(this)" class="term-student-select w-full border border-gray-300 rounded-2xl py-3 px-4">
                            <option value="">انتخاب هنرجو</option>
                            ${renderOptions(students, item.id || item.name || '')}
                        </select>
                    </div>
                    <button type="button" onclick="if (document.querySelectorAll('.term-student-item').length > 1) this.closest('.term-student-item').remove(); else alert('حداقل یک هنرجو لازم است')" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
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
                    <button type="button" onclick="if (document.querySelectorAll('.term-installment-item').length > 1) this.closest('.term-installment-item').remove(); else alert('حداقل یک قسط لازم است')" class="text-red-500 text-sm px-3 py-2 hover:underline">حذف</button>
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
        const installmentCount = (item.installments && item.installments.length) ? item.installments.length : 1;
        const sessions = item.sessions || [];
        const sessionCount = sessions.length || 8;

        const tContainer = prefix ? (prefix + 'TeachersContainer') : 'termTeachersContainer';
        const sContainer = prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer';
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
                    <select id="${id('Course')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" onchange="window.updateTermCourseCapacityHint('${prefix}')">
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
                    <input id="${id('Cost')}" type="number" min="0" value="${escapeHtml(item.cost ?? '')}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5" onchange="window.syncTermInstallments('${prefix}')" oninput="window.syncTermInstallments('${prefix}')">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تعداد اقساط</label>
                    <input id="${id('InstallmentCount')}" type="number" min="1" value="${installmentCount}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
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
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">جلسات (تاریخ هر جلسه)</label>
                <p class="text-xs text-gray-400 mb-2">تاریخ اولین جلسه = شروع ترم · تاریخ آخرین جلسه = پایان ترم</p>
                <div id="${sessContainer}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    ${window.getTermSessionFieldsHTML(sessionList)}
                </div>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
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
                <label class="block text-sm font-medium mb-2">استادها</label>
                <div id="${tContainer}">${teachers.map(function (t) { return window.getTermTeacherFieldHTML(t); }).join('')}</div>
                <button type="button" onclick="addTermTeacherField('${tContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن استاد</button>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">هنرجویان</label>
                <div id="${sContainer}">${students.map(function (s) { return window.getTermStudentFieldHTML(s); }).join('')}</div>
                <div id="${prefix ? (prefix + 'CourseCapacityHint') : 'termCourseCapacityHint'}" class="text-xs text-gray-500 mb-2">${item.course ? `ظرفیت هنرجویان این دوره ${window.getTermCourseCapacity(item.courseId || item.course)} نفر است` : 'ظرفیت دوره بعد از انتخاب دوره نمایش داده می‌شود'}</div>
                <button type="button" onclick="addTermStudentField('${sContainer}')" class="mt-2 text-sm text-indigo-600">+ افزودن هنرجو</button>
            </div>

            <div class="mt-6">
                <div class="text-xs text-gray-500">تعداد اقساط را مشخص کنید و مبلغ کل ترم به‌صورت خودکار میان اقساط تقسیم می‌شود.</div>
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
        const faValue = value => value === undefined || value === null || value === '' ? 'تعریف نشده' : value;
        return `
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-gray-50 rounded-2xl py-3"><div class="text-xs text-gray-400">کل رکورد</div><div class="font-bold mt-1">${faValue(stats.total)}</div></div>
                <div class="bg-green-50 rounded-2xl py-3"><div class="text-xs text-gray-400">حاضر</div><div class="font-bold mt-1 text-green-700">${faValue(stats.present)}</div></div>
                <div class="bg-red-50 rounded-2xl py-3"><div class="text-xs text-gray-400">غایب</div><div class="font-bold mt-1 text-red-600">${faValue(stats.absent)}</div></div>
                <div class="bg-indigo-50 rounded-2xl py-3"><div class="text-xs text-gray-400">درصد حضور</div><div class="font-bold mt-1 text-indigo-700">${stats.rate === undefined || stats.rate === null || stats.rate === '' ? 'تعریف نشده' : stats.rate + '٪'}</div></div>
            </div>`;
    }

    window.getTermDetailsModalHTML = function (item) {
        const teachers = (item.teachers || []).map(function (t) { return escapeHtml(t.name || t); }).join('، ') || '—';
        const students = (item.students || []).map(function (s) { return escapeHtml(s.name || s); }).join('، ') || '—';
        const installments = (item.installments || []).map(function (x, i) {
            return `<div class="flex justify-between border-b pb-2 text-sm"><span>قسط ${i + 1}</span><span class="font-medium">${Number(x.amount || 0).toLocaleString('fa-IR')}</span></div>`;
        }).join('') || '<p class="text-sm text-gray-400">قسطی ثبت نشده</p>';
        const sessions = (item.sessions || []).map(function (s, i) {
            return `<div class="flex justify-between border-b pb-2 text-sm"><span>جلسه ${i + 1}</span><span class="font-medium">${escapeHtml(s.date || '—')}، ${escapeHtml(s.startTime || '—')} تا ${escapeHtml(s.endTime || '—')}</span></div>`;
        }).join('') || '<p class="text-sm text-gray-400">جلسه‌ای ثبت نشده</p>';

        return `
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
                <div class="bg-white rounded-3xl w-full max-w-6xl my-4 shadow-2xl" onclick="event.stopPropagation()">
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
                    <div class="p-6 md:p-8 space-y-6">
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
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
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

// فرم دیتابیس‌محور ترم
(function(){
const esc=v=>String(v??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const opts=(rows,value)=>rows.map(x=>`<option value="${x.id}" ${String(x.id)===String(value)?'selected':''}>${esc(x.name)}</option>`).join('');
window.getTermTimeOptions=function(times,value){const rows=Array.isArray(times)?times:[];return '<option value="">انتخاب ساعت</option>'+rows.map(time=>`<option value="${time}" ${time===value?'selected':''}>${time}</option>`).join('');};
function people(prefix,type,capacity,selected){const rows=[];for(let i=0;i<capacity;i++){const current=selected[i]||{};rows.push(`<div class="mb-3"><label class="mb-1 block text-xs text-gray-500">${type==='teacher'?'استاد':'هنرجو'} ${i+1}</label><select class="term-${type}-select w-full rounded-2xl border px-4 py-3" data-person-index="${i}" onchange="refreshTermPeople('${prefix}','${type}')" ${i&&!current.id?'disabled':''}><option value="">انتخاب کنید</option><option value="${current.id||''}" selected>${esc(current.name||'')}</option></select></div>`);}return rows.join('');}
function duration(s){if(!s.startTime||!s.endTime)return 90;const[a,b]=s.startTime.split(':').map(Number),[c,d]=s.endTime.split(':').map(Number);return Math.max(1,c*60+d-a*60-b);}function form(item={},prefix=''){const id=n=>prefix?prefix+n:'term'+n,course=item.courseId||'',selectedCourse=(window.termCourses||[]).find(x=>x.id==course),branch=selectedCourse?.organizationUserId||window.termBranches?.[0]?.id||'',classroom=item.classroomId||'',sessions=item.sessions?.length?item.sessions:[{date:'',startTime:'10:00',endTime:'11:30'}],teacherCapacity=selectedCourse?.teacher_capacity||1,studentCapacity=selectedCourse?.student_capacity||1,isReceptionist=!!window.termPermissions?.isReceptionist,isBranchContext=!!window.termPermissions?.isBranchContext,status=item.status_code||(isReceptionist?'pending':'open'),cost=item.cost??15000000,repeatType=sessions.length===1?'no-period':(item.repeatType||'no-period');return `<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
<div><label class="mb-2 block text-sm font-medium">نام ترم *</label><input id="${id('Name')}" value="${esc(item.name||'')}" class="w-full rounded-2xl border px-5 py-3.5"></div>
<div class="${isBranchContext?'hidden':''}"><label class="mb-2 block text-sm font-medium">سازمان *</label><select id="${id('Branch')}" onchange="refreshTermDependencies('${prefix}')" class="w-full rounded-2xl border px-5 py-3.5">${opts(window.termBranches||[],branch)}</select></div>
<div><label class="mb-2 block text-sm font-medium">دوره مرتبط *</label><select id="${id('Course')}" onchange="refreshTermCourse('${prefix}')" ${branch?'':'disabled'} class="w-full rounded-2xl border px-5 py-3.5 disabled:bg-gray-100"><option value="">انتخاب دوره</option>${opts((window.termCourses||[]).filter(x=>x.organizationUserId==branch),course)}</select></div>
<div><label class="mb-2 block text-sm font-medium">کلاس برگزاری *</label><select id="${id('Classroom')}" onchange="refreshTermPeople('${prefix}','teacher');refreshTermPeople('${prefix}','student');syncTermDateAvailability('${prefix}');refreshAllTermSessionAvailability('${prefix}')" ${branch?'':'disabled'} class="w-full rounded-2xl border px-5 py-3.5 disabled:bg-gray-100"><option value="">انتخاب کلاس</option>${opts(window.termClassrooms||[],classroom)}</select></div>
<div><label class="mb-2 block text-sm font-medium">نوع پول *</label><select id="${id('Currency')}" class="w-full rounded-2xl border px-5 py-3.5">${opts(window.termCurrencies||[],item.currencyId||window.termCurrencies?.[0]?.id)}</select></div>
<div><label class="mb-2 block text-sm font-medium">هزینه ترم</label><input id="${id('Cost')}" type="number" min="0" value="${cost}" oninput="syncTermFinancialFields('${prefix}')" class="w-full rounded-2xl border px-5 py-3.5"></div>
<div><label class="mb-2 block text-sm font-medium">تعداد اقساط</label><input id="${id('InstallmentCount')}" type="number" min="1" max="${Math.max(2,sessions.length)}" value="${Math.min(item.installmentCount||1,Math.max(2,sessions.length))}" class="w-full rounded-2xl border px-5 py-3.5 disabled:bg-gray-100"><p class="mt-1 text-xs text-gray-400">حداکثر ${Math.max(2,sessions.length)} قسط</p></div>
<div><label class="mb-2 block text-sm font-medium">تخفیف</label><select id="${id('Discount')}" class="w-full rounded-2xl border px-5 py-3.5 disabled:bg-gray-100"><option value="">بدون تخفیف</option>${opts(window.termDiscounts||[],item.discountId)}</select><button id="${id('AddDiscount')}" type="button" onclick="openAddTermDiscountModal('${prefix}')" class="mt-2 text-sm text-indigo-600 disabled:text-gray-400" >+ افزودن تخفیف جدید</button></div>
${isReceptionist?`<input id="${id('Status')}" type="hidden" value="pending">`:`<div><label class="mb-2 block text-sm font-medium">وضعیت</label><select id="${id('Status')}" onchange="syncTermPeopleVisibility('${prefix}')" class="w-full rounded-2xl border px-5 py-3.5"><option value="open" ${status==='open'?'selected':''}>باز</option><option value="ongoing" ${status==='ongoing'?'selected':''}>در حال برگزاری</option></select></div>`}
<div><label class="mb-2 block text-sm font-medium">تعداد جلسات</label><input id="${id('SessionCount')}" type="number" min="1" max="100" value="${sessions.length}" onchange="rebuildDbTermSessions('${prefix}')" class="w-full rounded-2xl border px-5 py-3.5"></div>
<div id="${id('RepeatTypeField')}" class="${sessions.length===1?'hidden':''}"><label class="mb-2 block text-sm font-medium">دوره تکرار</label><select id="${id('RepeatType')}" onchange="refreshTermSessionDates('${prefix}')" class="w-full rounded-2xl border px-5 py-3.5"><option value="week" ${repeatType==='week'?'selected':''}>هفتگی</option><option value="2-week" ${repeatType==='2-week'?'selected':''}>دو هفته یک‌بار</option><option value="3-week" ${repeatType==='3-week'?'selected':''}>سه هفته یک‌بار</option><option value="4-week" ${repeatType==='4-week'?'selected':''}>چهار هفته یک‌بار</option><option value="month" ${repeatType==='month'?'selected':''}>ماهانه</option><option value="year" ${repeatType==='year'?'selected':''}>سالانه</option><option value="no-period" ${repeatType==='no-period'?'selected':''}>سایر</option></select></div></div>
<div class="mt-6 w-full"><label class="mb-2 block text-sm font-medium">تاریخ، منطقه زمانی، ساعت شروع و مدت جلسه</label><div id="${id('SessionsContainer')}" data-term-id="${item.id||0}" class="grid w-full grid-cols-1 gap-3">${sessions.map((s,i)=>`<div class="w-full rounded-2xl border p-4"><label class="mb-2 block text-sm font-medium">جلسه ${i+1}</label><div class="grid grid-cols-1 gap-2 sm:grid-cols-4"><input type="date" value="${s.date||''}" data-session-index="${i}" onchange="refreshTermSessionDates('${prefix}');refreshAllTermSessionAvailability('${prefix}')" class="term-session-date w-full rounded-xl border px-3 py-2.5 disabled:bg-gray-100" ${(!course||!classroom)||(i&&repeatType!=='no-period')?'disabled':''}><select class="term-session-timezone w-full rounded-xl border px-3 py-2.5" onchange="termTimezoneChanged('${prefix}',${i},this)" aria-label="منطقه زمانی">${opts(window.termTimezones||[],s.timezoneId||window.termTimezones?.find(x=>x.name==='Asia/Tehran')?.id||window.termTimezones?.[0]?.id)}</select><select disabled class="term-session-start w-full rounded-xl border px-3 py-2.5 disabled:bg-gray-100" ${i===0?`onchange="syncTermSessionTimes('${prefix}')"`:''} aria-label="ساعت شروع">${window.getTermTimeOptions(s.startTime?[s.startTime]:[],s.startTime||'')}</select><div class="relative"><input disabled type="number" min="5" max="1440" step="5" value="${duration(s)}" class="term-session-duration w-full rounded-xl border px-3 py-2.5 pl-14 disabled:bg-gray-100" oninput="termDurationChanged('${prefix}',${i})" onchange="termDurationChanged('${prefix}',${i})" aria-label="مدت جلسه"><span class="pointer-events-none absolute left-3 top-3 text-xs text-gray-400">دقیقه</span></div></div></div>`).join('')}</div></div>
<div class="mt-6 grid gap-5"><input id="${id('Summary')}" value="${esc(item.summary||'')}" placeholder="خلاصه ترم" class="w-full rounded-2xl border px-5 py-3.5"><textarea id="${id('Description')}" rows="3" placeholder="شرح ترم" class="w-full rounded-2xl border px-5 py-3.5">${esc(item.description||'')}</textarea></div>
<div id="${id('TeachersField')}" class="mt-6"><h3 class="mb-3 font-medium">استادها (ظرفیت ${teacherCapacity})</h3><div id="${id('TeachersContainer')}">${people(prefix,'teacher',teacherCapacity,item.teachers||[])}</div></div>
<div id="${id('StudentsField')}" class="mt-6 ${status==='ongoing'||status==='pending'?'hidden':''}"><h3 class="mb-3 font-medium">هنرجویان (ظرفیت ${studentCapacity})</h3><div id="${id('StudentsContainer')}">${people(prefix,'student',studentCapacity,item.students||[])}</div></div>`;}
window.getTermAddModalHTML=()=>`<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4"><div class="my-8 w-full max-w-4xl overflow-hidden rounded-3xl bg-white"><div class="flex justify-between border-b p-6"><h2 class="text-2xl font-bold">افزودن ترم جدید</h2><button onclick="closeModal()">×</button></div><div class="max-h-[78vh] space-y-6 overflow-y-auto p-7">${form()}<div class="flex gap-3"><button onclick="saveTerm()" class="flex-1 rounded-2xl bg-indigo-600 py-3 text-white">ذخیره ترم</button><button onclick="closeModal()" class="flex-1 rounded-2xl border py-3">انصراف</button></div></div></div></div>`;
window.getTermEditModalHTML=item=>`<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4"><div class="my-8 w-full max-w-4xl overflow-hidden rounded-3xl bg-white"><div class="flex justify-between border-b p-6"><h2 class="text-2xl font-bold">ویرایش ترم</h2><button onclick="closeModal()">×</button></div><div class="max-h-[78vh] space-y-6 overflow-y-auto p-7">${form(item,'editTerm')}<div class="flex gap-3"><button onclick="saveEditedTerm(${item.id})" class="flex-1 rounded-2xl bg-indigo-600 py-3 text-white">ذخیره تغییرات</button><button onclick="closeModal()" class="flex-1 rounded-2xl border py-3">انصراف</button></div></div></div></div>`;
window.getTermInlineEditRowHTML=item=>`<div class="space-y-6">${form(item,'inlineTerm'+item.id)}<button onclick="saveInlineTerm(${item.id})" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white">ذخیره</button></div>`;
})();
