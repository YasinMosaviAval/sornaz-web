// ==================== برنامه زمانی کلاس‌ها ====================
const scheduleDays = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
const scheduleTypes = ['خصوصی', 'گروهی', 'آنلاین'];
const scheduleStatuses = ['فعال', 'غیرفعال', 'تأیید شده', 'در انتظار تأیید', 'رد شده', 'حذف‌شده', 'پایان یافته'];

const scheduleStudentPool = [
    { id: 1, name: 'سارا احمدی' }, { id: 2, name: 'امیر حسینی' }, { id: 3, name: 'زهرا کریمی' },
    { id: 4, name: 'علی محمدی' }, { id: 5, name: 'نگار رضایی' }, { id: 6, name: 'پارسا نوری' },
    { id: 7, name: 'مهسا جعفری' }, { id: 8, name: 'کیان نوری' }, { id: 9, name: 'هستی احمدی' },
    { id: 10, name: 'آرین محمدی' }, { id: 11, name: 'گروه ۶ نفره' }, { id: 12, name: 'گروه ۸ نفره' }
];
const scheduleTeacherPool = [
    { id: 1, name: 'استاد موسوی' }, { id: 2, name: 'استاد رضایی' }, { id: 3, name: 'استاد بهرامی' },
    { id: 4, name: 'استاد کاظمی' }, { id: 5, name: 'استاد نوری' }, { id: 6, name: 'استاد احمدی' }
];
const scheduleInstrumentPool = [
    { id: 1, name: 'پیانو' }, { id: 2, name: 'گیتار' }, { id: 3, name: 'ویولن' },
    { id: 4, name: 'آواز' }, { id: 5, name: 'درام' }, { id: 6, name: 'سنتور' }, { id: 7, name: 'تار' }
];
const scheduleClassroomPool = [
    { id: 1, name: 'کلاس پیانو ۱' }, { id: 2, name: 'کلاس پیانو ۲' }, { id: 3, name: 'کلاس گیتار A' },
    { id: 4, name: 'سالن تمرین گروهی' }, { id: 5, name: 'کلاس آواز' }, { id: 6, name: 'کلاس درام' },
    { id: 7, name: 'کلاس ویولن' }
];

