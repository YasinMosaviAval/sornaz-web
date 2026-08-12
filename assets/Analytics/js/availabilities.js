(function () {
'use strict';
// ==================== برنامه زمانی شعبه‌ها (ایزوله) ====================
window.branchScheduleDaysList = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
window.branchScheduleStatusesList = ['فعال', 'غیرفعال', 'پر شده', 'در انتظار تأیید'];
window.branchScheduleRepeatList = ['هفتگی', 'دو هفته', 'سه هفته', 'چهار هفته', 'ماهانه', 'سالانه', 'بی‌تکرار'];
window.branchScheduleTimezoneList = [
    { value: 'Asia/Tehran', label: 'تهران (Asia/Tehran)' },
    { value: 'Asia/Dubai', label: 'دبی (Asia/Dubai)' },
    { value: 'Asia/Istanbul', label: 'استانبول (Asia/Istanbul)' },
    { value: 'Europe/London', label: 'لندن (Europe/London)' },
    { value: 'Europe/Paris', label: 'پاریس (Europe/Paris)' },
    { value: 'Europe/Berlin', label: 'برلین (Europe/Berlin)' },
    { value: 'Europe/Rome', label: 'رم (Europe/Rome)' },
    { value: 'Europe/Amsterdam', label: 'آمستردام (Europe/Amsterdam)' },
    { value: 'America/New_York', label: 'نیویورک (America/New_York)' },
    { value: 'America/Chicago', label: 'شیکاگو (America/Chicago)' },
    { value: 'America/Los_Angeles', label: 'لس‌آنجلس (America/Los_Angeles)' },
    { value: 'America/Toronto', label: 'تورنتو (America/Toronto)' },
    { value: 'UTC', label: 'UTC' }
];

window.toggleBranchScheduleRepeatDate = async function (wrapId, repeatValue) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;
    const show = (repeatValue === 'ماهانه' || repeatValue === 'سالانه');
    wrap.classList.toggle('hidden', !show);
};

window.getBranchScheduleBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
};

function timeToMinutes(t) {
    if (!t) return 0;
    const p = String(t).split(':');
    return parseInt(p[0], 10) * 60 + parseInt(p[1] || 0, 10);
}
function minutesToTime(m) {
    // تا 24:00 برای پایان بازه مجاز است؛ برای اسلات‌های انتخابی فقط 00:00–23:30
    const h = Math.floor(m / 60);
    const mm = m % 60;
    return String(h).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
}
/** کل ۲۴ ساعت روز به‌صورت استاتیک (هر نیم‌ساعت از 00:00 تا 23:30) */
function getFullDaySlots() {
    const slots = [];
    for (let m = 0; m < 24 * 60; m += 30) slots.push(minutesToTime(m));
    return slots;
}
function mergeConsecutiveSlots(slots) {
    if (!slots || !slots.length) return [];
    const mins = slots.map(timeToMinutes).sort(function (a, b) { return a - b; });
    const ranges = [];
    let rangeStart = mins[0], prev = mins[0];
    for (let i = 1; i < mins.length; i++) {
        if (mins[i] === prev + 30) prev = mins[i];
        else {
            ranges.push({ start: minutesToTime(rangeStart), end: minutesToTime(prev + 30) });
            rangeStart = mins[i]; prev = mins[i];
        }
    }
    ranges.push({ start: minutesToTime(rangeStart), end: minutesToTime(prev + 30) });
    return ranges;
}
function rangeLabel(range) { return range.start + '-' + range.end; }

window.buildBranchScheduleTimeSlotsHTML = async function (containerId, branchId, selectedSlots) {
    const slots = getFullDaySlots();
    const selected = (selectedSlots || []).map(String);
    return '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">' +
        slots.map(function (s) {
            const checked = selected.indexOf(s) !== -1 ? 'checked' : '';
            return '<label class="inline-flex items-center gap-2 text-sm border border-gray-200 rounded-xl px-2 py-1.5 hover:bg-gray-50 cursor-pointer">' +
                '<input type="checkbox" class="bs-time-slot" value="' + s + '" ' + checked + '> ' + s +
                '</label>';
        }).join('') + '</div>';
};

window.refreshBranchScheduleTimeSlots = async function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildBranchScheduleTimeSlotsHTML(containerId, branchId, selectedSlots || []);
};

