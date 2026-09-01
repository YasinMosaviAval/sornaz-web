// ==================== لیست‌های قابل گسترش ====================
let allTermCurrencies = [
    { id: 1, name: 'تومان' },
    { id: 2, name: 'دلار' },
    { id: 3, name: 'یورو' }
];
let allTermDiscounts = [
    { id: 1, name: 'بدون تخفیف' },
    { id: 2, name: '۱۰٪' },
    { id: 3, name: '۲۰٪' },
    { id: 4, name: '۳۰٪' },
    { id: 5, name: 'ویژه' }
];
let allTermCourseOptions = [
    { id: 1, name: 'دوره پیانو مبتدی' },
    { id: 2, name: 'دوره گیتار متوسط' },
    { id: 3, name: 'دوره ویولن پیشرفته' },
    { id: 4, name: 'دوره آواز کلاسیک' },
    { id: 5, name: 'دوره درام کودکان' },
    { id: 6, name: 'دوره سنتور' },
    { id: 7, name: 'دوره تئوری موسیقی' },
    { id: 8, name: 'دوره کمانچه' }
];
let allTermClassroomOptions = [
    { id: 1, name: 'کلاس پیانو ۱' },
    { id: 2, name: 'کلاس گیتار A' },
    { id: 3, name: 'سالن تمرین گروهی' },
    { id: 4, name: 'کلاس ویولن' },
    { id: 5, name: 'کلاس آواز' },
    { id: 6, name: 'کلاس عمومی' }
];
let allTermTeacherOptions = [
    { id: 1, name: 'علی رضایی' },
    { id: 2, name: 'سارا موسوی' },
    { id: 3, name: 'رضا کریمی' },
    { id: 4, name: 'مینا احمدی' },
    { id: 5, name: 'حسین مهدوی' }
];
let allTermStudentOptions = [
    { id: 1, name: 'محمد نوری' },
    { id: 2, name: 'زهرا حسینی' },
    { id: 3, name: 'امیر جعفری' },
    { id: 4, name: 'نگار کاظمی' },
    { id: 5, name: 'پارسا محمدی' },
    { id: 6, name: 'هستی بهرامی' },
    { id: 7, name: 'کیان احمدی' },
    { id: 8, name: 'آوا موسوی' }
];

const termStatuses = ['در حال برگزاری', 'در انتظار', 'پایان‌یافته', 'تعلیق‌شده'];
const termNameTemplates = [
    'ترم پاییز', 'ترم زمستان', 'ترم بهار', 'ترم تابستان',
    'ترم فشرده', 'ترم ویژه نوروز', 'ترم پیشرفته', 'ترم مبتدی'
];

