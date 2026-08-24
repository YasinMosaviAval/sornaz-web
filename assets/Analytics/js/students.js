(function () {
'use strict';
// ==================== مدیریت هنرجویان ====================

const studentFirstNames = ['سارا', 'امیر', 'زهرا', 'علی', 'نگار', 'پارسا', 'مهسا', 'رضا', 'نیلوفر', 'محمد', 'فاطمه', 'حسین', 'مریم', 'آرین', 'هستی', 'کیان', 'یاسمن', 'آرش', 'ستایش', 'دانیال'];
const studentLastNames = ['احمدی', 'حسینی', 'کریمی', 'محمدی', 'رضایی', 'نوری', 'موسوی', 'جعفری', 'کاظمی', 'حیدری', 'صادقی', 'اکبری', 'میرزایی', 'نظری', 'رحیمی', 'باقری', 'شریفی', 'طاهری', 'قاسمی', 'ابراهیمی'];
const studentInstruments = ['پیانو', 'گیتار', 'ویولن', 'آواز', 'درام', 'سنتور', 'کمانچه'];
const studentLevels = ['مبتدی', 'متوسط', 'پیشرفته'];
const studentTeachers = ['موسوی', 'رضایی', 'بهرامی', 'کاظمی', 'نوری'];
const studentFinancials = ['تسویه', 'بدهکار'];
const studentBranchesFallback = [
    { id: 1, name: 'شعبه مرکزی' },
    { id: 2, name: 'شعبه ونک' },
    { id: 3, name: 'شعبه سعادت‌آباد' },
    { id: 4, name: 'شعبه کرج' }
];

window.studentInstrumentsList = studentInstruments;
window.studentLevelsList = studentLevels;
window.studentTeachersList = studentTeachers;
window.studentFinancialsList = studentFinancials;

window.getStudentBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) {
        return allBranches.map(function (b) { return { id: b.id, name: b.name }; });
    }
    return studentBranchesFallback.slice();
};

function todayISO() {
    return new Date().toISOString().split('T')[0];
}

function calcAge(birthDate) {
    if (!birthDate) return null;
    const d = new Date(birthDate);
    if (isNaN(d.getTime())) return null;
    const now = new Date();
    let age = now.getFullYear() - d.getFullYear();
    const m = now.getMonth() - d.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < d.getDate())) age--;
    return age;
}

window.isStudentUnder18 = async function (birthDate) {
    const age = calcAge(birthDate);
    return age !== null && age < 18;
};

window.toggleStudentParentFields = async function (birthInputId, wrapId) {
    const input = document.getElementById(birthInputId);
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;
    const show = input && window.isStudentUnder18(input.value);
    wrap.classList.toggle('hidden', !show);
};

function randomNationalId() {
    let s = '';
    for (let i = 0; i < 10; i++) s += Math.floor(Math.random() * 10);
    return s;
}

function randomBirthDate(minAge, maxAge) {
    const now = new Date();
    const age = minAge + Math.floor(Math.random() * (maxAge - minAge + 1));
    const year = now.getFullYear() - age;
    const month = 1 + Math.floor(Math.random() * 12);
    const day = 1 + Math.floor(Math.random() * 28);
    return year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
}