window.getScheduleBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
};
window.getScheduleStudentOptions = async function () {
    if (typeof allStudents !== 'undefined' && allStudents.length) {
        return allStudents.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return scheduleStudentPool.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
};
window.getScheduleTeacherOptions = async function () {
    if (typeof allStaff !== 'undefined' && allStaff.length) {
        return allStaff.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return scheduleTeacherPool.map(function (t) { return { value: t.id, label: t.name, id: t.id, name: t.name }; });
};
window.getScheduleInstrumentOptions = async function () {
    if (typeof sampleInstruments !== 'undefined' && sampleInstruments.length) {
        return sampleInstruments.map(function (i) { return { value: i.id, label: i.title, id: i.id, name: i.title }; });
    }
    return scheduleInstrumentPool.map(function (i) { return { value: i.id, label: i.name, id: i.id, name: i.name }; });
};
window.getScheduleClassroomOptions = async function () {
    if (typeof allClassrooms !== 'undefined' && allClassrooms.length) {
        return allClassrooms.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
    }
    return scheduleClassroomPool.map(function (c) { return { value: c.id, label: c.name, id: c.id, name: c.name }; });
};

function padTime(h, m) {
    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
}

// ۲۰۰ برنامه نمونه
let allSchedules = [];
(function buildSampleSchedules() {
    const branches = getScheduleBranches();
    for (let i = 1; i <= 200; i++) {
        const day = scheduleDays[Math.floor(Math.random() * scheduleDays.length)];
        const type = scheduleTypes[Math.floor(Math.random() * scheduleTypes.length)];
        const status = scheduleStatuses[Math.floor(Math.random() * scheduleStatuses.length)];
        const student = scheduleStudentPool[Math.floor(Math.random() * scheduleStudentPool.length)];
        const teacher = scheduleTeacherPool[Math.floor(Math.random() * scheduleTeacherPool.length)];
        const instrument = scheduleInstrumentPool[Math.floor(Math.random() * scheduleInstrumentPool.length)];
        const classroom = scheduleClassroomPool[Math.floor(Math.random() * scheduleClassroomPool.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const startH = 8 + Math.floor(Math.random() * 10);
        const startM = Math.random() > 0.5 ? 0 : 30;
        const duration = 1 + Math.floor(Math.random() * 2);
        const endH = startH + duration;
        const startTime = padTime(startH, startM);
        const endTime = padTime(endH, startM);
        allSchedules.push({
            id: i,
            title: type + ' ' + instrument.name + ' - ' + day,
            summary: 'جلسه ' + type + ' با ' + teacher.name,
            description: 'برنامه ' + day + ' ساعت ' + startTime + ' تا ' + endTime + ' در ' + classroom.name,
            day: day,
            startTime: startTime,
            endTime: endTime,
            time: startTime + '-' + endTime,
            studentId: student.id,
            student: student.name,
            teacherId: teacher.id,
            teacher: teacher.name,
            instrumentId: instrument.id,
            instrument: instrument.name,
            classroomId: classroom.id,
            classroom: classroom.name,
            branchId: branch.id,
            branchName: branch.name,
            type: type,
            status: status
        });
    }
})();

let currentScheduleBranch = 'all';
let schedulesCurrentPage = 1;
const schedulesPerPage = 10;
let filteredSchedules = allSchedules.slice();
let editingScheduleRowId = null;
let scheduleSortField = '';
let scheduleSortDirection = 'asc';

const schedulePdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'title', label: 'عنوان' },
    { field: 'day', label: 'روز' },
    { field: 'time', label: 'ساعت' },
    { field: 'student', label: 'هنرجو' },
    { field: 'teacher', label: 'استاد' },
    { field: 'instrument', label: 'ساز' },
    { field: 'classroom', label: 'کلاس' },
    { field: 'type', label: 'نوع' },
    { field: 'status', label: 'وضعیت' }
];

// ==================== مرتب‌سازی ====================
function sortScheduleItems() {
    if (!scheduleSortField) return;
    filteredSchedules.sort(function (a, b) {
        let av = a[scheduleSortField], bv = b[scheduleSortField];
        if (scheduleSortField === 'time') {
            av = a.startTime || ''; bv = b.startTime || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return scheduleSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return scheduleSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateScheduleSortIcons = async function () {
    ['title', 'day', 'time', 'student', 'teacher', 'instrument', 'classroom', 'type', 'status'].forEach(function (f) {
        const icon = document.getElementById('scheduleSortIcon-' + f);
        if (!icon) return;
        icon.textContent = scheduleSortField === f ? (scheduleSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortSchedulesBy = async function (field) {
    if (scheduleSortField === field) scheduleSortDirection = scheduleSortDirection === 'asc' ? 'desc' : 'asc';
    else { scheduleSortField = field; scheduleSortDirection = 'asc'; }
    sortScheduleItems();
    renderSchedulesTable(filteredSchedules);
    updateScheduleSortIcons();
};

// ==================== تب شعبه‌ها ====================
window.renderSchedulesBranchTabs = async function () {
    const container = document.getElementById('schedulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.schedule-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    getScheduleBranches().forEach(function (b) {
        const active = currentScheduleBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { filterSchedulesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterSchedulesByBranch = async function (branchId) {
    currentScheduleBranch = branchId;
    document.querySelectorAll('.schedule-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.schedule-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getScheduleBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterSchedules();
};

window.renderScheduleFilterOptions = async function () {
    const instSel = document.getElementById('filterScheduleInstrument');
    const classSel = document.getElementById('filterScheduleClassroom');
    if (instSel) {
        const names = new Set(allSchedules.map(function (s) { return s.instrument; }).filter(Boolean));
        const cur = instSel.value;
        instSel.innerHTML = '<option value="">همه سازها</option>' +
            Array.from(names).sort().map(function (n) {
                return '<option value="' + n + '"' + (n === cur ? ' selected' : '') + '>' + n + '</option>';
            }).join('');
    }
    if (classSel) {
        const names = new Set(allSchedules.map(function (s) { return s.classroom; }).filter(Boolean));
        const cur = classSel.value;
        classSel.innerHTML = '<option value="">همه کلاس‌ها</option>' +
            Array.from(names).sort().map(function (n) {
                return '<option value="' + n + '"' + (n === cur ? ' selected' : '') + '>' + n + '</option>';
            }).join('');
    }
};

function timeToMinutes(t) {
    if (!t) return null;
    const parts = String(t).split(':');
    if (parts.length < 2) return null;
    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
}

window.filterSchedules = async function () {
    const search = (document.getElementById('scheduleSearch') && document.getElementById('scheduleSearch').value || '').trim().toLowerCase();
    const day = document.getElementById('filterScheduleDay') && document.getElementById('filterScheduleDay').value || '';
    const type = document.getElementById('filterScheduleType') && document.getElementById('filterScheduleType').value || '';
    const status = document.getElementById('filterScheduleStatus') && document.getElementById('filterScheduleStatus').value || '';
    const instrument = document.getElementById('filterScheduleInstrument') && document.getElementById('filterScheduleInstrument').value || '';
    const classroom = document.getElementById('filterScheduleClassroom') && document.getElementById('filterScheduleClassroom').value || '';
    const timeFrom = document.getElementById('filterScheduleTimeFrom') && document.getElementById('filterScheduleTimeFrom').value || '';
    const timeTo = document.getElementById('filterScheduleTimeTo') && document.getElementById('filterScheduleTimeTo').value || '';
    const fromMin = timeToMinutes(timeFrom);
    const toMin = timeToMinutes(timeTo);

    filteredSchedules = allSchedules.filter(function (s) {
        const matchBranch = currentScheduleBranch === 'all' || s.branchId == currentScheduleBranch;
        const matchSearch = !search ||
            (s.student || '').toLowerCase().includes(search) ||
            (s.teacher || '').toLowerCase().includes(search) ||
            (s.title || '').toLowerCase().includes(search);
        const matchDay = !day || s.day === day;
        const matchType = !type || s.type === type;
        const matchStatus = !status || s.status === status;
        const matchInst = !instrument || s.instrument === instrument;
        const matchClass = !classroom || s.classroom === classroom;
        let matchTime = true;
        if (fromMin != null || toMin != null) {
            const sMin = timeToMinutes(s.startTime);
            const eMin = timeToMinutes(s.endTime);
            if (fromMin != null && eMin != null && eMin < fromMin) matchTime = false;
            if (toMin != null && sMin != null && sMin > toMin) matchTime = false;
        }
        return matchBranch && matchSearch && matchDay && matchType && matchStatus && matchInst && matchClass && matchTime;
    });

    schedulesCurrentPage = 1;
    sortScheduleItems();
    renderSchedulesTable(filteredSchedules);
};

window.renderSchedulesTable = async function (list) {
    list = list || filteredSchedules;
    const tbody = document.querySelector('#schedulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / schedulesPerPage) || 1;
    if (schedulesCurrentPage > totalPages) schedulesCurrentPage = totalPages;

    const start = (schedulesCurrentPage - 1) * schedulesPerPage;
    const end = start + schedulesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getScheduleEmptyRowHTML ? window.getScheduleEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getScheduleRowHTML ? window.getScheduleRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingScheduleRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getScheduleInlineExpandRowHTML ? window.getScheduleInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateSchedulesPagination(list.length, start, end, totalPages);
    updateScheduleSortIcons();
};

function updateSchedulesPagination(total, start, end, totalPages) {
    const info = document.getElementById('schedulesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' برنامه';
    }
    const pagination = document.getElementById('schedulesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeSchedulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (schedulesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeSchedulesPage(' + (schedulesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (schedulesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, schedulesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeSchedulesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === schedulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeSchedulesPage(' + (schedulesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (schedulesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeSchedulesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (schedulesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeSchedulesPage = async function (page) {
    const totalPages = Math.ceil(filteredSchedules.length / schedulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    schedulesCurrentPage = page;
    renderSchedulesTable(filteredSchedules);
};

// ==================== فرم ====================
function readScheduleForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'sch' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = getScheduleBranches().find(function (b) { return b.id === branchId; });
    const studentSel = f('Student');
    const teacherSel = f('Teacher');
    const instSel = f('Instrument');
    const classSel = f('Classroom');
    const startTime = f('StartTime') && f('StartTime').value || '';
    const endTime = f('EndTime') && f('EndTime').value || '';
    return {
        branchId: branchId,
        branchName: branch ? branch.name : 'نامشخص',
        title: f('Title') && f('Title').value.trim() || '',
        day: f('Day') && f('Day').value || '',
        startTime: startTime,
        endTime: endTime,
        time: startTime && endTime ? (startTime + '-' + endTime) : '',
        type: f('Type') && f('Type').value || 'خصوصی',
        status: f('Status') && f('Status').value || 'فعال',
        studentId: studentSel && studentSel.value || '',
        student: studentSel && studentSel.selectedOptions[0] ? studentSel.selectedOptions[0].textContent : '',
        teacherId: teacherSel && teacherSel.value || '',
        teacher: teacherSel && teacherSel.selectedOptions[0] ? teacherSel.selectedOptions[0].textContent : '',
        instrumentId: instSel && instSel.value || '',
        instrument: instSel && instSel.selectedOptions[0] && instSel.value ? instSel.selectedOptions[0].textContent : '—',
        classroomId: classSel && classSel.value || '',
        classroom: classSel && classSel.selectedOptions[0] && classSel.value ? classSel.selectedOptions[0].textContent : '—',
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Description') && f('Description').value.trim() || ''
    };
}

window.openAddScheduleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getScheduleAddModalHTML ? window.getScheduleAddModalHTML() : '';
};

window.saveSchedule = async function () {
    const data = readScheduleForm('');
    if (!data.day || !data.startTime || !data.endTime || !data.studentId || !data.teacherId) {
        return alert('روز، ساعت شروع/پایان، هنرجو و استاد الزامی است');
    }
    if (!data.title) data.title = data.type + ' ' + data.instrument + ' - ' + data.day;
    allSchedules.unshift(Object.assign({ id: Date.now() }, data));
    renderScheduleFilterOptions();
    filterSchedules();
    closeModal();
    alert('✅ برنامه زمانی ثبت شد');
};

window.viewSchedule = async function (id) {
    const item = allSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getScheduleDetailsModalHTML
        ? window.getScheduleDetailsModalHTML(item) : '';
};

window.editSchedule = async function (id) {
    const item = allSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getScheduleEditModalHTML
        ? window.getScheduleEditModalHTML(item) : '';
};

window.saveEditedSchedule = async function (id) {
    const data = readScheduleForm('editSch');
    if (!data.day || !data.startTime || !data.endTime || !data.studentId || !data.teacherId) {
        return alert('روز، ساعت شروع/پایان، هنرجو و استاد الزامی است');
    }
    const index = allSchedules.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allSchedules[index] = Object.assign({}, allSchedules[index], data);
    editingScheduleRowId = null;
    renderScheduleFilterOptions();
    filterSchedules();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleScheduleInlineEdit = async function (id) {
    editingScheduleRowId = editingScheduleRowId === id ? null : id;
    renderSchedulesTable(filteredSchedules);
};

window.saveInlineSchedule = async function (id) {
    const data = readScheduleForm('inlineSch' + id);
    if (!data.day || !data.startTime || !data.endTime || !data.studentId || !data.teacherId) {
        return alert('روز، ساعت شروع/پایان، هنرجو و استاد الزامی است');
    }
    const index = allSchedules.findIndex(function (x) { return x.id === id; });
    if (index === -1) return;
    allSchedules[index] = Object.assign({}, allSchedules[index], data);
    editingScheduleRowId = null;
    renderScheduleFilterOptions();
    filterSchedules();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteSchedule = async function (id) {
    if (!(await AppDialog.confirm('حذف این برنامه؟'))) return;
    allSchedules = allSchedules.filter(function (s) { return s.id !== id; });
    if (editingScheduleRowId === id) editingScheduleRowId = null;
    renderScheduleFilterOptions();
    filterSchedules();
};

// ==================== اکسل / PDF ====================
window.exportSchedulesToExcel = async function () {
    const data = filteredSchedules.length ? filteredSchedules : allSchedules;
    let csv = '\uFEFFردیف,عنوان,روز,ساعت,هنرجو,استاد,ساز,کلاس,نوع,وضعیت,شعبه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + (item.title || '') + '","' + item.day + '","' + (item.time || '') + '","' +
            item.student + '","' + item.teacher + '","' + item.instrument + '","' + item.classroom + '","' +
            item.type + ',"' + item.status + '","' + item.branchName + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'برنامه_زمانی_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportSchedulesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getSchedulePDFModalHTML
        ? window.getSchedulePDFModalHTML(schedulePdfColumns) : '';
};

window.generateSchedulesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('schedulePdfTitle') && document.getElementById('schedulePdfTitle').value || 'گزارش برنامه زمانی';
    const subtitle = document.getElementById('schedulePdfSubtitle') && document.getElementById('schedulePdfSubtitle').value || '';
    const footer = document.getElementById('schedulePdfFooter') && document.getElementById('schedulePdfFooter').value || '';
    const format = document.getElementById('schedulePdfFormat') && document.getElementById('schedulePdfFormat').value || 'a4';
    const orientation = document.getElementById('schedulePdfOrientation') && document.getElementById('schedulePdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('schedulePdfIncludeDate') && document.getElementById('schedulePdfIncludeDate').checked;
    const headerColor = document.getElementById('schedulePdfHeaderColor') && document.getElementById('schedulePdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('schedulePdfEvenRowColor') && document.getElementById('schedulePdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('schedulePdfOddRowColor') && document.getElementById('schedulePdfOddRowColor').value || '#f8fafc';
    const selectedColumns = schedulePdfColumns.filter(function (c) {
        return document.getElementById('schedulePdfCol-' + c.field) && document.getElementById('schedulePdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredSchedules.length ? filteredSchedules : allSchedules;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getSchedulePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('برنامه_زمانی_' + date + '.pdf');
    closeModal();
};

// ==================== Init ====================
(function initSchedules() {
    setTimeout(function () {
        if (document.getElementById('schedulesTable')) {
            renderSchedulesBranchTabs();
            renderScheduleFilterOptions();
            filterSchedules();
        }
    }, 200);
})();