function getTermBranches() {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function buildOptionMarkup(options, selectedValue) {
    return (options || []).map(function (option) {
        const value = option.value ?? option.id ?? option.name ?? option;
        const label = option.label ?? option.name ?? option;
        const isSelected = String(value) === String(selectedValue) ? 'selected' : '';
        return '<option value="' + escapeHtml(value) + '" ' + isSelected + '>' + escapeHtml(label) + '</option>';
    }).join('');
}

function getTermPrefixFromContainer(containerId) {
    if (!containerId) return '';
    return containerId.replace(/(TeachersContainer|StudentsContainer|InstallmentsContainer)$/, '');
}

function getTermCourseCapacityFromContext(prefix) {
    const courseField = document.getElementById(prefix ? (prefix + 'Course') : 'termCourse');
    const courseId = courseField ? courseField.value : '';
    return window.getTermCourseCapacity(courseId);
}

window.getTermCourseOptions = function () {
    if (typeof allCourses !== 'undefined' && allCourses.length) {
        return allCourses.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
    }
    return allTermCourseOptions.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
};

window.getTermClassroomOptions = function () {
    if (typeof allClassrooms !== 'undefined' && allClassrooms.length) {
        return allClassrooms.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
    }
    return allTermClassroomOptions.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
};

window.getTermTeacherOptions = function () {
    if (typeof allStaff !== 'undefined' && allStaff.length) {
        return allStaff.filter(function (s) { return s.type === 'teacher' || !s.type; })
            .map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return allTermTeacherOptions.map(function (t) { return { value: t.id, label: t.name, id: t.id, name: t.name }; });
};

window.getTermStudentOptions = function () {
    if (typeof allStudents !== 'undefined' && allStudents.length) {
        return allStudents.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return allTermStudentOptions.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
};

function randomDateISO(startYear, endYear) {
    const start = new Date(startYear, 0, 1).getTime();
    const end = new Date(endYear, 11, 31).getTime();
    const d = new Date(start + Math.random() * (end - start));
    return d.toISOString().split('T')[0];
}

function buildSessions(count) {
    const sessions = [];
    let base = new Date(2024, Math.floor(Math.random() * 8), 1 + Math.floor(Math.random() * 20));
    for (let i = 0; i < count; i++) {
        const d = new Date(base.getTime() + i * 7 * 24 * 60 * 60 * 1000);
        sessions.push({ date: d.toISOString().split('T')[0] });
    }
    return sessions;
}

function pickN(arr, n) {
    const shuffled = arr.slice().sort(function () { return Math.random() - 0.5; });
    return shuffled.slice(0, n).map(function (x) { return { id: x.id, name: x.name }; });
}

// ==================== ۴۰ ترم نمونه ====================
const termCourseCapacities = { 1: 8, 2: 10, 3: 12, 4: 6, 5: 7, 6: 9, 7: 8, 8: 10 };
window.getTermCourseCapacity = function (courseId) {
    if (!courseId && courseId !== 0) return 8;
    const normalized = Number(courseId);
    return termCourseCapacities[normalized] || 8;
};

window.updateTermCourseCapacityHint = async function (prefix) {
    const courseField = document.getElementById(prefix ? (prefix + 'Course') : 'termCourse');
    const hint = document.getElementById(prefix ? (prefix + 'CourseCapacityHint') : 'termCourseCapacityHint');
    if (!hint) return;
    const courseId = courseField ? courseField.value : '';
    const capacity = window.getTermCourseCapacity(courseId);
    hint.textContent = `ظرفیت هنرجویان این دوره ${capacity} نفر است`;
    window.refreshTermStudentFieldLimit(prefix);
    window.refreshTermStudentSelectionOptions(prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer');
};

window.syncTermInstallments = async function (prefix) {
    const costField = document.getElementById(prefix ? (prefix + 'Cost') : 'termCost');
    const container = document.getElementById(prefix ? (prefix + 'InstallmentsContainer') : 'termInstallmentsContainer');
    if (!costField || !container) return;
    const cost = Number(costField.value || 0);
    const items = container.querySelectorAll('.term-installment-item');
    if (!items.length) return;
};

window.refreshTermSelectionOptionsForInput = async function (selectEl) {
    if (!selectEl) return;
    const containerEl = selectEl.closest('[id$="TeachersContainer"], [id$="StudentsContainer"]');
    if (!containerEl) return;
    if (containerEl.id.endsWith('TeachersContainer')) {
        window.refreshTermTeacherSelectionOptions(containerEl.id);
    } else if (containerEl.id.endsWith('StudentsContainer')) {
        window.refreshTermStudentSelectionOptions(containerEl.id);
    }
};

window.refreshTermTeacherSelectionOptions = async function (containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const selects = Array.from(container.querySelectorAll('.term-teacher-select'));
    const options = (typeof window.getTermTeacherOptions === 'function') ? window.getTermTeacherOptions() : [];
    selects.forEach(function (selectEl) {
        const currentValue = selectEl.value;
        const usedValues = selects.filter(function (item) { return item !== selectEl && item.value; }).map(function (item) { return String(item.value); });
        const filtered = options.filter(function (option) {
            const optionValue = String(option.value ?? option.id ?? option.name ?? option);
            return !usedValues.includes(optionValue) || optionValue === String(currentValue);
        });
        selectEl.innerHTML = '<option value="">انتخاب استاد</option>' + buildOptionMarkup(filtered, currentValue);
        if (!filtered.some(function (option) { return String(option.value ?? option.id ?? option.name ?? option) === String(currentValue); })) {
            selectEl.value = '';
        }
    });
};

window.refreshTermStudentSelectionOptions = async function (containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const selects = Array.from(container.querySelectorAll('.term-student-select'));
    const options = (typeof window.getTermStudentOptions === 'function') ? window.getTermStudentOptions() : [];
    selects.forEach(function (selectEl) {
        const currentValue = selectEl.value;
        const usedValues = selects.filter(function (item) { return item !== selectEl && item.value; }).map(function (item) { return String(item.value); });
        const filtered = options.filter(function (option) {
            const optionValue = String(option.value ?? option.id ?? option.name ?? option);
            return !usedValues.includes(optionValue) || optionValue === String(currentValue);
        });
        selectEl.innerHTML = '<option value="">انتخاب هنرجو</option>' + buildOptionMarkup(filtered, currentValue);
        if (!filtered.some(function (option) { return String(option.value ?? option.id ?? option.name ?? option) === String(currentValue); })) {
            selectEl.value = '';
        }
    });
};

window.refreshTermStudentFieldLimit = async function (prefix) {
    const containerId = prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer';
    const container = document.getElementById(containerId);
    if (!container) return;
    const capacity = getTermCourseCapacityFromContext(prefix);
    const currentCount = container.querySelectorAll('.term-student-item').length;
    const buttons = Array.from(document.querySelectorAll('button')).filter(function (btn) {
        return btn.getAttribute('onclick') && btn.getAttribute('onclick').indexOf('addTermStudentField(\'' + containerId + '\'') !== -1;
    });
    buttons.forEach(function (btn) {
        btn.disabled = currentCount >= capacity;
    });
};

function buildInstallmentsFromCount(totalCost, count) {
    const safeCount = Math.max(1, parseInt(count || '1', 10) || 1);
    const safeCost = Number(totalCost || 0);
    const base = Math.floor(safeCost / safeCount);
    const remainder = safeCost % safeCount;
    return Array.from({ length: safeCount }, function (_, index) {
        return { amount: index === safeCount - 1 ? base + remainder : base };
    });
}

function validateTermData(data) {
    if (!data.name) {
        alert('نام ترم الزامی است');
        return false;
    }
    const teacherIds = (data.teachers || []).map(function (teacher) { return String(teacher && teacher.id ? teacher.id : ''); }).filter(Boolean);
    if (teacherIds.length !== new Set(teacherIds).size) {
        alert('هر استاد فقط یک بار قابل انتخاب است');
        return false;
    }
    const studentIds = (data.students || []).map(function (student) { return String(student && student.id ? student.id : ''); }).filter(Boolean);
    if (studentIds.length !== new Set(studentIds).size) {
        alert('هر هنرجو فقط یک بار قابل انتخاب است');
        return false;
    }
    const courseCapacity = window.getTermCourseCapacity(data.courseId);
    if (data.students.length > courseCapacity) {
        alert('تعداد هنرجویان برای این دوره بیش از ظرفیت مجاز است');
        return false;
    }
    return true;
}

let allTerms = [];
(function buildSampleTerms() {
    const branches = getTermBranches();
    const courses = allTermCourseOptions;
    const classrooms = allTermClassroomOptions;
    for (let i = 1; i <= 40; i++) {
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const course = courses[Math.floor(Math.random() * courses.length)];
        const classroom = classrooms[Math.floor(Math.random() * classrooms.length)];
        const sessionCount = 6 + Math.floor(Math.random() * 6);
        const sessions = buildSessions(sessionCount);
        const teachers = pickN(allTermTeacherOptions, 1 + Math.floor(Math.random() * 2));
        const students = pickN(allTermStudentOptions, 2 + Math.floor(Math.random() * 4));
        const cost = (5 + Math.floor(Math.random() * 20)) * 500000;
        const installments = [
            { amount: Math.floor(cost / 2) },
            { amount: Math.floor(cost / 2) }
        ];
        const currency = allTermCurrencies[Math.floor(Math.random() * allTermCurrencies.length)].name;
        const discount = allTermDiscounts[Math.floor(Math.random() * allTermDiscounts.length)].name;
        const status = termStatuses[Math.floor(Math.random() * termStatuses.length)];
        const baseName = termNameTemplates[Math.floor(Math.random() * termNameTemplates.length)];

        // sample attendance
        const attendance = {};
        sessions.forEach(function (_, si) {
            const tMap = {};
            const sMap = {};
            teachers.forEach(function (t) { tMap[String(t.id)] = Math.random() > 0.2; });
            students.forEach(function (s) { sMap[String(s.id)] = Math.random() > 0.3; });
            attendance[String(si)] = { teachers: tMap, students: sMap };
        });

        allTerms.push({
            id: i,
            name: baseName + ' ۱۴۰۴ - ' + i,
            branchId: branch.id,
            branchName: branch.name,
            courseId: course.id,
            course: course.name,
            classroomId: classroom.id,
            classroom: classroom.name,
            currency: currency,
            discount: discount,
            cost: cost,
            summary: 'خلاصه ' + baseName + ' برای ' + course.name,
            description: 'شرح ترم ' + baseName + ' مرتبط با ' + course.name + ' در ' + branch.name,
            status: status,
            teachers: teachers,
            students: students,
            sessions: sessions,
            installments: installments,
            start: sessions[0] ? sessions[0].date : '',
            end: sessions.length ? sessions[sessions.length - 1].date : '',
            attendance: attendance
        });
    }
})();

// ==================== state ====================
let termsCurrentPage = 1;
const termsPerPage = 10;
let filteredTerms = allTerms.slice();
let currentTermBranch = 'all';
let editingTermRowId = null;
let attendanceTermRowId = null;
let termsRealtimeVersion = null;
let termsRealtimeBusy = false;
let termSortField = '';
let termSortDirection = 'asc';

function closeTermInlineEdit(render=true){if(editingTermRowId===null)return false;editingTermRowId=null;if(render)renderTermsTable(filteredTerms);return true;}
function closeTermInlineAttendance(render=true){if(attendanceTermRowId===null)return false;attendanceTermRowId=null;if(render)renderTermsTable(filteredTerms);return true;}
async function pollTermsRealtime(){if(termsRealtimeBusy||document.hidden||(!document.getElementById('termsTable')&&!document.getElementById('financeTable')))return;termsRealtimeBusy=true;try{const state=await termApi('/academy/admin/terms/realtime-version');if(termsRealtimeVersion===null){termsRealtimeVersion=state.version;return;}if(state.version!==termsRealtimeVersion){termsRealtimeVersion=state.version;editingTermRowId=null;attendanceTermRowId=null;if(document.getElementById('termsTable'))await loadTerms();if(document.getElementById('financeTable')&&typeof loadFinanceInvoices==='function')await loadFinanceInvoices();}}catch(error){}finally{termsRealtimeBusy=false;}}
if(!window.termInlineOutsideCloseBound){
    window.termInlineOutsideCloseBound=true;
    document.addEventListener('click',function(event){
        if((editingTermRowId===null&&attendanceTermRowId===null)||event.target.closest('.term-inline-expand'))return;
        const editButton=event.target.closest('[data-term-action="inline-edit"]');
        if(editButton&&Number(editButton.dataset.termId)===editingTermRowId){
            event.preventDefault();event.stopImmediatePropagation();closeTermInlineEdit();return;
        }
        const attendanceButton=event.target.closest('[data-term-action="inline-attendance"]');
        if(attendanceButton&&Number(attendanceButton.dataset.termId)===attendanceTermRowId){
            event.preventDefault();event.stopImmediatePropagation();closeTermInlineAttendance();return;
        }
        if(editingTermRowId!==null)closeTermInlineEdit();
        else closeTermInlineAttendance();
    },true);
}

const termPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام ترم' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'course', label: 'دوره مرتبط' },
    { field: 'start', label: 'تاریخ شروع' },
    { field: 'end', label: 'تاریخ پایان' },
    { field: 'status', label: 'وضعیت' },
    { field: 'cost', label: 'هزینه' }
];

function deriveStartEnd(sessions) {
    const dates = (sessions || []).map(function (s) { return s.date; }).filter(Boolean).sort();
    return {
        start: dates[0] || '',
        end: dates.length ? dates[dates.length - 1] : ''
    };
}

window.getTermAttendanceStats = function (item) {
    const att = item.attendance || {};
    let present = 0, total = 0;
    Object.keys(att).forEach(function (k) {
        const row = att[k] || {};
        Object.keys(row.teachers || {}).forEach(function (id) {
            total++;
            if (row.teachers[id]) present++;
        });
        Object.keys(row.students || {}).forEach(function (id) {
            total++;
            if (row.students[id]) present++;
        });
    });
    const rate = total ? Math.round((present / total) * 100) : 0;
    return { present: present, absent: total - present, total: total, rate: rate };
};

// ==================== sort ====================
function sortTermItems() {
    if (!termSortField) return;
    filteredTerms.sort(function (a, b) {
        let aValue = a[termSortField];
        let bValue = b[termSortField];
        if (termSortField === 'cost') {
            aValue = Number(aValue) || 0;
            bValue = Number(bValue) || 0;
        } else if (termSortField === 'start' || termSortField === 'end') {
            aValue = new Date(aValue || 0);
            bValue = new Date(bValue || 0);
        } else {
            aValue = String(aValue || '').toLowerCase();
            bValue = String(bValue || '').toLowerCase();
        }
        if (aValue < bValue) return termSortDirection === 'asc' ? -1 : 1;
        if (aValue > bValue) return termSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateTermSortIcons = async function () {
    ['name', 'branchName', 'course', 'start', 'end', 'status'].forEach(function (field) {
        const icon = document.getElementById('termSortIcon-' + field);
        if (!icon) return;
        icon.textContent = termSortField === field
            ? (termSortDirection === 'asc' ? '↑' : '↓')
            : '↕';
    });
};

window.sortTermsBy = async function (field) {
    if (termSortField === field) {
        termSortDirection = termSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        termSortField = field;
        termSortDirection = 'asc';
    }
    sortTermItems();
    renderTermsTable(filteredTerms);
    updateTermSortIcons();
};

// ==================== branch tabs ====================
window.renderTermsBranchTabs = async function () {
    const container = document.getElementById('termsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.term-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    getTermBranches().forEach(function (b) {
        const active = currentTermBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'term-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { filterTermsByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterTermsByBranch = async function (branchId) {
    currentTermBranch = branchId;
    document.querySelectorAll('.term-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.term-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getTermBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterTerms();
};

window.renderTermCourseFilter = async function () {
    const select = document.getElementById('filterTermCourse');
    if (!select) return;
    const names = new Set(allTerms.map(function (t) { return t.course; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه دوره‌ها</option>' +
        Array.from(names).sort().map(function (n) {
            return '<option value="' + n + '"' + (n === current ? ' selected' : '') + '>' + n + '</option>';
        }).join('');
};

window.renderTermCurrencyFilter = async function () {
    const select = document.getElementById('filterTermCurrency');
    if (!select) return;
    const values = new Set(allTerms.map(function (t) { return t.currency; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه نوع پول</option>' +
        Array.from(values).sort().map(function (value) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + value + '</option>';
        }).join('');
};

window.renderTermDiscountFilter = async function () {
    const select = document.getElementById('filterTermDiscount');
    if (!select) return;
    const values = new Set(allTerms.map(function (t) { return t.discount; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه تخفیف‌ها</option>' +
        Array.from(values).sort().map(function (value) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + value + '</option>';
        }).join('');
};

window.renderTermClassroomFilter = async function () {
    const select = document.getElementById('filterTermClassroom');
    if (!select) return;
    const values = new Set(allTerms.map(function (t) { return t.classroom; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه کلاس‌ها</option>' +
        Array.from(values).sort().map(function (value) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + value + '</option>';
        }).join('');
};

window.renderTermInstallmentCountFilter = async function () {
    const select = document.getElementById('filterTermInstallmentCount');
    if (!select) return;
    const values = new Set(allTerms.map(function (t) { return (t.installments && t.installments.length) ? String(t.installments.length) : '1'; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه تعداد اقساط</option>' +
        Array.from(values).sort(function (a, b) { return Number(a) - Number(b); }).map(function (value) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + value + ' قسط</option>';
        }).join('');
};

window.renderTermFilters = async function () {
    window.renderTermCourseFilter();
    window.renderTermCurrencyFilter();
    window.renderTermDiscountFilter();
    window.renderTermClassroomFilter();
    window.renderTermInstallmentCountFilter();
};

window.filterTerms = async function () {
    const search = (document.getElementById('termSearch') && document.getElementById('termSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterTermStatus') && document.getElementById('filterTermStatus').value || '';
    const course = document.getElementById('filterTermCourse') && document.getElementById('filterTermCourse').value || '';
    const currency = document.getElementById('filterTermCurrency') && document.getElementById('filterTermCurrency').value || '';
    const discount = document.getElementById('filterTermDiscount') && document.getElementById('filterTermDiscount').value || '';
    const classroom = document.getElementById('filterTermClassroom') && document.getElementById('filterTermClassroom').value || '';
    const installmentCount = document.getElementById('filterTermInstallmentCount') && document.getElementById('filterTermInstallmentCount').value || '';
    const weekday = document.getElementById('filterTermWeekday') && document.getElementById('filterTermWeekday').value || '';

    filteredTerms = allTerms.filter(function (item) {
        const matchBranch = window.matchesOrganizationFilter(item,currentTermBranch);
        const matchSearch = !search || (item.name || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchCourse = !course || item.course === course;
        const matchCurrency = !currency || item.currency === currency;
        const matchDiscount = !discount || item.discount === discount;
        const matchClassroom = !classroom || item.classroom === classroom;
        const matchInstallmentCount = !installmentCount || (item.installments && item.installments.length === Number(installmentCount));
        const matchWeekday = weekday === '' || (item.sessions || []).some(function (session) { return session.date && String(new Date(session.date + 'T12:00:00').getDay()) === weekday; });
        return matchBranch && matchSearch && matchStatus && matchCourse && matchCurrency && matchDiscount && matchClassroom && matchInstallmentCount && matchWeekday;
    });

    termsCurrentPage = 1;
    sortTermItems();
    renderTermsTable(filteredTerms);
};

// ==================== table ====================
window.renderTermsTable = async function (list) {
    list = list || filteredTerms;
    const tbody = document.querySelector('#termsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / termsPerPage) || 1;
    if (termsCurrentPage > totalPages) termsCurrentPage = totalPages;

    const start = (termsCurrentPage - 1) * termsPerPage;
    const end = start + termsPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';

    if (!pageItems.length) {
        tbody.innerHTML = window.getTermEmptyRowHTML ? window.getTermEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const statusClass = {
                'باز': 'bg-green-100 text-green-700',
                'در حال برگزاری': 'bg-gray-100 text-gray-700',
                'پایان یافته': 'bg-red-100 text-red-700',
                'در انتظار تأیید': 'bg-yellow-100 text-yellow-700'
            }[item.status] || 'bg-gray-100 text-gray-600';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getTermRowHTML ? window.getTermRowHTML(item, statusClass) : '';
            tbody.appendChild(tr);

            if (editingTermRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 term-inline-expand';
                expandRow.innerHTML = window.getTermInlineExpandRowHTML ? window.getTermInlineExpandRowHTML(item) : '';
                tbody.appendChild(expandRow);
            } else if (attendanceTermRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 term-inline-expand';
                expandRow.innerHTML = window.getTermInlineAttendanceRowHTML ? window.getTermInlineAttendanceRowHTML(item) : '';
                tbody.appendChild(expandRow);
            }
        });
    }

    updateTermsPagination(list.length, start, end, totalPages);
    updateTermSortIcons();
};

function updateTermsPagination(total, start, end, totalPages) {
    const info = document.getElementById('termsPaginationInfo');
    if (info?.parentElement) info.parentElement.classList.toggle('hidden', totalPages <= 1);
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = 'نمایش ' + from + ' تا ' + to + ' از ' + total + ' ترم';
    }

    const pagination = document.getElementById('termsPaginationButtons');
    if (!pagination) return;

    let html = ''
        + '<button onclick="changeTermsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (termsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeTermsPage(' + (termsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (termsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';

    let startPage = Math.max(1, termsCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += '<button onclick="changeTermsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' +
            (i === termsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }

    html += ''
        + '<button onclick="changeTermsPage(' + (termsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (termsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeTermsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (termsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';

    pagination.innerHTML = html;
}

window.changeTermsPage = async function (page) {
    const totalPages = Math.ceil(filteredTerms.length / termsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    termsCurrentPage = page;
    renderTermsTable(filteredTerms);
};

// ==================== prompts for new types ====================
async function promptAddNamed(list, label, selectIds, inlineSuffix) {
    const name = (await AppDialog.prompt('نام ' + label + ' جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (list.some(function (x) { return x.name === name; })) return alert('این مورد قبلاً وجود دارد');
    list.push({ id: Date.now(), name: name });
    selectIds.forEach(function (id) {
        const sel = document.getElementById(id);
        if (!sel) return;
        const opt = document.createElement('option');
        opt.value = list[list.length - 1].id || name;
        opt.textContent = name;
        opt.selected = true;
        sel.appendChild(opt);
    });
}

window.promptAddTermCourse = async function () {
    const name = (await AppDialog.prompt('نام دوره جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (allTermCourseOptions.some(function (c) { return c.name === name; })) return alert('این دوره قبلاً وجود دارد');
    const item = { id: Date.now(), name: name };
    allTermCourseOptions.push(item);
    document.querySelectorAll('select[id$="Course"], select[id*="Course"]').forEach(function (sel) {
        if (!sel.id.includes('Course')) return;
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = name;
        opt.selected = true;
        sel.appendChild(opt);
    });
    renderTermCourseFilter();
};

window.promptAddTermCurrency = async function () {
    const name = (await AppDialog.prompt('نام واحد پول جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (allTermCurrencies.some(function (c) { return c.name === name; })) return alert('این واحد قبلاً وجود دارد');
    allTermCurrencies.push({ id: Date.now(), name: name });
    ['termCurrency', 'editTermCurrency'].forEach(function (id) {
        const sel = document.getElementById(id);
        if (!sel) return;
        const opt = document.createElement('option');
        opt.value = name;
        opt.textContent = name;
        opt.selected = true;
        sel.appendChild(opt);
    });
};

window.promptAddTermDiscount = async function () {
    const name = (await AppDialog.prompt('عنوان تخفیف جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (allTermDiscounts.some(function (d) { return d.name === name; })) return alert('این تخفیف قبلاً وجود دارد');
    allTermDiscounts.push({ id: Date.now(), name: name });
    ['termDiscount', 'editTermDiscount'].forEach(function (id) {
        const sel = document.getElementById(id);
        if (!sel) return;
        const opt = document.createElement('option');
        opt.value = name;
        opt.textContent = name;
        opt.selected = true;
        sel.appendChild(opt);
    });
};

window.promptAddTermClassroom = async function () {
    const name = (await AppDialog.prompt('نام کلاس جدید را وارد کنید:') || '').trim();
    if (!name) return;
    if (allTermClassroomOptions.some(function (c) { return c.name === name; })) return alert('این کلاس قبلاً وجود دارد');
    const item = { id: Date.now(), name: name };
    allTermClassroomOptions.push(item);
    document.querySelectorAll('select[id$="Classroom"]').forEach(function (sel) {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = name;
        opt.selected = true;
        sel.appendChild(opt);
    });
};

// ==================== multi fields helpers ====================
window.addTermTeacherField = async function (containerId) {
    const el = document.getElementById(containerId);
    if (el && window.getTermTeacherFieldHTML) {
        el.insertAdjacentHTML('beforeend', window.getTermTeacherFieldHTML({}));
        window.refreshTermTeacherSelectionOptions(containerId);
    }
};
window.addTermStudentField = async function (containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const prefix = getTermPrefixFromContainer(containerId);
    const capacity = getTermCourseCapacityFromContext(prefix);
    const currentCount = el.querySelectorAll('.term-student-item').length;
    if (currentCount >= capacity) {
        alert('حداکثر ' + capacity + ' هنرجو برای این دوره قابل انتخاب است');
        return;
    }
    if (window.getTermStudentFieldHTML) el.insertAdjacentHTML('beforeend', window.getTermStudentFieldHTML({}));
    window.refreshTermStudentSelectionOptions(containerId);
    window.refreshTermStudentFieldLimit(prefix);
};
window.addTermInstallmentField = async function (containerId) {
    const el = document.getElementById(containerId);
    if (el && window.getTermInstallmentFieldHTML) el.insertAdjacentHTML('beforeend', window.getTermInstallmentFieldHTML({}));
};

window.rebuildTermSessions = async function (prefix) {
    const countId = prefix ? (prefix + 'SessionCount') : 'termSessionCount';
    const containerId = prefix ? (prefix + 'SessionsContainer') : 'termSessionsContainer';
    const countEl = document.getElementById(countId);
    const container = document.getElementById(containerId);
    if (!countEl || !container) return;
    const count = Math.max(1, Math.min(40, parseInt(countEl.value || '8', 10) || 8));
    const existing = Array.from(container.querySelectorAll('.term-session-date')).map(function (inp) {
        return { date: inp.value };
    });
    const sessions = [];
    for (let i = 0; i < count; i++) {
        sessions.push(existing[i] || { date: '' });
    }
    container.innerHTML = window.getTermSessionFieldsHTML(sessions);
};

function readCollection(containerId, selector, mapper) {
    const container = document.getElementById(containerId);
    if (!container) return [];
    return Array.from(container.querySelectorAll(selector)).map(mapper).filter(Boolean);
}

function readTermForm(prefix) {
    if(window.termCourses&&window.termCourses.length){const value=name=>termField(prefix,name),course=value('Course'),room=value('Classroom'),currency=value('Currency'),discount=value('Discount'),repeat=value('RepeatType'),status=value('Status'),teachers=value('TeachersContainer'),students=value('StudentsContainer'),sessions=value('SessionsContainer'),installmentCount=Math.max(1,Number(value('InstallmentCount')?.value||1)),classroom=termClassrooms.find(x=>x.id===Number(room?.value||0)),endTime=(start,duration)=>{const[a,b]=(start||'00:00').split(':').map(Number),total=a*60+b+Number(duration||0);return String(Math.floor(total/60)%24).padStart(2,'0')+':'+String(total%60).padStart(2,'0')};return{name:value('Name')?.value.trim()||'',organizationUserId:Number(value('Branch')?.value||0),branchId:Number(classroom?.branchId||0),courseId:Number(course?.value||0),classroomId:Number(room?.value||0),currencyId:Number(currency?.value||0),cost:Number(value('Cost')?.value||0),installmentCount,installments:Array.from({length:installmentCount},()=>({amount:1})),discountId:Number(discount?.value||0),repeatType:repeat?.value||'no-period',status:status?.value||'pending',summary:value('Summary')?.value.trim()||'',description:value('Description')?.value.trim()||'',teachers:teachers?[...teachers.querySelectorAll('select')].filter(x=>x.value).map(x=>({id:Number(x.value),name:x.selectedOptions[0]?.textContent||''})):[],students:students?[...students.querySelectorAll('select')].filter(x=>x.value).map(x=>({id:Number(x.value),name:x.selectedOptions[0]?.textContent||''})):[],sessions:sessions?[...sessions.querySelectorAll('.term-session-date')].map((x,i)=>{const startTime=sessions.querySelectorAll('.term-session-start')[i]?.value||'',durationMinutes=Number(sessions.querySelectorAll('.term-session-duration')[i]?.value||0);return{date:x.value,startTime,endTime:endTime(startTime,durationMinutes),durationMinutes}}):[]};}
    const field = function (suffix) {
        return document.getElementById(prefix ? (prefix + suffix) : ('term' + suffix));
    };
    const name = field('Name') && field('Name').value.trim();
    const branchId = parseInt(field('Branch') && field('Branch').value, 10);
    const courseVal = field('Course') && field('Course').value;
    const courseSelect = field('Course');
    const courseLabel = courseSelect && courseSelect.selectedOptions[0] ? courseSelect.selectedOptions[0].textContent : '';
    const currency = field('Currency') && field('Currency').value;
    const discount = field('Discount') && field('Discount').value;
    const classroomVal = field('Classroom') && field('Classroom').value;
    const classroomSelect = field('Classroom');
    const classroomLabel = classroomSelect && classroomSelect.selectedOptions[0] ? classroomSelect.selectedOptions[0].textContent : '';
    const cost = parseFloat(field('Cost') && field('Cost').value || 0) || 0;
    const installmentCount = parseInt(field('InstallmentCount') && field('InstallmentCount').value || 1, 10) || 1;
    const status = field('Status') && field('Status').value || 'در حال برگزاری';
    const summary = field('Summary') && field('Summary').value.trim() || '';
    const description = field('Description') && field('Description').value.trim() || '';

    const tContainer = prefix ? (prefix + 'TeachersContainer') : 'termTeachersContainer';
    const sContainer = prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer';
    const sessContainer = prefix ? (prefix + 'SessionsContainer') : 'termSessionsContainer';

    const teachers = readCollection(tContainer, '.term-teacher-item', function (div) {
        const sel = div.querySelector('.term-teacher-select');
        if (!sel || !sel.value) return null;
        return { id: sel.value, name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value };
    }).filter(function (item, index, arr) {
        return item && arr.findIndex(function (candidate) { return candidate && String(candidate.id) === String(item.id); }) === index;
    });
    const students = readCollection(sContainer, '.term-student-item', function (div) {
        const sel = div.querySelector('.term-student-select');
        if (!sel || !sel.value) return null;
        return { id: sel.value, name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value };
    }).filter(function (item, index, arr) {
        return item && arr.findIndex(function (candidate) { return candidate && String(candidate.id) === String(item.id); }) === index;
    });
    const installments = buildInstallmentsFromCount(cost, installmentCount);
    const sessions = readCollection(sessContainer, '.term-session-date', function (inp) {
        return { date: inp.value || '' };
    });

    const branch = getTermBranches().find(function (b) { return b.id === branchId; });
    const se = deriveStartEnd(sessions);

    return {
        name: name,
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        courseId: courseVal,
        course: courseLabel || courseVal || '—',
        classroomId: classroomVal,
        classroom: classroomLabel && classroomLabel !== 'انتخاب کلاس' ? classroomLabel : (classroomVal || '—'),
        currency: currency,
        discount: discount,
        cost: cost,
        status: status,
        summary: summary,
        description: description,
        teachers: teachers,
        students: students,
        installments: installments,
        sessions: sessions,
        start: se.start,
        end: se.end
    };
}

function termEncode(data){const bytes=new TextEncoder().encode(JSON.stringify(data));let value='';bytes.forEach(b=>value+=String.fromCharCode(b));return btoa(value).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');}
async function termApi(url,data=null){const token=window.adminCsrfToken||'',options={method:data?'POST':'GET',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(data){options.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';options.headers['X-CSRF-TOKEN']=token;options.body=new URLSearchParams({_token:token,payload_b64:termEncode(data)}).toString();}const response=await fetch(url,options),raw=await response.text();let body;try{body=JSON.parse(raw)}catch(e){throw new Error('پاسخ معتبر JSON از سرور دریافت نشد.')}const envelope=body.data??body;if(!response.ok||envelope.success===false)throw new Error(envelope.message||'عملیات ترم ناموفق بود.');return envelope.data??envelope;}
window.loadTerms=async function(){const[data,courseData]=await Promise.all([termApi('/academy/admin/terms'),termApi('/academy/admin/courses')]);window.termBranches=(courseData.organizations||[]).map(x=>({id:x.user_id,name:x.name,kind:x.kind,organizationId:x.id,academyId:x.kind==='academy'?x.id:(data.branchAcademies||{})[x.id]||null}));window.termCourses=(courseData.courses||[]).map(x=>({...x,lessonId:x.lesson_id}));window.termTimezones=data.timezones||[];window.termClassrooms=data.classrooms||[];window.termCurrencies=data.currencies||[];window.termDiscounts=data.discounts||[];window.termMembers=data.members||[];window.termPermissions=data.permissions||{};window.allBranches=termBranches;allTermCurrencies=termCurrencies;allTermCourseOptions=termCourses;allTermClassroomOptions=termClassrooms;allTerms=data.terms||[];filteredTerms=allTerms.slice();if(document.getElementById('termsTable')){renderTermsBranchTabs();renderTermFilters();filterTerms();}return data;};
const readTermFormWithoutTimezones=readTermForm;readTermForm=function(prefix){const data=readTermFormWithoutTimezones(prefix),box=termField(prefix,'SessionsContainer');if(box){const zones=[...box.querySelectorAll('.term-session-timezone')],dates=[...box.querySelectorAll('.term-session-date')];data.sessions.forEach((session,index)=>session.timezoneId=Number(zones[index]?.value||0));data.skippedDates=dates.flatMap(input=>{try{return JSON.parse(input.dataset.skippedDates||'[]');}catch(_){return[];}});}return data;};
function termField(prefix,name){return document.getElementById(prefix?prefix+name:'term'+name)}
window.refreshTermDependencies=function(prefix){const organizationUserId=Number(termField(prefix,'Branch')?.value||0),organization=termBranches.find(x=>x.id===organizationUserId),course=termField(prefix,'Course'),room=termField(prefix,'Classroom');if(course){course.disabled=!organizationUserId;course.innerHTML='<option value="">انتخاب دوره</option>'+termCourses.filter(x=>x.organizationUserId===organizationUserId).map(x=>`<option value="${x.id}">${x.name}</option>`).join('');}if(room){const branchIds=organization?.kind==='branch'?[organization.organizationId]:termBranches.filter(x=>x.kind==='branch'&&x.academyId===organization?.organizationId).map(x=>x.organizationId);room.disabled=!organizationUserId;room.innerHTML='<option value="">انتخاب کلاس</option>'+termClassrooms.filter(x=>branchIds.includes(x.branchId)).map(x=>`<option value="${x.id}">${x.name}</option>`).join('');}syncTermDateAvailability(prefix);refreshAllTermSessionAvailability(prefix);};
window.refreshTermCourse=function(prefix){const course=termCourses.find(x=>x.id===Number(termField(prefix,'Course')?.value||0));syncTermDateAvailability(prefix);if(!course)return;const render=(type,capacity)=>{const box=termField(prefix,type==='teacher'?'TeachersContainer':'StudentsContainer');if(!box)return;box.innerHTML=Array.from({length:capacity},(_,i)=>`<div class="mb-3"><label class="mb-1 block text-xs">${type==='teacher'?'استاد':'هنرجو'} ${i+1}</label><select class="term-${type}-select w-full rounded-2xl border px-4 py-3" data-person-index="${i}" onchange="refreshTermPeople('${prefix}','${type}')" ${i?'disabled':''}><option value="">انتخاب کنید</option></select></div>`).join('');refreshTermPeople(prefix,type);};render('teacher',course.teacher_capacity);render('student',course.student_capacity);syncTermPeopleVisibility(prefix);};
window.refreshTermPeople=function(prefix,type){const classroom=Number(termField(prefix,'Classroom')?.value||0),branch=Number(termClassrooms.find(x=>x.id===classroom)?.branchId||0),course=termCourses.find(x=>x.id===Number(termField(prefix,'Course')?.value||0)),box=termField(prefix,type==='teacher'?'TeachersContainer':'StudentsContainer');if(!box||!course)return;const selects=[...box.querySelectorAll('select')],selected=selects.map(x=>x.value).filter(Boolean),available=termMembers.filter(x=>x.branchId===branch&&x.type===type&&(type!=='teacher'||x.lessonId===course.lessonId));selects.forEach((select,i)=>{const value=select.value;select.disabled=i>0&&!selects[i-1].value;select.innerHTML='<option value="">انتخاب کنید</option>'+available.filter(x=>!selected.includes(String(x.id))||String(x.id)===value).map(x=>`<option value="${x.id}" ${String(x.id)===value?'selected':''}>${x.name}</option>`).join('');});};
window.rebuildDbTermSessions=function(prefix){const count=Math.max(1,Number(termField(prefix,'SessionCount')?.value||1)),box=termField(prefix,'SessionsContainer'),dates=box?[...box.querySelectorAll('.term-session-date')].map(x=>x.value):[],starts=box?[...box.querySelectorAll('.term-session-start')].map(x=>x.value):[],durations=box?[...box.querySelectorAll('.term-session-duration')].map(x=>x.value):[],firstDuration=durations[0]||'90',termId=box?.dataset.termId||0,repeat=termField(prefix,'RepeatType'),repeatField=termField(prefix,'RepeatTypeField');if(count===1&&repeat)repeat.value='no-period';repeatField?.classList.toggle('hidden',count===1);if(box)box.innerHTML=Array.from({length:count},(_,i)=>`<div class="w-full rounded-2xl border p-4"><label class="mb-2 block text-sm font-medium">جلسه ${i+1}</label><div class="grid grid-cols-1 gap-2 sm:grid-cols-3">${window.getTermDateInputHTML(prefix,i,dates[i]||'',true)}<select disabled class="term-session-start w-full rounded-xl border px-3 py-2.5 disabled:bg-gray-100" ${i===0?`onchange="syncTermSessionTimes('${prefix}')"`:''}>${window.getTermTimeOptions(starts[i]?[starts[i]]:[],starts[i]||'')}</select><div class="relative"><input disabled type="number" min="5" max="1440" step="5" value="${durations[i]||firstDuration}" class="term-session-duration w-full rounded-xl border px-3 py-2.5 pl-14 disabled:bg-gray-100" oninput="termDurationChanged('${prefix}',${i})" onchange="termDurationChanged('${prefix}',${i})"><span class="pointer-events-none absolute left-3 top-3 text-xs text-gray-400">دقیقه</span></div></div><p class="term-session-note mt-2 hidden text-xs text-amber-700"></p></div>`).join('');box.dataset.termId=termId;syncTermInstallmentLimit(prefix);syncTermDateAvailability(prefix);refreshTermSessionDates(prefix);refreshAllTermSessionAvailability(prefix);};
const rebuildDbTermSessionsWithoutTimezones=window.rebuildDbTermSessions;window.rebuildDbTermSessions=function(prefix){const oldBox=termField(prefix,'SessionsContainer'),oldZones=oldBox?[...oldBox.querySelectorAll('.term-session-timezone')].map(x=>x.value):[],firstZone=oldZones[0]||termTimezones.find(x=>x.name==='Asia/Tehran')?.id||termTimezones[0]?.id||0;rebuildDbTermSessionsWithoutTimezones(prefix);const box=termField(prefix,'SessionsContainer'),count=box?.querySelectorAll('.term-session-date').length||1,repeat=termField(prefix,'RepeatType');if(count>1&&repeat?.value==='no-period')repeat.value='week';box?.querySelectorAll('.term-session-start').forEach(function(start,index){const selected=oldZones[index]||firstZone,select=document.createElement('select');select.className='term-session-timezone w-full rounded-xl border px-3 py-2.5';select.setAttribute('onchange',`termTimezoneChanged('${prefix}',${index},this)`);select.innerHTML=termTimezones.map(x=>`<option value="${x.id}" ${String(x.id)===String(selected)?'selected':''}>${x.name}</option>`).join('');start.insertAdjacentElement('beforebegin',select);});refreshTermSessionDates(prefix,true);syncTermSessionTimes(prefix);};
window.syncTermSessionTimes=async function(prefix){const box=termField(prefix,'SessionsContainer');if(!box)return;const starts=[...box.querySelectorAll('.term-session-start')],durations=[...box.querySelectorAll('.term-session-duration')],first=starts[0]?.value||'',firstDuration=durations[0]?.value||'90';starts.slice(1).forEach(x=>x.dataset.preferredStart=first);durations.slice(1).forEach(x=>x.value=firstDuration);await Promise.all(starts.slice(1).map((_,i)=>refreshTermSessionAvailability(prefix,i+1)));starts.slice(1).forEach(x=>{if([...x.options].some(o=>o.value===first))x.value=first;});};
window.syncTermFinancialFields=function(prefix){const free=Number(termField(prefix,'Cost')?.value||0)===0;const installments=termField(prefix,'InstallmentCount'),discount=termField(prefix,'Discount'),addDiscount=termField(prefix,'AddDiscount');if(installments){installments.disabled=free;if(free)installments.value='1';}if(discount){discount.disabled=free;if(free)discount.value='';}if(addDiscount)addDiscount.disabled=free;};
window.syncTermPeopleVisibility=function(prefix){const status=termField(prefix,'Status')?.value||(window.termPermissions?.isReceptionist?'pending':'open'),teachers=termField(prefix,'TeachersField'),students=termField(prefix,'StudentsField');teachers?.classList.toggle('hidden',!['open','ongoing'].includes(status));students?.classList.toggle('hidden',status!=='open');};
let termDurationTimer;window.termDurationChanged=function(prefix,index){const box=termField(prefix,'SessionsContainer');if(!box)return;if(index===0){const durations=[...box.querySelectorAll('.term-session-duration')],value=durations[0]?.value||'90';durations.slice(1).forEach(x=>x.value=value);}clearTimeout(termDurationTimer);termDurationTimer=setTimeout(()=>refreshAllTermSessionAvailability(prefix),150);};
window.syncTermInstallmentLimit=function(prefix){const count=Math.max(1,Number(termField(prefix,'SessionCount')?.value||1)),field=termField(prefix,'InstallmentCount'),max=Math.max(2,count);if(field){field.max=String(max);if(Number(field.value)>max)field.value=String(max);const hint=field.nextElementSibling;if(hint)hint.textContent='حداکثر '+max+' قسط';}};
window.syncTermDateAvailability=function(prefix){const ready=!!termField(prefix,'Course')?.value&&!!termField(prefix,'Classroom')?.value,box=termField(prefix,'SessionsContainer');if(!box)return;[...box.querySelectorAll('.term-session-date')].forEach(input=>{input.disabled=!ready;window.syncLocalizedDateInput?.(input);});};
window.refreshTermSessionAvailability=async function(prefix,index){const box=termField(prefix,'SessionsContainer'),classroom=Number(termField(prefix,'Classroom')?.value||0),branch=Number(termClassrooms.find(x=>x.id===classroom)?.branchId||0),organizationUserId=Number(termField(prefix,'Branch')?.value||0),organization=termBranches.find(x=>x.id===organizationUserId);if(!box)return;const dates=[...box.querySelectorAll('.term-session-date')],starts=[...box.querySelectorAll('.term-session-start')],durations=[...box.querySelectorAll('.term-session-duration')],zones=[...box.querySelectorAll('.term-session-timezone')],date=dates[index]?.value||'',select=starts[index],duration=durations[index],zone=zones[index];if(!select||!duration)return;const previous=select.dataset.preferredStart||select.value,requestedTimezone=Number(zone?.dataset.userChanged==='1'?zone.value:0),sourceTimezoneId=Number(zone?.dataset.sourceTimezone||zone?.dataset.previousTimezone||0);select.disabled=true;duration.disabled=true;select.innerHTML='<option value="">ابتدا تاریخ را انتخاب کنید</option>';if(!date||!branch||!classroom)return;try{const query=new URLSearchParams({branch,classroom,date,duration:Math.max(5,Number(duration.value||90)),excludeTerm:Number(box.dataset.termId||0),organizationKind:organization?.kind||'branch',organizationUserId,timezoneId:requestedTimezone,selectedStart:previous,sourceTimezoneId}),data=await termApi('/academy/admin/term-available-times?'+query),times=data.times||[],label=(organization?.kind==='academy'?'آموزشگاه':'شعبه'),desired=data.selectedStart||previous;if(zone&&data.timezoneId){zone.value=String(data.timezoneId);zone.dataset.previousTimezone=String(data.timezoneId);delete zone.dataset.sourceTimezone;}select.innerHTML=times.length?window.getTermTimeOptions(times,desired):`<option value="">${data.closed?label+' در این روز تعطیل است':'کلاس در این روز ظرفیت زمانی خالی ندارد'}</option>`;select.disabled=!times.length;duration.disabled=!times.length;if(times.length)select.value=times.includes(desired)?desired:times[0];delete select.dataset.preferredStart;if(!times.length&&dates[index]===document.activeElement){alert(data.closed?label+' در تاریخ انتخاب‌شده تعطیل است.':'تمام ساعت‌های قابل برگزاری این کلاس در تاریخ انتخاب‌شده پر است.');dates[index].value='';}}catch(e){console.error('Term availability request failed:',e);const fallback=Array.from({length:48},(_,i)=>String(Math.floor(i/2)).padStart(2,'0')+':'+(i%2?'30':'00'));select.innerHTML=window.getTermTimeOptions(fallback,previous||'10:00');select.value=fallback.includes(previous)?previous:'10:00';select.disabled=false;duration.disabled=false;select.title=e.message||'خطا در دریافت ساعت کاری';}};
window.termTimezoneChanged=function(prefix,index,select){const box=termField(prefix,'SessionsContainer'),zones=box?[...box.querySelectorAll('.term-session-timezone')]:[];select.dataset.sourceTimezone=select.dataset.previousTimezone||'';select.dataset.userChanged='1';if(index===0)zones.slice(1).forEach(zone=>{zone.dataset.sourceTimezone=zone.dataset.previousTimezone||zone.value;zone.value=select.value;zone.dataset.userChanged='1';});if(index===0)refreshAllTermSessionAvailability(prefix);else refreshTermSessionAvailability(prefix,index);};
window.refreshAllTermSessionAvailability=async function(prefix){const box=termField(prefix,'SessionsContainer');if(!box)return;await Promise.all([...box.querySelectorAll('.term-session-date')].map((_,i)=>refreshTermSessionAvailability(prefix,i)));};
function advanceTermSessionDate(value,repeat){const days={'week':7,'2-week':14,'3-week':21,'4-week':28},date=new Date(value+'T12:00:00');if(days[repeat])date.setDate(date.getDate()+days[repeat]);else if(repeat==='month')date.setMonth(date.getMonth()+1);else if(repeat==='year')date.setFullYear(date.getFullYear()+1);else date.setDate(date.getDate()+1);return date.toISOString().slice(0,10);}
function setTermSessionNote(box,index,message){const note=box?.querySelectorAll('.term-session-note')[index];if(!note)return;note.textContent=message||'';note.classList.toggle('hidden',!message);}
async function resolveAutomaticTermSessionDates(prefix){
    const repeat=termField(prefix,'RepeatType')?.value||'no-period',box=termField(prefix,'SessionsContainer');
    if(!box||repeat==='no-period')return;
    const inputs=[...box.querySelectorAll('.term-session-date')],durations=[...box.querySelectorAll('.term-session-duration')],zones=[...box.querySelectorAll('.term-session-timezone')];
    const classroom=Number(termField(prefix,'Classroom')?.value||0),branch=Number(termClassrooms.find(x=>x.id===classroom)?.branchId||0),organizationUserId=Number(termField(prefix,'Branch')?.value||0),organization=termBranches.find(x=>x.id===organizationUserId);
    if(!inputs[0]?.value||!classroom||!branch)return;
    setTermSessionNote(box,0,'');
    let previousDate=inputs[0].value;
    for(let index=1;index<inputs.length;index++){
        let candidate=advanceTermSessionDate(previousDate,repeat),rejected=[];
        for(let attempt=0;attempt<100;attempt++){
            const query=new URLSearchParams({branch,classroom,date:candidate,duration:Math.max(5,Number(durations[index]?.value||durations[0]?.value||90)),excludeTerm:Number(box.dataset.termId||0),organizationKind:organization?.kind||'branch',organizationUserId,timezoneId:Number(zones[index]?.value||0)});
            const result=await termApi('/academy/admin/term-available-times?'+query);
            if(!result.closed)break;
            rejected.push({date:candidate,reasonType:result.closureType||'organization_holiday',reason:result.closureReason||'تعطیلی آموزشگاه یا شعبه'});
            candidate=advanceTermSessionDate(candidate,repeat);
        }
        inputs[index].value=candidate;
        inputs[index].dataset.manual='0';
        inputs[index].dataset.skippedDates=JSON.stringify(rejected.map(item=>({sessionNumber:index+1,skippedDate:item.date,replacementDate:candidate,reasonType:item.reasonType,reason:item.reason})));
        window.syncLocalizedDateInput?.(inputs[index]);
        const skipped=rejected.map(item=>`${window.localizedDateValue?.(item.date)||item.date} (${item.reason})`).join('، '),replacement=window.localizedDateValue?.(candidate)||candidate;
        setTermSessionNote(box,index,rejected.length?`${skipped}؛ تاریخ جلسه به ${replacement} منتقل شد.`:'');
        previousDate=candidate;
    }
}
window.refreshTermSessionDates=async function(prefix,force=false){const repeat=termField(prefix,'RepeatType')?.value||'no-period',box=termField(prefix,'SessionsContainer');if(!box)return;const inputs=[...box.querySelectorAll('.term-session-date')],activeIndex=inputs.indexOf(document.activeElement),first=inputs[0]?.value;if(!force&&activeIndex>0){inputs[activeIndex].dataset.manual='1';return;}if(repeat==='no-period'||!first)return;await resolveAutomaticTermSessionDates(prefix);await refreshAllTermSessionAvailability(prefix);await syncTermSessionTimes(prefix);};
window.termSessionDateChanged=async function(prefix,index,input){const signature=input?.value||'';if(!signature)return refreshTermSessionAvailability(prefix,index);if(index===0){await refreshTermSessionDates(prefix,true);}else{input.dataset.manual='1';delete input.dataset.skippedDates;setTermSessionNote(termField(prefix,'SessionsContainer'),index,'');await refreshTermSessionAvailability(prefix,index);}};
window.termJalaliDateChanged=function(prefix,index){setTimeout(function(){const box=termField(prefix,'SessionsContainer'),input=box?.querySelectorAll('.term-session-date')[index];if(input)termSessionDateChanged(prefix,index,input);},0);};
let pendingTermSessionCancellation=null;
function termSessionDurationMinutes(session){const[a,b]=(session.startTime||'00:00').split(':').map(Number),[c,d]=(session.endTime||'00:00').split(':').map(Number);return Math.max(5,c*60+d-a*60-b);}
window.openTermSessionCancellation=async function(termId,sessionId){const item=allTerms.find(x=>x.id===termId),session=item?.sessions?.find(x=>x.id===sessionId);if(!item||!session)return;const active=(item.sessions||[]).filter(x=>!['canceled','rejected'].includes(x.bookingStatus)&&x.cancellationStatus!=='approved').map(x=>x.date).filter(Boolean).sort(),last=active[active.length-1]||session.date,suggested=advanceTermSessionDate(last,item.repeatType||'no-period'),duration=termSessionDurationMinutes(session);pendingTermSessionCancellation={item,session,duration};const display=window.localizedDateValue?.(suggested)||suggested;document.getElementById('modalContainer').innerHTML=`<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><div class="w-full max-w-xl rounded-3xl bg-white p-7"><div class="mb-5 flex items-center justify-between"><div><h2 class="text-xl font-bold">لغو جلسه ${session.number||''}</h2><p class="mt-1 text-sm text-gray-500">${item.name}</p></div><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="space-y-4"><div><label class="mb-2 block text-sm font-medium">دلیل لغو *</label><textarea id="termCancellationReason" rows="3" class="w-full rounded-2xl border p-3" placeholder="دلیل برگزار نشدن جلسه را بنویسید"></textarea></div><div><label class="mb-2 block text-sm font-medium">تاریخ جلسه جبرانی *</label><div class="localized-date-field"><input type="text" value="${display}" data-jdp data-jdp-target-value-input="#termMakeupDate" data-jdp-target-value-type="gregorian" onchange="termMakeupDateChanged()" class="w-full rounded-2xl border p-3"><button type="button" class="localized-date-trigger" onclick="this.previousElementSibling.focus();window.openLocalizedDatePicker?.(this.previousElementSibling)"><i class="far fa-calendar-alt"></i></button><input id="termMakeupDate" type="hidden" value="${suggested}"></div><p id="termMakeupAvailabilityMessage" class="mt-2 text-xs text-gray-500">در حال بررسی تاریخ پیشنهادی...</p></div><div class="grid grid-cols-2 gap-3"><div><label class="mb-2 block text-sm font-medium">ساعت شروع</label><select id="termMakeupStart" class="w-full rounded-2xl border p-3"><option value="">در حال دریافت...</option></select></div><div><label class="mb-2 block text-sm font-medium">مدت جلسه</label><div class="rounded-2xl border bg-gray-50 p-3">${duration} دقیقه</div></div></div><div class="flex gap-3"><button id="saveTermCancellationButton" disabled onclick="saveTermSessionCancellation()" class="flex-1 rounded-2xl bg-red-600 p-3 text-white disabled:opacity-40">ثبت لغو و جلسه جبرانی</button><button onclick="viewTerm(${termId})" class="flex-1 rounded-2xl border p-3">انصراف</button></div></div></div></div>`;await refreshTermMakeupAvailability(true);};
window.termMakeupDateChanged=function(){setTimeout(()=>refreshTermMakeupAvailability(false),0);};
window.refreshTermMakeupAvailability=async function(autoAdvance=false){const state=pendingTermSessionCancellation,dateInput=document.getElementById('termMakeupDate'),startSelect=document.getElementById('termMakeupStart'),message=document.getElementById('termMakeupAvailabilityMessage'),save=document.getElementById('saveTermCancellationButton');if(!state||!dateInput||!startSelect)return;let date=dateInput.value,attempt=0;save.disabled=true;startSelect.disabled=true;message.textContent='در حال بررسی زمان‌های قابل برگزاری...';try{const course=termCourses.find(x=>x.id===state.item.courseId),organization=termBranches.find(x=>x.id===course?.organizationUserId);let result;do{const query=new URLSearchParams({branch:state.item.branchId,classroom:state.item.classroomId,date,duration:state.duration,excludeTerm:0,organizationKind:organization?.kind||'branch',organizationUserId:course?.organizationUserId||0,timezoneId:state.session.timezoneId||0,selectedStart:state.session.startTime||''});result=await termApi('/academy/admin/term-available-times?'+query);const times=result.times||[],sameTime=times.includes(state.session.startTime);if(!autoAdvance||(!result.closed&&sameTime))break;date=advanceTermSessionDate(date,state.item.repeatType||'no-period');attempt++;}while(attempt<60);if(date!==dateInput.value){dateInput.value=date;window.syncLocalizedDateInput?.(dateInput);}const times=result?.times||[];startSelect.innerHTML=window.getTermTimeOptions(times,state.session.startTime);if(times.includes(state.session.startTime))startSelect.value=state.session.startTime;else if(times.length)startSelect.value=times[0];startSelect.disabled=!times.length;save.disabled=!times.length;message.textContent=attempt?`تاریخ پیشنهادی اولیه قابل برگزاری نبود؛ جلسه جبرانی به ${window.localizedDateValue?.(date)||date} منتقل شد.`:times.length?'تاریخ و ساعت جلسه جبرانی قابل برگزاری است.':result?.closed?'شعبه یا آموزشگاه در این تاریخ تعطیل است.':'در این تاریخ ساعت خالی وجود ندارد.';message.className='mt-2 text-xs '+(times.length?'text-emerald-600':'text-red-600');}catch(e){message.textContent=e.message;message.className='mt-2 text-xs text-red-600';}};
window.saveTermSessionCancellation=async function(){const state=pendingTermSessionCancellation,reason=document.getElementById('termCancellationReason')?.value.trim()||'',date=document.getElementById('termMakeupDate')?.value||'',start=document.getElementById('termMakeupStart')?.value||'';if(!state)return;if(!reason)return alert('دلیل لغو جلسه الزامی است.');if(!date||!start)return alert('تاریخ و ساعت معتبر جلسه جبرانی را انتخاب کنید.');const[a,b]=start.split(':').map(Number),total=a*60+b+state.duration,end=String(Math.floor(total/60)%24).padStart(2,'0')+':'+String(total%60).padStart(2,'0');try{await termApi(`/academy/admin/terms/${state.item.id}/sessions/${state.session.id}/cancel`,{reason,makeupDate:date,startTime:start,endTime:end});const termId=state.item.id;pendingTermSessionCancellation=null;await loadTerms();viewTerm(termId);alert(window.termPermissions?.isReceptionist?'درخواست لغو برای تأیید مدیر ثبت شد.':'جلسه لغو و جلسه جبرانی ثبت شد.');}catch(e){alert(e.message);}};
window.decideTermSessionCancellation=async function(termId,sessionId,approve){const action=approve?'تأیید':'رد';if(!confirm(`${action} درخواست لغو این جلسه؟`))return;try{await termApi(`/academy/admin/terms/${termId}/sessions/${sessionId}/cancel/${approve?'approve':'reject'}`,{});await loadTerms();viewTerm(termId);}catch(e){alert(e.message);}};
window.restoreTermSession=async function(termId,sessionId){if(!await AppDialog.confirm('جلسه بازگردانی و همه جلسات جبرانی وابسته به آن حذف شوند؟'))return;try{const result=await termApi(`/academy/admin/terms/${termId}/sessions/${sessionId}/restore`,{});await loadTerms();await window.refreshDashboard?.();viewTerm(termId);AppDialog.alert(`جلسه بازگردانی شد و ${Number(result?.removedMakeupSessions||0).toLocaleString('fa-IR')} جلسه جبرانی حذف شد.`);}catch(e){AppDialog.alert(e.message);}};
window.openAddTermDiscountModal=function(prefix){const host=document.getElementById('modalContainer'),termHtml=host.innerHTML;window.pendingTermModal={html:termHtml,prefix};host.innerHTML=`<div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4"><div class="w-full max-w-lg rounded-3xl bg-white p-7"><h2 class="mb-5 text-xl font-bold">افزودن تخفیف جدید</h2><div class="space-y-4"><input id="newTermDiscountTitle" placeholder="عنوان تخفیف" class="w-full rounded-2xl border p-4"><select id="newTermDiscountType" class="w-full rounded-2xl border p-4"><option value="percentage">درصدی</option><option value="fixed">مبلغ ثابت ویژه</option></select><input id="newTermDiscountValue" type="number" min="0" placeholder="مقدار تخفیف" class="w-full rounded-2xl border p-4"><div class="flex gap-3"><button onclick="saveNewTermDiscount()" class="flex-1 rounded-2xl bg-indigo-600 p-3 text-white">ذخیره</button><button onclick="restoreTermModal()" class="flex-1 rounded-2xl border p-3">انصراف</button></div></div></div></div>`;};
window.restoreTermModal=function(){if(window.pendingTermModal)document.getElementById('modalContainer').innerHTML=window.pendingTermModal.html;};window.saveNewTermDiscount=async function(){const data={title:document.getElementById('newTermDiscountTitle').value.trim(),type:document.getElementById('newTermDiscountType').value,value:Number(document.getElementById('newTermDiscountValue').value||0)};try{const row=await termApi('/academy/admin/term-discounts',data),state=window.pendingTermModal;termDiscounts.push(row);document.getElementById('modalContainer').innerHTML=state.html;const select=termField(state.prefix,'Discount');if(select){select.insertAdjacentHTML('beforeend',`<option value="${row.id}">${row.name}</option>`);select.value=String(row.id);}window.pendingTermModal=null;}catch(e){alert(e.message)}};

// ==================== CRUD ====================
window.openAddTermModal = async function () {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }
    document.getElementById('modalContainer').innerHTML = window.getTermAddModalHTML ? window.getTermAddModalHTML() : '';
    syncTermFinancialFields('');syncTermPeopleVisibility('');refreshTermDependencies('');
};

async function validateTermScheduleBeforeSave(data,excludeTerm){const maxInstallments=Math.max(2,data.sessions.length),organization=termBranches.find(x=>x.id===data.organizationUserId),label=organization?.kind==='academy'?'آموزشگاه':'شعبه';if(data.installmentCount>maxInstallments)throw new Error(`تعداد اقساط نمی‌تواند بیشتر از ${maxInstallments} باشد.`);for(let i=0;i<data.sessions.length;i++){const session=data.sessions[i],query=new URLSearchParams({branch:data.branchId,classroom:data.classroomId,date:session.date,duration:session.durationMinutes,excludeTerm:excludeTerm||0,organizationKind:organization?.kind||'branch',organizationUserId:data.organizationUserId,timezoneId:session.timezoneId||0}),result=await termApi('/academy/admin/term-available-times?'+query);if(result.closed)throw new Error(`${label} در تاریخ جلسه ${i+1} تعطیل است.`);if(result.full)throw new Error(`کلاس انتخاب‌شده در تاریخ جلسه ${i+1} ظرفیت زمانی خالی ندارد.`);if(!(result.times||[]).includes(session.startTime))throw new Error(`ساعت شروع جلسه ${i+1} خارج از ساعات مجاز یا دارای تداخل است.`);}return true;}

window.saveTerm = async function () {
    const data = readTermForm('');
    if (!validateTermData(data)) return;
    try{await validateTermScheduleBeforeSave(data,0);await termApi('/academy/admin/terms',data);await loadTerms();closeModal();alert('✅ ترم با موفقیت اضافه شد');}catch(e){alert(e.message)}
};

window.viewTerm = async function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermDetailsModalHTML ? window.getTermDetailsModalHTML(item) : '';
};

window.editTerm = async function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermEditModalHTML ? window.getTermEditModalHTML(item) : '';
    document.querySelectorAll('#editTermSessionsContainer .term-session-timezone').forEach((zone,index)=>{if(item.sessions?.[index]?.timezoneId)zone.dataset.userChanged='1';});
    syncTermFinancialFields('editTerm');syncTermPeopleVisibility('editTerm');syncTermDateAvailability('editTerm');
    await refreshAllTermSessionAvailability('editTerm');
};

window.saveEditedTerm = async function (id) {
    const data = readTermForm('editTerm');
    if (!validateTermData(data)) return;
    const index = allTerms.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    try{await validateTermScheduleBeforeSave(data,id);await termApi('/academy/admin/terms/'+id+'/update',data);await loadTerms();editingTermRowId=null;closeModal();alert('✅ تغییرات ذخیره شد');}catch(e){alert(e.message)}
};

window.toggleTermInlineEdit = async function (id) {
    attendanceTermRowId = null;
    editingTermRowId = editingTermRowId === id ? null : id;
    renderTermsTable(filteredTerms);
    if (editingTermRowId){const prefix='inlineTerm'+id,item=allTerms.find(x=>x.id===id);document.querySelectorAll(`#${prefix}SessionsContainer .term-session-timezone`).forEach((zone,index)=>{if(item?.sessions?.[index]?.timezoneId)zone.dataset.userChanged='1';});syncTermFinancialFields(prefix);syncTermPeopleVisibility(prefix);syncTermDateAvailability(prefix);await refreshAllTermSessionAvailability(prefix);}
};
window.cycleTermStatus=async function(id){closeTermInlineEdit();try{await termApi('/academy/admin/terms/'+id+'/status',{});await loadTerms();}catch(e){alert(e.message)}};

window.saveInlineTerm = async function (id) {
    const data = readTermForm('inlineTerm' + id);
    if (!validateTermData(data)) return;
    const index = allTerms.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    try{await validateTermScheduleBeforeSave(data,id);await termApi('/academy/admin/terms/'+id+'/update',data);editingTermRowId=null;await loadTerms();alert('✅ تغییرات با موفقیت ذخیره شد');}catch(e){alert(e.message)}
};

window.deleteTerm = async function (id) {
    if (!(await AppDialog.confirmDelete(allTerms, id, 'ترم'))) return;
    await termApi('/academy/admin/terms/'+id+'/delete',{});allTerms = allTerms.filter(function (t) { return t.id !== id; });
    if (editingTermRowId === id) editingTermRowId = null;
    if (attendanceTermRowId === id) attendanceTermRowId = null;
    renderTermFilters();
    filterTerms();
};

// ==================== attendance ====================
window.toggleTermInlineAttendance = async function (id) {
    editingTermRowId = null;
    attendanceTermRowId = attendanceTermRowId === id ? null : id;
    renderTermsTable(filteredTerms);
};

window.openTermAttendanceModal = async function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermAttendanceModalHTML ? window.getTermAttendanceModalHTML(item) : '';
};

window.saveTermAttendance = async function (id, isInline) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;

    const root = isInline
        ? document.getElementById('termAttendancePanel-' + id)
        : document.getElementById('modalContainer');
    if (!root) return;

    const attendance = {};
    (item.sessions || []).forEach(function (_, si) {
        attendance[String(si)] = { teachers: {}, students: {} };
    });

    root.querySelectorAll('.att-teacher').forEach(function (cb) {
        const si = cb.getAttribute('data-session');
        const tid = cb.getAttribute('data-id');
        if (!attendance[si]) attendance[si] = { teachers: {}, students: {} };
        attendance[si].teachers[tid] = cb.checked;
    });
    root.querySelectorAll('.att-student').forEach(function (cb) {
        const si = cb.getAttribute('data-session');
        const sid = cb.getAttribute('data-id');
        if (!attendance[si]) attendance[si] = { teachers: {}, students: {} };
        attendance[si].students[sid] = cb.checked;
    });

    item.attendance = attendance;
    if (isInline) {
        attendanceTermRowId = null;
        renderTermsTable(filteredTerms);
    } else {
        closeModal();
    }
    alert('✅ حضور و غیاب ذخیره شد');
};

// ==================== excel / pdf ====================
window.exportTermsToExcel = async function () {
    const data = filteredTerms.length ? filteredTerms : allTerms;
    let csv = '\uFEFF';
    csv += 'ردیف,نام ترم,شعبه,دوره مرتبط,تاریخ شروع,تاریخ پایان,وضعیت,هزینه,واحد پول\n';
    data.forEach(function (item, index) {
        csv += (index + 1) + ',"' + item.name + '","' + item.branchName + '","' + (item.course || '') + '",' +
            (item.start || '') + ',' + (item.end || '') + ',"' + item.status + '",' + (item.cost || 0) + ',"' + (item.currency || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'ترم‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportTermsToPDF = async function () {
    openTermsPDFOptionsModal();
};

window.openTermsPDFOptionsModal = async function () {
    document.getElementById('modalContainer').innerHTML = window.getTermPDFModalHTML
        ? window.getTermPDFModalHTML(termPdfColumns) : '';
};

window.generateTermsPDF = async function () {
    if (!window.html2canvas) {
        alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
        return;
    }

    const title = document.getElementById('termPdfTitle') && document.getElementById('termPdfTitle').value || 'گزارش ترم‌های آموزشگاه';
    const subtitle = document.getElementById('termPdfSubtitle') && document.getElementById('termPdfSubtitle').value || 'لیست ترم‌ها، دوره‌ها و وضعیت برگزاری';
    const footer = document.getElementById('termPdfFooter') && document.getElementById('termPdfFooter').value || '';
    const format = document.getElementById('termPdfFormat') && document.getElementById('termPdfFormat').value || 'a4';
    const orientation = document.getElementById('termPdfOrientation') && document.getElementById('termPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('termPdfIncludeDate') && document.getElementById('termPdfIncludeDate').checked;
    const headerColor = document.getElementById('termPdfHeaderColor') && document.getElementById('termPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('termPdfEvenRowColor') && document.getElementById('termPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('termPdfOddRowColor') && document.getElementById('termPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = termPdfColumns.filter(function (col) {
        return document.getElementById('termPdfCol-' + col.field) && document.getElementById('termPdfCol-' + col.field).checked;
    });
    const date = new Date().toLocaleDateString('fa-IR');
    const data = (filteredTerms.length ? filteredTerms : allTerms).map(function (item) {
        return Object.assign({}, item, { cost: item.cost != null ? Number(item.cost).toLocaleString('fa-IR') + ' ' + (item.currency || '') : '—' });
    });

    if (!selectedColumns.length) {
        alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
        return;
    }

    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];

    for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
        const pageRows = data.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);
        const pageWrapper = document.createElement('div');
        pageWrapper.style.direction = 'rtl';
        pageWrapper.style.position = 'fixed';
        pageWrapper.style.top = '-9999px';
        pageWrapper.style.left = '-9999px';
        pageWrapper.style.width = orientation === 'portrait' ? '900px' : '1400px';
        pageWrapper.style.padding = pageIndex === 0 ? '20px 30px 30px' : '30px';
        pageWrapper.style.backgroundColor = '#ffffff';
        pageWrapper.style.fontFamily = 'Vazirmatn, Tahoma, sans-serif';
        pageWrapper.innerHTML = window.getTermPDFPageHTML
            ? window.getTermPDFPageHTML(pageIndex + 1, pageRows, pageIndex === 0, {
                title: title, subtitle: subtitle, footer: footer, includeDate: includeDate, date: date,
                headerColor: headerColor, evenRowColor: evenRowColor, oddRowColor: oddRowColor,
                selectedColumns: selectedColumns, rowsPerPage: rowsPerPage, totalPages: totalPages
            }) : '';
        document.body.appendChild(pageWrapper);
        const canvas = await html2canvas(pageWrapper, {
            scale: 2, useCORS: true, backgroundColor: '#ffffff', scrollY: -window.scrollY
        });
        canvasPages.push(canvas);
        pageWrapper.remove();
    }

    const doc = new window.jspdf.jsPDF({ orientation: orientation, unit: 'pt', format: format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20;
    const imgWidth = pageWidth - margin * 2;

    canvasPages.forEach(function (canvas, index) {
        if (index > 0) doc.addPage();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
    });

    doc.save('ترم‌ها_' + date + '.pdf');
    closeModal();
};

// ==================== Init ====================
const saveTermSessionCancellationBase=window.saveTermSessionCancellation;
window.saveTermSessionCancellation=async function(){await saveTermSessionCancellationBase();await window.refreshDashboard?.();};
window.addEventListener('sornaz:data-changed',async function(event){if(event.detail?.resource!=='courses')return;try{const data=await termApi('/academy/admin/courses');window.termCourses=(data.courses||[]).map(x=>({...x,lessonId:x.lesson_id}));document.querySelectorAll('select[id$="Course"]').forEach(function(select){const prefix=select.id==='termCourse'?'':select.id.slice(0,-6),organizationUserId=Number(termField(prefix,'Branch')?.value||0),selected=select.value;select.innerHTML='<option value="">انتخاب دوره</option>'+termCourses.filter(x=>x.organizationUserId===organizationUserId).map(x=>`<option value="${x.id}" ${String(x.id)===selected?'selected':''}>${x.name}</option>`).join('');});}catch(error){console.error(error);}});
(function initTerms() {
    setTimeout(function () {
        if (document.getElementById('termsTable')) {
            loadTerms().catch(function(e){console.error(e);alert(e.message)});
            pollTermsRealtime();setInterval(pollTermsRealtime,2000);document.addEventListener('visibilitychange',function(){if(!document.hidden)pollTermsRealtime();});
        }
    }, 200);
})();