let allStudents = [];
/* Legacy random fixtures removed: students are hydrated from academy-data-loaded. */
(function buildSample() { return;
    const branches = window.getStudentBranches();
    for (let i = 1; i <= 250; i++) {
        const first = studentFirstNames[Math.floor(Math.random() * studentFirstNames.length)];
        const last = studentLastNames[Math.floor(Math.random() * studentLastNames.length)];
        const under18 = Math.random() < 0.35;
        const birthDate = under18 ? randomBirthDate(8, 17) : randomBirthDate(18, 45);
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const remaining = Math.floor(Math.random() * 12);
        const item = {
            id: i,
            name: first + ' ' + last,
            nationalId: randomNationalId(),
            fatherName: studentFirstNames[Math.floor(Math.random() * studentFirstNames.length)],
            birthDate: birthDate,
            phone: '۰۹۱' + Math.floor(10000000 + Math.random() * 89999999),
            address: 'تهران، خیابان نمونه، پلاک ' + (10 + Math.floor(Math.random() * 200)),
            registrationDate: randomBirthDate(0, 2),
            instrument: studentInstruments[Math.floor(Math.random() * studentInstruments.length)],
            level: studentLevels[Math.floor(Math.random() * studentLevels.length)],
            teacher: studentTeachers[Math.floor(Math.random() * studentTeachers.length)],
            remaining: remaining,
            financial: studentFinancials[Math.floor(Math.random() * studentFinancials.length)],
            attendance: (70 + Math.floor(Math.random() * 30)) + '٪',
            branchId: branch.id,
            branch: branch.name,
            parentName: '',
            parentNationalId: '',
            parentFatherName: '',
            parentBirthDate: '',
            parentPhone: ''
        };
        if (under18) {
            item.parentName = studentFirstNames[Math.floor(Math.random() * studentFirstNames.length)] + ' ' + studentLastNames[Math.floor(Math.random() * studentLastNames.length)];
            item.parentNationalId = randomNationalId();
            item.parentFatherName = studentFirstNames[Math.floor(Math.random() * studentFirstNames.length)];
            item.parentBirthDate = randomBirthDate(30, 55);
            item.parentPhone = '۰۹۱' + Math.floor(10000000 + Math.random() * 89999999);
        }
        // registration date closer to today for realism
        const reg = new Date();
        reg.setDate(reg.getDate() - Math.floor(Math.random() * 400));
        item.registrationDate = reg.toISOString().split('T')[0];
        allStudents.push(item);
    }
})();
window.addEventListener('academy-data-loaded',function(event){allStudents=(event.detail.members||[]).filter(function(item){return item.type==='student';});filteredStudents=allStudents.slice();if(document.getElementById('studentsTable')){window.renderStudentBranchTabs();window.filterStudents();}});

let currentStudentBranch = 'all';
let studentsCurrentPage = 1;
const studentsPerPage = 10;
let filteredStudents = allStudents.slice();
let editingStudentRowId = null;
let stuSortField = '';
let stuSortDirection = 'asc';

const studentPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام هنرجو' },
    { field: 'nationalId', label: 'کد ملی' },
    { field: 'instrument', label: 'ساز' },
    { field: 'level', label: 'سطح' },
    { field: 'teacher', label: 'استاد' },
    { field: 'branch', label: 'شعبه' },
    { field: 'remaining', label: 'جلسات باقی‌مانده' },
    { field: 'financial', label: 'وضعیت مالی' },
    { field: 'attendance', label: 'حضور' },
    { field: 'registrationDate', label: 'تاریخ ثبت‌نام' }
];