let allBranchSchedules = [];
(function buildSample() {
    const branches = window.getBranchScheduleBranches();
    const allSlots = getFullDaySlots();
    let id = 1;
    for (let i = 0; i < 100 && allBranchSchedules.length < 100; i++) {
        const day = window.branchScheduleDaysList[Math.floor(Math.random() * window.branchScheduleDaysList.length)];
        const status = window.branchScheduleStatusesList[Math.floor(Math.random() * window.branchScheduleStatusesList.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const count = 2 + Math.floor(Math.random() * 6);
        const startIdx = Math.floor(Math.random() * Math.max(1, allSlots.length - count - 4));
        let picked = [];
        if (Math.random() > 0.4) {
            for (let k = 0; k < count; k++) picked.push(allSlots[startIdx + k]);
        } else {
            const c1 = 1 + Math.floor(Math.random() * 3);
            for (let k = 0; k < c1; k++) picked.push(allSlots[startIdx + k]);
            const gap = 3 + Math.floor(Math.random() * 4);
            const s2 = startIdx + c1 + gap;
            const c2 = 1 + Math.floor(Math.random() * 3);
            for (let k = 0; k < c2 && s2 + k < allSlots.length; k++) picked.push(allSlots[s2 + k]);
        }
        picked = picked.filter(Boolean);
        mergeConsecutiveSlots(picked).forEach(function (range) {
            const rangeSlots = [];
            for (let m = timeToMinutes(range.start); m < timeToMinutes(range.end); m += 30) rangeSlots.push(minutesToTime(m));
            const repeatPeriod = window.branchScheduleRepeatList[Math.floor(Math.random() * window.branchScheduleRepeatList.length)];
            const tz = window.branchScheduleTimezoneList[Math.floor(Math.random() * window.branchScheduleTimezoneList.length)];
            let repeatDate = '';
            if (repeatPeriod === 'ماهانه' || repeatPeriod === 'سالانه') {
                const d = new Date();
                d.setDate(1 + Math.floor(Math.random() * 28));
                repeatDate = d.toISOString().split('T')[0];
            }
            allBranchSchedules.push({
                id: id++, day: day,
                slots: rangeSlots, timeLabel: rangeLabel(range), time: rangeLabel(range),
                branchId: branch.id, branchName: branch.name, status: status,
                repeatPeriod: repeatPeriod, repeatDate: repeatDate, timezone: tz.value,
                summary: 'ساعات کاری ' + branch.name + ' در ' + day,
                description: 'زمان‌بندی شعبه ' + branch.name + ' — ' + rangeLabel(range)
            });
        });
    }
    allBranchSchedules = allBranchSchedules.slice(0, 100);
})();

let currentBranchScheduleBranch = 'all';
let branchSchedulesCurrentPage = 1;
const branchSchedulesPerPage = 10;
let filteredBranchSchedules = allBranchSchedules.slice();
let editingBranchScheduleRowId = null;
let bsSortField = '';
let bsSortDirection = 'asc';

const bsPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'day', label: 'روز' },
    { field: 'timeLabel', label: 'ساعت' },
    { field: 'repeatPeriod', label: 'دوره تکرار' },
    { field: 'timezone', label: 'منطقه زمانی' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'status', label: 'وضعیت' }
];

