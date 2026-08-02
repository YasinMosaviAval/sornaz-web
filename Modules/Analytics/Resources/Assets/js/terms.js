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

window.updateTermCourseCapacityHint = function (prefix) {
    const courseField = document.getElementById(prefix ? (prefix + 'Course') : 'termCourse');
    const hint = document.getElementById(prefix ? (prefix + 'CourseCapacityHint') : 'termCourseCapacityHint');
    if (!hint) return;
    const courseId = courseField ? courseField.value : '';
    hint.textContent = `ظرفیت هنرجویان این دوره ${window.getTermCourseCapacity(courseId)} نفر است`;
};

window.syncTermInstallments = function (prefix) {
    const costField = document.getElementById(prefix ? (prefix + 'Cost') : 'termCost');
    const container = document.getElementById(prefix ? (prefix + 'InstallmentsContainer') : 'termInstallmentsContainer');
    if (!costField || !container) return;
    const cost = Number(costField.value || 0);
    const items = container.querySelectorAll('.term-installment-item');
    if (!items.length) return;
    const firstInput = items[0].querySelector('.term-installment-amount');
    if (firstInput && !firstInput.value) firstInput.value = cost;
};

function validateTermData(data) {
    if (!data.name) {
        alert('نام ترم الزامی است');
        return false;
    }
    if (!data.teachers || data.teachers.length < 1) {
        alert('حداقل یک استاد باید انتخاب شود');
        return false;
    }
    if (!data.students || data.students.length < 1) {
        alert('حداقل یک هنرجو باید انتخاب شود');
        return false;
    }
    const courseCapacity = window.getTermCourseCapacity(data.courseId);
    if (data.students.length > courseCapacity) {
        alert('تعداد هنرجویان برای این دوره بیش از ظرفیت مجاز است');
        return false;
    }
    const installments = (data.installments || []).filter(Boolean);
    if (!installments.length) {
        alert('حداقل یک قسط باید ثبت شود');
        return false;
    }
    const totalCost = Number(data.cost || 0);
    const installmentSum = installments.reduce(function (sum, item) {
        return sum + Number(item.amount || 0);
    }, 0);
    if (installmentSum !== totalCost) {
        alert('جمع اقساط باید برابر مبلغ کل هزینه ترم باشد');
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
let termSortField = '';
let termSortDirection = 'asc';

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

window.updateTermSortIcons = function () {
    ['name', 'branchName', 'course', 'start', 'end', 'status'].forEach(function (field) {
        const icon = document.getElementById('termSortIcon-' + field);
        if (!icon) return;
        icon.textContent = termSortField === field
            ? (termSortDirection === 'asc' ? '↑' : '↓')
            : '↕';
    });
};

window.sortTermsBy = function (field) {
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
window.renderTermsBranchTabs = function () {
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

window.filterTermsByBranch = function (branchId) {
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

window.renderTermCourseFilter = function () {
    const select = document.getElementById('filterTermCourse');
    if (!select) return;
    const names = new Set(allTerms.map(function (t) { return t.course; }).filter(Boolean));
    const current = select.value;
    select.innerHTML = '<option value="">همه دوره‌ها</option>' +
        Array.from(names).sort().map(function (n) {
            return '<option value="' + n + '"' + (n === current ? ' selected' : '') + '>' + n + '</option>';
        }).join('');
};

window.filterTerms = function () {
    const search = (document.getElementById('termSearch') && document.getElementById('termSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterTermStatus') && document.getElementById('filterTermStatus').value || '';
    const course = document.getElementById('filterTermCourse') && document.getElementById('filterTermCourse').value || '';

    filteredTerms = allTerms.filter(function (item) {
        const matchBranch = currentTermBranch === 'all' || item.branchId == currentTermBranch;
        const matchSearch = !search || (item.name || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchCourse = !course || item.course === course;
        return matchBranch && matchSearch && matchStatus && matchCourse;
    });

    termsCurrentPage = 1;
    sortTermItems();
    renderTermsTable(filteredTerms);
};

// ==================== table ====================
window.renderTermsTable = function (list) {
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
                'در حال برگزاری': 'bg-green-100 text-green-700',
                'پایان‌یافته': 'bg-gray-100 text-gray-600',
                'در انتظار': 'bg-yellow-100 text-yellow-700',
                'تعلیق‌شده': 'bg-red-100 text-red-700'
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

window.changeTermsPage = function (page) {
    const totalPages = Math.ceil(filteredTerms.length / termsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    termsCurrentPage = page;
    renderTermsTable(filteredTerms);
};

// ==================== prompts for new types ====================
function promptAddNamed(list, label, selectIds, inlineSuffix) {
    const name = (prompt('نام ' + label + ' جدید را وارد کنید:') || '').trim();
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

window.promptAddTermCourse = function () {
    const name = (prompt('نام دوره جدید را وارد کنید:') || '').trim();
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

window.promptAddTermCurrency = function () {
    const name = (prompt('نام واحد پول جدید را وارد کنید:') || '').trim();
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

window.promptAddTermDiscount = function () {
    const name = (prompt('عنوان تخفیف جدید را وارد کنید:') || '').trim();
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

window.promptAddTermClassroom = function () {
    const name = (prompt('نام کلاس جدید را وارد کنید:') || '').trim();
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
window.addTermTeacherField = function (containerId) {
    const el = document.getElementById(containerId);
    if (el && window.getTermTeacherFieldHTML) el.insertAdjacentHTML('beforeend', window.getTermTeacherFieldHTML({}));
};
window.addTermStudentField = function (containerId) {
    const el = document.getElementById(containerId);
    if (el && window.getTermStudentFieldHTML) el.insertAdjacentHTML('beforeend', window.getTermStudentFieldHTML({}));
};
window.addTermInstallmentField = function (containerId) {
    const el = document.getElementById(containerId);
    if (el && window.getTermInstallmentFieldHTML) el.insertAdjacentHTML('beforeend', window.getTermInstallmentFieldHTML({}));
};

window.rebuildTermSessions = function (prefix) {
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
    const status = field('Status') && field('Status').value || 'در حال برگزاری';
    const summary = field('Summary') && field('Summary').value.trim() || '';
    const description = field('Description') && field('Description').value.trim() || '';

    const tContainer = prefix ? (prefix + 'TeachersContainer') : 'termTeachersContainer';
    const sContainer = prefix ? (prefix + 'StudentsContainer') : 'termStudentsContainer';
    const iContainer = prefix ? (prefix + 'InstallmentsContainer') : 'termInstallmentsContainer';
    const sessContainer = prefix ? (prefix + 'SessionsContainer') : 'termSessionsContainer';

    const teachers = readCollection(tContainer, '.term-teacher-item', function (div) {
        const sel = div.querySelector('.term-teacher-select');
        if (!sel || !sel.value) return null;
        return { id: sel.value, name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value };
    });
    const students = readCollection(sContainer, '.term-student-item', function (div) {
        const sel = div.querySelector('.term-student-select');
        if (!sel || !sel.value) return null;
        return { id: sel.value, name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value };
    });
    const installments = readCollection(iContainer, '.term-installment-item', function (div) {
        const amount = parseFloat(div.querySelector('.term-installment-amount') && div.querySelector('.term-installment-amount').value || 0);
        return amount ? { amount: amount } : null;
    });
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

// ==================== CRUD ====================
window.openAddTermModal = function () {
    if (!document.getElementById('modalContainer')) {
        alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
        return;
    }
    document.getElementById('modalContainer').innerHTML = window.getTermAddModalHTML ? window.getTermAddModalHTML() : '';
};

window.saveTerm = function () {
    const data = readTermForm('');
    if (!validateTermData(data)) return;
    allTerms.unshift(Object.assign({ id: Date.now(), attendance: {} }, data));
    renderTermCourseFilter();
    filterTerms();
    closeModal();
    alert('✅ ترم با موفقیت اضافه شد');
};

window.viewTerm = function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermDetailsModalHTML ? window.getTermDetailsModalHTML(item) : '';
};

window.editTerm = function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermEditModalHTML ? window.getTermEditModalHTML(item) : '';
};

window.saveEditedTerm = function (id) {
    const data = readTermForm('editTerm');
    if (!validateTermData(data)) return;
    const index = allTerms.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allTerms[index] = Object.assign({}, allTerms[index], data);
    editingTermRowId = null;
    renderTermCourseFilter();
    filterTerms();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleTermInlineEdit = function (id) {
    attendanceTermRowId = null;
    editingTermRowId = editingTermRowId === id ? null : id;
    renderTermsTable(filteredTerms);
};

window.saveInlineTerm = function (id) {
    const data = readTermForm('inlineTerm' + id);
    if (!validateTermData(data)) return;
    const index = allTerms.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allTerms[index] = Object.assign({}, allTerms[index], data);
    editingTermRowId = null;
    renderTermCourseFilter();
    filterTerms();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

window.deleteTerm = function (id) {
    if (!confirm('آیا از حذف این ترم مطمئن هستید؟')) return;
    allTerms = allTerms.filter(function (t) { return t.id !== id; });
    if (editingTermRowId === id) editingTermRowId = null;
    if (attendanceTermRowId === id) attendanceTermRowId = null;
    renderTermCourseFilter();
    filterTerms();
};

// ==================== attendance ====================
window.toggleTermInlineAttendance = function (id) {
    editingTermRowId = null;
    attendanceTermRowId = attendanceTermRowId === id ? null : id;
    renderTermsTable(filteredTerms);
};

window.openTermAttendanceModal = function (id) {
    const item = allTerms.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getTermAttendanceModalHTML ? window.getTermAttendanceModalHTML(item) : '';
};

window.saveTermAttendance = function (id, isInline) {
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
window.exportTermsToExcel = function () {
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

window.exportTermsToPDF = function () {
    openTermsPDFOptionsModal();
};

window.openTermsPDFOptionsModal = function () {
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
(function initTerms() {
    setTimeout(function () {
        if (document.getElementById('termsTable')) {
            renderTermsBranchTabs();
            renderTermCourseFilter();
            filterTerms();
        }
    }, 200);
})();