function sortStudentItems() {
    if (!stuSortField) return;
    filteredStudents.sort(function (a, b) {
        let av = a[stuSortField], bv = b[stuSortField];
        if (stuSortField === 'remaining') {
            av = Number(av); bv = Number(bv);
        } else if (stuSortField === 'attendance') {
            av = parseInt(String(av).replace(/[^\d]/g, ''), 10) || 0;
            bv = parseInt(String(bv).replace(/[^\d]/g, ''), 10) || 0;
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return stuSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return stuSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateStudentSortIcons = async function () {
    ['name', 'instrument', 'level', 'teacher', 'branch', 'remaining', 'financial', 'attendance'].forEach(function (f) {
        const icon = document.getElementById('stuSortIcon-' + f);
        if (!icon) return;
        icon.textContent = stuSortField === f ? (stuSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortStudentsBy = async function (field) {
    if (stuSortField === field) stuSortDirection = stuSortDirection === 'asc' ? 'desc' : 'asc';
    else { stuSortField = field; stuSortDirection = 'asc'; }
    sortStudentItems();
    window.renderStudentsTable(filteredStudents);
    window.updateStudentSortIcons();
};

window.renderStudentBranchTabs = async function () {
    const container = document.getElementById('studentBranchTabs');
    if (!container) return;
    container.querySelectorAll('.student-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getStudentBranches().forEach(function (b) {
        const active = String(currentStudentBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'student-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition-colors ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterStudentsByBranch(b.id); };
        container.appendChild(btn);
    });
    // sync all tab
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentStudentBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterStudentsByBranch = async function (branchId) {
    currentStudentBranch = branchId;
    document.querySelectorAll('.student-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) {
            tab.classList.add('border', 'border-gray-200');
            tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        } else {
            tab.classList.remove('border-gray-200');
        }
    });
    window.filterStudents();
};

window.filterStudents = async function () {
    const search = (document.getElementById('studentSearch') && document.getElementById('studentSearch').value || '').trim().toLowerCase();
    const ageGroup = document.getElementById('filterStudentAgeGroup') && document.getElementById('filterStudentAgeGroup').value || '';
    const level = document.getElementById('filterStudentLevel') && document.getElementById('filterStudentLevel').value || '';
    const instrument = document.getElementById('filterStudentInstrument') && document.getElementById('filterStudentInstrument').value || '';
    const financial = document.getElementById('filterStudentFinancial') && document.getElementById('filterStudentFinancial').value || '';
    const remaining = document.getElementById('filterStudentRemaining') && document.getElementById('filterStudentRemaining').value || '';

    filteredStudents = allStudents.filter(function (stu) {
        const matchBranch = window.matchesOrganizationFilter(stu,currentStudentBranch);
        const matchSearch = !search ||
            (stu.name || '').toLowerCase().includes(search) ||
            (stu.teacher || '').toLowerCase().includes(search) ||
            (stu.phone && String(stu.phone).includes(search));
        const age = calcAge(stu.birthDate);
        let matchAge = true;
        if (ageGroup === 'child') matchAge = age !== null && age < 18;
        if (ageGroup === 'adult') matchAge = age !== null && age >= 18;
        const matchLevel = !level || stu.level === level;
        const matchInstrument = !instrument || stu.instrument === instrument;
        const matchFinancial = !financial || stu.financial === financial;
        let matchRemaining = true;
        const r = Number(stu.remaining) || 0;
        if (remaining === '0') matchRemaining = r === 0;
        else if (remaining === '1-2') matchRemaining = r >= 1 && r <= 2;
        else if (remaining === '3-5') matchRemaining = r >= 3 && r <= 5;
        else if (remaining === '6+') matchRemaining = r >= 6;
        return matchBranch && matchSearch && matchAge && matchLevel && matchInstrument && matchFinancial && matchRemaining;
    });

    studentsCurrentPage = 1;
    sortStudentItems();
    window.renderStudentsTable(filteredStudents);
};

window.renderStudentsTable = async function (list) {
    list = list || filteredStudents;
    const tbody = document.querySelector('#studentsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / studentsPerPage) || 1;
    if (studentsCurrentPage > totalPages) studentsCurrentPage = totalPages;

    const start = (studentsCurrentPage - 1) * studentsPerPage;
    const end = start + studentsPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getStudentEmptyRowHTML ? window.getStudentEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getStudentRowHTML ? window.getStudentRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingStudentRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 student-inline-expand';
                expand.innerHTML = window.getStudentInlineExpandRowHTML ? window.getStudentInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateStudentsPagination(list.length, start, end, totalPages);
    window.updateStudentSortIcons();
};

function updateStudentsPagination(total, start, end, totalPages) {
    const info = document.getElementById('studentsPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' هنرجو';
    }
    const pagination = document.getElementById('studentsPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeStudentsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (studentsCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeStudentsPage(' + (studentsCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (studentsCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, studentsCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeStudentsPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === studentsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeStudentsPage(' + (studentsCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (studentsCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeStudentsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (studentsCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeStudentsPage = async function (page) {
    const totalPages = Math.ceil(filteredStudents.length / studentsPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    studentsCurrentPage = page;
    window.renderStudentsTable(filteredStudents);
};

function readStudentForm(prefix) {
    const g = function (id) { return document.getElementById(prefix + id); };
    const branchId = parseInt(g('Branch') && g('Branch').value, 10);
    const branchObj = window.getStudentBranches().find(function (b) { return b.id === branchId; });
    const birthDate = g('BirthDate') && g('BirthDate').value || '';
    const under18 = window.isStudentUnder18(birthDate);
    const data = {
        name: (g('Name') && g('Name').value || '').trim(),
        nationalId: (g('NationalId') && g('NationalId').value || '').trim(),
        fatherName: (g('FatherName') && g('FatherName').value || '').trim(),
        birthDate: birthDate,
        phone: (g('Phone') && g('Phone').value || '').trim(),
        address: (g('Address') && g('Address').value || '').trim(),
        registrationDate: (g('RegistrationDate') && g('RegistrationDate').value) || todayISO(),
        instrument: g('Instrument') && g('Instrument').value || '',
        level: g('Level') && g('Level').value || '',
        teacher: g('Teacher') && g('Teacher').value || '',
        remaining: parseInt(g('Remaining') && g('Remaining').value, 10),
        financial: g('Financial') && g('Financial').value || 'تسویه',
        attendance: (g('Attendance') && g('Attendance').value || '').trim() || '—',
        branchId: branchId,
        branch: branchObj ? branchObj.name : 'نامشخص',
        parentName: '',
        parentNationalId: '',
        parentFatherName: '',
        parentBirthDate: '',
        parentPhone: ''
    };
    if (isNaN(data.remaining)) data.remaining = 0;
    if (under18) {
        data.parentName = (g('ParentName') && g('ParentName').value || '').trim();
        data.parentNationalId = (g('ParentNationalId') && g('ParentNationalId').value || '').trim();
        data.parentFatherName = (g('ParentFatherName') && g('ParentFatherName').value || '').trim();
        data.parentBirthDate = g('ParentBirthDate') && g('ParentBirthDate').value || '';
        data.parentPhone = (g('ParentPhone') && g('ParentPhone').value || '').trim();
    }
    return data;
}

function validateStudentData(data) {
    if (!data.name || !data.phone || !data.nationalId || !data.fatherName || !data.birthDate) {
        alert('لطفاً نام، شماره تماس، کد ملی، نام پدر و تاریخ تولد را وارد کنید.');
        return false;
    }
    if (!data.branchId) {
        alert('انتخاب شعبه الزامی است.');
        return false;
    }
    if (window.isStudentUnder18(data.birthDate)) {
        if (!data.parentName || !data.parentNationalId || !data.parentFatherName || !data.parentBirthDate || !data.parentPhone) {
            alert('برای هنرجویان زیر ۱۸ سال، تکمیل تمام اطلاعات والد الزامی است.');
            return false;
        }
    }
    return true;
}

window.openAddStudentModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getStudentAddModalHTML
        ? window.getStudentAddModalHTML() : '';
};

window.saveStudent = async function () {
    const data = readStudentForm('stu');
    if (!validateStudentData(data)) return;
    allStudents.unshift(Object.assign({}, data, {
        id: Date.now(),
        remaining: typeof data.remaining === 'number' ? data.remaining : 8,
        financial: data.financial || 'تسویه',
        attendance: data.attendance || '—'
    }));
    window.filterStudents();
    closeModal();
    alert('✅ هنرجو با موفقیت ثبت شد');
};

window.viewStudent = async function (id) {
    const item = allStudents.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getStudentDetailsModalHTML
        ? window.getStudentDetailsModalHTML(item) : '';
};

window.editStudent = async function (id) {
    const item = allStudents.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getStudentEditModalHTML
        ? window.getStudentEditModalHTML(item) : '';
};

window.saveEditedStudent = async function (id) {
    const data = readStudentForm('editStu');
    if (!validateStudentData(data)) return;
    const index = allStudents.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allStudents[index] = Object.assign({}, allStudents[index], data);
    await branchRequest('/academy/admin/members/' + id + '/update', {payload_b64:encodeBranchPayload(allStudents[index])});
    window.filterStudents();
    closeModal();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

window.toggleStudentInlineEdit = async function (id) {
    editingStudentRowId = editingStudentRowId === id ? null : id;
    window.renderStudentsTable(filteredStudents);
};

window.saveInlineStudent = async function (id) {
    const data = readStudentForm('inlineStu' + id);
    if (!validateStudentData(data)) return;
    const index = allStudents.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allStudents[index] = Object.assign({}, allStudents[index], data);
    await branchRequest('/academy/admin/members/' + id + '/update', {payload_b64:encodeBranchPayload(allStudents[index])});
    editingStudentRowId = null;
    window.filterStudents();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

window.deleteStudent = async function (id) {
    if (!(await AppDialog.confirmDelete(allStudents, id, 'هنرجو'))) return;
    await branchRequest('/academy/admin/members/' + id + '/delete', {});
    allStudents = allStudents.filter(function (s) { return s.id !== id; });
    if (editingStudentRowId === id) editingStudentRowId = null;
    window.filterStudents();
};

window.exportStudentsToExcel = async function () {
    const data = filteredStudents.length ? filteredStudents : allStudents;
    let csv = '\uFEFFردیف,نام,کد ملی,نام پدر,تاریخ تولد,شماره تماس,آدرس,تاریخ ثبت‌نام,ساز,سطح,استاد,شعبه,جلسات باقی‌مانده,وضعیت مالی,حضور,نام والد,کد ملی والد,تلفن والد\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.name + '","' + (item.nationalId || '') + '","' + (item.fatherName || '') + '","' +
            (item.birthDate || '') + '","' + (item.phone || '') + '","' + (item.address || '') + '","' +
            (item.registrationDate || '') + '","' + item.instrument + '","' + item.level + '","' + item.teacher + '","' +
            item.branch + '",' + item.remaining + ',"' + item.financial + '","' + item.attendance + '","' +
            (item.parentName || '') + '","' + (item.parentNationalId || '') + '","' + (item.parentPhone || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'هنرجویان_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportStudentsToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getStudentPDFModalHTML
        ? window.getStudentPDFModalHTML(studentPdfColumns) : '';
};

window.generateStudentsPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('stuPdfTitle') && document.getElementById('stuPdfTitle').value || 'گزارش هنرجویان';
    const subtitle = document.getElementById('stuPdfSubtitle') && document.getElementById('stuPdfSubtitle').value || '';
    const footer = document.getElementById('stuPdfFooter') && document.getElementById('stuPdfFooter').value || '';
    const format = document.getElementById('stuPdfFormat') && document.getElementById('stuPdfFormat').value || 'a4';
    const orientation = document.getElementById('stuPdfOrientation') && document.getElementById('stuPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('stuPdfIncludeDate') && document.getElementById('stuPdfIncludeDate').checked;
    const headerColor = document.getElementById('stuPdfHeaderColor') && document.getElementById('stuPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('stuPdfEvenRowColor') && document.getElementById('stuPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('stuPdfOddRowColor') && document.getElementById('stuPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = studentPdfColumns.filter(function (c) {
        return document.getElementById('stuPdfCol-' + c.field) && document.getElementById('stuPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredStudents.length ? filteredStudents : allStudents;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getStudentPDFPageHTML(p + 1, pageRows, p === 0, {
            title: title, subtitle: subtitle, footer: footer, includeDate: includeDate, date: date,
            headerColor: headerColor, evenRowColor: evenRowColor, oddRowColor: oddRowColor,
            selectedColumns: selectedColumns, rowsPerPage: rowsPerPage, totalPages: totalPages
        });
        document.body.appendChild(wrap);
        canvasPages.push(await html2canvas(wrap, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }));
        wrap.remove();
    }
    const doc = new window.jspdf.jsPDF({ orientation: orientation, unit: 'pt', format: format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20, imgWidth = pageWidth - margin * 2;
    canvasPages.forEach(function (canvas, i) {
        if (i > 0) doc.addPage();
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, (canvas.height * imgWidth) / canvas.width);
    });
    doc.save('هنرجویان_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('studentsTable')) {
        window.renderStudentBranchTabs();
        window.filterStudents();
    }
}, 200);
})();