function sortBranchScheduleItems() {
    if (!bsSortField) return;
    filteredBranchSchedules.sort(function (a, b) {
        let av = a[bsSortField], bv = b[bsSortField];
        if (bsSortField === 'timeLabel' || bsSortField === 'time') {
            av = (a.slots && a.slots[0]) || a.timeLabel || '';
            bv = (b.slots && b.slots[0]) || b.timeLabel || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return bsSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return bsSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateBranchScheduleSortIcons = async function () {
    ['day', 'timeLabel', 'repeatPeriod', 'timezone', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('bsSortIcon-' + f);
        if (!icon) return;
        icon.textContent = bsSortField === f ? (bsSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortBranchSchedulesBy = async function (field) {
    if (bsSortField === field) bsSortDirection = bsSortDirection === 'asc' ? 'desc' : 'asc';
    else { bsSortField = field; bsSortDirection = 'asc'; }
    sortBranchScheduleItems();
    window.renderBranchSchedulesTable(filteredBranchSchedules);
    window.updateBranchScheduleSortIcons();
};

window.renderBranchSchedulesBranchTabs = async function () {
    const container = document.getElementById('branchSchedulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.branch-schedule-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    window.getBranchScheduleBranches().forEach(function (b) {
        const active = currentBranchScheduleBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'branch-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { window.filterBranchSchedulesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterBranchSchedulesByBranch = async function (branchId) {
    currentBranchScheduleBranch = branchId;
    document.querySelectorAll('.branch-schedule-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.branch-schedule-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = window.getBranchScheduleBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    window.filterBranchSchedules();
};

window.filterBranchSchedules = async function () {
    const day = document.getElementById('filterBranchDay') && document.getElementById('filterBranchDay').value || '';
    const status = document.getElementById('filterBranchStatus') && document.getElementById('filterBranchStatus').value || '';
    const repeat = document.getElementById('filterBranchRepeat') && document.getElementById('filterBranchRepeat').value || '';
    const timezone = document.getElementById('filterBranchTimezone') && document.getElementById('filterBranchTimezone').value || '';

    filteredBranchSchedules = allBranchSchedules.filter(function (s) {
        const matchBranch = currentBranchScheduleBranch === 'all' || s.branchId == currentBranchScheduleBranch;
        const matchDay = !day || s.day === day;
        const matchStatus = !status || s.status === status;
        const matchRepeat = !repeat || s.repeatPeriod === repeat;
        const matchTz = !timezone || s.timezone === timezone;
        return matchBranch && matchDay && matchStatus && matchRepeat && matchTz;
    });

    branchSchedulesCurrentPage = 1;
    sortBranchScheduleItems();
    window.renderBranchSchedulesTable(filteredBranchSchedules);
};

window.renderBranchSchedulesTable = async function (list) {
    list = list || filteredBranchSchedules;
    const tbody = document.querySelector('#branchSchedulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / branchSchedulesPerPage) || 1;
    if (branchSchedulesCurrentPage > totalPages) branchSchedulesCurrentPage = totalPages;

    const start = (branchSchedulesCurrentPage - 1) * branchSchedulesPerPage;
    const end = start + branchSchedulesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getBranchScheduleEmptyRowHTML ? window.getBranchScheduleEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getBranchScheduleRowHTML ? window.getBranchScheduleRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingBranchScheduleRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getBranchScheduleInlineExpandRowHTML ? window.getBranchScheduleInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateBranchSchedulesPagination(list.length, start, end, totalPages);
    window.updateBranchScheduleSortIcons();
};

function updateBranchSchedulesPagination(total, start, end, totalPages) {
    const info = document.getElementById('branchSchedulesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' زمان‌بندی';
    }
    const pagination = document.getElementById('branchSchedulesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeBranchSchedulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (branchSchedulesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeBranchSchedulesPage(' + (branchSchedulesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (branchSchedulesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, branchSchedulesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeBranchSchedulesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === branchSchedulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeBranchSchedulesPage(' + (branchSchedulesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (branchSchedulesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeBranchSchedulesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (branchSchedulesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeBranchSchedulesPage = async function (page) {
    const totalPages = Math.ceil(filteredBranchSchedules.length / branchSchedulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    branchSchedulesCurrentPage = page;
    window.renderBranchSchedulesTable(filteredBranchSchedules);
};

function readSelectedSlots(prefix) {
    const containerId = prefix ? prefix + 'TimeSlots' : 'bsTimeSlots';
    const container = document.getElementById(containerId);
    if (!container) return [];
    return Array.from(container.querySelectorAll('.bs-time-slot:checked')).map(function (cb) { return cb.value; });
}

function readBranchScheduleForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'bs' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = window.getBranchScheduleBranches().find(function (b) { return b.id === branchId; });
    const slots = readSelectedSlots(prefix);
    const ranges = mergeConsecutiveSlots(slots);
    const repeatPeriod = f('Repeat') && f('Repeat').value || 'هفتگی';
    return {
        branchId: branchId, branchName: branch ? branch.name : 'نامشخص',
        day: f('Day') && f('Day').value || '',
        status: f('Status') && f('Status').value || 'فعال',
        repeatPeriod: repeatPeriod,
        repeatDate: (repeatPeriod === 'ماهانه' || repeatPeriod === 'سالانه')
            ? (f('RepeatDate') && f('RepeatDate').value || '') : '',
        timezone: f('Timezone') && f('Timezone').value || 'Asia/Tehran',
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Description') && f('Description').value.trim() || '',
        slots: slots, ranges: ranges
    };
}

function expandRangesToRows(base, ranges) {
    return ranges.map(function (range, idx) {
        const rangeSlots = [];
        for (let m = timeToMinutes(range.start); m < timeToMinutes(range.end); m += 30) rangeSlots.push(minutesToTime(m));
        return Object.assign({}, base, {
            id: Date.now() + idx, slots: rangeSlots,
            timeLabel: rangeLabel(range), time: rangeLabel(range)
        });
    });
}

window.openAddBranchScheduleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleAddModalHTML
        ? window.getBranchScheduleAddModalHTML() : '';
};

window.saveBranchSchedule = async function () {
    const data = readBranchScheduleForm('');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    expandRangesToRows({
        day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allBranchSchedules.unshift(r); });
    window.filterBranchSchedules();
    closeModal();
    alert('✅ بازه(های) زمانی ثبت شد');
};

window.viewBranchSchedule = async function (id) {
    const item = allBranchSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleDetailsModalHTML
        ? window.getBranchScheduleDetailsModalHTML(item) : '';
};

window.editBranchSchedule = async function (id) {
    const item = allBranchSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleEditModalHTML
        ? window.getBranchScheduleEditModalHTML(item) : '';
};

window.saveEditedBranchSchedule = async function (id) {
    const data = readBranchScheduleForm('editBs');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allBranchSchedules = allBranchSchedules.filter(function (x) { return x.id !== id; });
    expandRangesToRows({
        day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allBranchSchedules.unshift(r); });
    editingBranchScheduleRowId = null;
    window.filterBranchSchedules();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleBranchScheduleInlineEdit = async function (id) {
    editingBranchScheduleRowId = editingBranchScheduleRowId === id ? null : id;
    window.renderBranchSchedulesTable(filteredBranchSchedules);
};

window.saveInlineBranchSchedule = async function (id) {
    const data = readBranchScheduleForm('inlineBs' + id);
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allBranchSchedules = allBranchSchedules.filter(function (x) { return x.id !== id; });
    expandRangesToRows({
        day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allBranchSchedules.unshift(r); });
    editingBranchScheduleRowId = null;
    window.filterBranchSchedules();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteBranchSchedule = async function (id) {
    if (!(await AppDialog.confirm('حذف این زمان‌بندی؟'))) return;
    allBranchSchedules = allBranchSchedules.filter(function (s) { return s.id !== id; });
    if (editingBranchScheduleRowId === id) editingBranchScheduleRowId = null;
    window.filterBranchSchedules();
};

window.exportBranchSchedulesToExcel = async function () {
    const data = filteredBranchSchedules.length ? filteredBranchSchedules : allBranchSchedules;
    let csv = '\uFEFFردیف,روز,ساعت,دوره تکرار,منطقه زمانی,شعبه,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.day + '","' +
            (item.timeLabel || item.time || '') + '","' + (item.repeatPeriod || '') + '","' + (item.timezone || '') + '","' +
            item.branchName + '","' + item.status + '","' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'زمانبندی_شعبه_ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportBranchSchedulesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getBranchSchedulePDFModalHTML
        ? window.getBranchSchedulePDFModalHTML(bsPdfColumns) : '';
};

window.generateBranchSchedulesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('bsPdfTitle') && document.getElementById('bsPdfTitle').value || 'گزارش برنامه زمانی شعبه‌ها';
    const subtitle = document.getElementById('bsPdfSubtitle') && document.getElementById('bsPdfSubtitle').value || '';
    const footer = document.getElementById('bsPdfFooter') && document.getElementById('bsPdfFooter').value || '';
    const format = document.getElementById('bsPdfFormat') && document.getElementById('bsPdfFormat').value || 'a4';
    const orientation = document.getElementById('bsPdfOrientation') && document.getElementById('bsPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('bsPdfIncludeDate') && document.getElementById('bsPdfIncludeDate').checked;
    const headerColor = document.getElementById('bsPdfHeaderColor') && document.getElementById('bsPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('bsPdfEvenRowColor') && document.getElementById('bsPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('bsPdfOddRowColor') && document.getElementById('bsPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = bsPdfColumns.filter(function (c) {
        return document.getElementById('bsPdfCol-' + c.field) && document.getElementById('bsPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredBranchSchedules.length ? filteredBranchSchedules : allBranchSchedules;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getBranchSchedulePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('زمانبندی_شعبه_ها_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('branchSchedulesTable')) {
        window.renderBranchSchedulesBranchTabs();
        window.filterBranchSchedules();
    }
}, 200);
})();
