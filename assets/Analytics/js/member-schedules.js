(function () {
'use strict';
// ==================== زمان‌بندی اعضا (ایزوله) ====================
window.memberScheduleDaysList = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
window.memberScheduleRolesList = ['استاد', 'منشی', 'مدیر', 'پرسنل'];
window.memberScheduleStatusesList = ['فعال', 'غیرفعال', 'پر شده', 'در انتظار تأیید'];
window.memberScheduleRepeatList = ['هفتگی', 'دو هفته', 'سه هفته', 'چهار هفته', 'ماهانه', 'سالانه', 'بی‌تکرار'];
window.memberScheduleTimezoneList = [
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

window.toggleMemberScheduleRepeatDate = async function (wrapId, repeatValue) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;
    const show = (repeatValue === 'ماهانه' || repeatValue === 'سالانه');
    wrap.classList.toggle('hidden', !show);
};

const branchWorkingHours = {
    1: { start: '08:00', end: '22:00' },
    2: { start: '09:00', end: '21:00' },
    3: { start: '08:30', end: '20:00' },
    4: { start: '09:00', end: '20:00' }
};

let memberScheduleMembers = [
    { id: 1, name: 'استاد محمد موسوی' }, { id: 2, name: 'استاد علی رضایی' },
    { id: 3, name: 'زهرا کریمی' }, { id: 4, name: 'استاد بهرامی' },
    { id: 5, name: 'امیر نوری' }, { id: 6, name: 'استاد کاظمی' },
    { id: 7, name: 'نگار احمدی' }, { id: 8, name: 'استاد نوری' },
    { id: 9, name: 'پارسا جعفری' }, { id: 10, name: 'هستی محمدی' }
];

window.getMemberScheduleBranches = async function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
};

window.getMemberScheduleMemberOptions = async function () {
    if (typeof allStaff !== 'undefined' && allStaff.length) {
        return allStaff.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return memberScheduleMembers.map(function (m) { return { value: m.id, label: m.name, id: m.id, name: m.name }; });
};

function timeToMinutes(t) {
    if (!t) return 0;
    const p = String(t).split(':');
    return parseInt(p[0], 10) * 60 + parseInt(p[1] || 0, 10);
}
function minutesToTime(m) {
    const h = Math.floor(m / 60);
    const mm = m % 60;
    return String(h).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
}
function getBranchSlots(branchId) {
    const wh = branchWorkingHours[branchId] || { start: '08:00', end: '22:00' };
    const start = timeToMinutes(wh.start);
    const end = timeToMinutes(wh.end);
    const slots = [];
    for (let m = start; m < end; m += 30) slots.push(minutesToTime(m));
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

window.buildMemberScheduleTimeSlotsHTML = async function (containerId, branchId, selectedSlots) {
    const slots = getBranchSlots(parseInt(branchId, 10) || 1);
    const selected = (selectedSlots || []).map(String);
    return '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">' +
        slots.map(function (s) {
            const checked = selected.indexOf(s) !== -1 ? 'checked' : '';
            return '<label class="inline-flex items-center gap-2 text-sm border border-gray-200 rounded-xl px-2 py-1.5 hover:bg-gray-50 cursor-pointer">' +
                '<input type="checkbox" class="ms-time-slot" value="' + s + '" ' + checked + '> ' + s +
                '</label>';
        }).join('') + '</div>';
};

window.refreshMemberScheduleTimeSlots = async function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildMemberScheduleTimeSlotsHTML(containerId, branchId, selectedSlots || []);
};

window.promptAddMemberScheduleMember = async function (selectId) {
    const name = await AppDialog.prompt('نام عضو جدید را وارد کنید:');
    const sel = document.getElementById(selectId);
    if (!name || !name.trim()) { if (sel) sel.value = ''; return; }
    const m = { id: Date.now(), name: name.trim() };
    memberScheduleMembers.push(m);
    if (sel) {
        const opt = document.createElement('option');
        opt.value = m.id; opt.textContent = m.name;
        sel.insertBefore(opt, sel.lastElementChild);
        sel.value = m.id;
    }
};

let allMemberSchedules = Array.isArray(window.adminMemberSchedulesData) ? window.adminMemberSchedulesData.slice() : [];

let currentMemberScheduleBranch = 'all';
let memberSchedulesCurrentPage = 1;
const memberSchedulesPerPage = 10;
let filteredMemberSchedules = allMemberSchedules.slice();
let editingMemberScheduleRowId = null;
let msSortField = '';
let msSortDirection = 'asc';

const msPdfColumns = [
    { field: 'index', label: 'ردیف' }, { field: 'name', label: 'نام عضو' },
    { field: 'role', label: 'نقش' }, { field: 'day', label: 'روز' },
    { field: 'timeLabel', label: 'ساعت' }, { field: 'repeatPeriod', label: 'دوره تکرار' },
    { field: 'timezone', label: 'منطقه زمانی' }, { field: 'branchName', label: 'شعبه' },
    { field: 'status', label: 'وضعیت' }
];

function sortMemberScheduleItems() {
    if (!msSortField) return;
    filteredMemberSchedules.sort(function (a, b) {
        let av = a[msSortField], bv = b[msSortField];
        if (msSortField === 'timeLabel' || msSortField === 'time') {
            av = (a.slots && a.slots[0]) || a.timeLabel || '';
            bv = (b.slots && b.slots[0]) || b.timeLabel || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return msSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return msSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateMemberScheduleSortIcons = async function () {
    ['name', 'role', 'day', 'timeLabel', 'repeatPeriod', 'timezone', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('msSortIcon-' + f);
        if (!icon) return;
        icon.textContent = msSortField === f ? (msSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortMemberSchedulesBy = async function (field) {
    if (msSortField === field) msSortDirection = msSortDirection === 'asc' ? 'desc' : 'asc';
    else { msSortField = field; msSortDirection = 'asc'; }
    sortMemberScheduleItems();
    window.renderMemberSchedulesTable(filteredMemberSchedules);
    window.updateMemberScheduleSortIcons();
};

window.renderMemberSchedulesBranchTabs = async function () {
    const container = document.getElementById('memberSchedulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.member-schedule-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    window.getMemberScheduleBranches().forEach(function (b) {
        const active = currentMemberScheduleBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'member-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { window.filterMemberSchedulesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterMemberSchedulesByBranch = async function (branchId) {
    currentMemberScheduleBranch = branchId;
    document.querySelectorAll('.member-schedule-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.member-schedule-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = window.getMemberScheduleBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    window.filterMemberSchedules();
};

window.filterMemberSchedules = async function () {
    const search = (document.getElementById('memberScheduleSearch') && document.getElementById('memberScheduleSearch').value || '').trim().toLowerCase();
    const role = document.getElementById('filterMemberRole') && document.getElementById('filterMemberRole').value || '';
    const day = document.getElementById('filterMemberDay') && document.getElementById('filterMemberDay').value || '';
    const status = document.getElementById('filterMemberStatus') && document.getElementById('filterMemberStatus').value || '';
    const repeat = document.getElementById('filterMemberRepeat') && document.getElementById('filterMemberRepeat').value || '';
    const timezone = document.getElementById('filterMemberTimezone') && document.getElementById('filterMemberTimezone').value || '';

    filteredMemberSchedules = allMemberSchedules.filter(function (s) {
        const matchBranch = currentMemberScheduleBranch === 'all' || s.branchId == currentMemberScheduleBranch;
        const matchSearch = !search || (s.name || '').toLowerCase().includes(search) || (s.summary || '').toLowerCase().includes(search);
        const matchRole = !role || s.role === role;
        const matchDay = !day || s.day === day;
        const matchStatus = !status || s.status === status;
        const matchRepeat = !repeat || s.repeatPeriod === repeat;
        const matchTz = !timezone || s.timezone === timezone;
        return matchBranch && matchSearch && matchRole && matchDay && matchStatus && matchRepeat && matchTz;
    });

    memberSchedulesCurrentPage = 1;
    sortMemberScheduleItems();
    window.renderMemberSchedulesTable(filteredMemberSchedules);
};

window.renderMemberSchedulesTable = async function (list) {
    list = list || filteredMemberSchedules;
    const tbody = document.querySelector('#memberSchedulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / memberSchedulesPerPage) || 1;
    if (memberSchedulesCurrentPage > totalPages) memberSchedulesCurrentPage = totalPages;

    const start = (memberSchedulesCurrentPage - 1) * memberSchedulesPerPage;
    const end = start + memberSchedulesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getMemberScheduleEmptyRowHTML ? window.getMemberScheduleEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getMemberScheduleRowHTML ? window.getMemberScheduleRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingMemberScheduleRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getMemberScheduleInlineExpandRowHTML ? window.getMemberScheduleInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateMemberSchedulesPagination(list.length, start, end, totalPages);
    window.updateMemberScheduleSortIcons();
};

function updateMemberSchedulesPagination(total, start, end, totalPages) {
    const info = document.getElementById('memberSchedulesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' زمان‌بندی';
    }
    const pagination = document.getElementById('memberSchedulesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeMemberSchedulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (memberSchedulesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeMemberSchedulesPage(' + (memberSchedulesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (memberSchedulesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, memberSchedulesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeMemberSchedulesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === memberSchedulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeMemberSchedulesPage(' + (memberSchedulesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (memberSchedulesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeMemberSchedulesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (memberSchedulesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeMemberSchedulesPage = async function (page) {
    const totalPages = Math.ceil(filteredMemberSchedules.length / memberSchedulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    memberSchedulesCurrentPage = page;
    window.renderMemberSchedulesTable(filteredMemberSchedules);
};

function readSelectedSlots(prefix) {
    const containerId = prefix ? prefix + 'TimeSlots' : 'msTimeSlots';
    const container = document.getElementById(containerId);
    if (!container) return [];
    return Array.from(container.querySelectorAll('.ms-time-slot:checked')).map(function (cb) { return cb.value; });
}

function readMemberScheduleForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'ms' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = window.getMemberScheduleBranches().find(function (b) { return b.id === branchId; });
    const memberSel = f('Member');
    const memberId = memberSel && memberSel.value;
    const memberName = memberSel && memberSel.selectedOptions[0] && memberId && memberId !== '__new__'
        ? memberSel.selectedOptions[0].textContent : '';
    const slots = readSelectedSlots(prefix);
    const ranges = mergeConsecutiveSlots(slots);
    const repeatPeriod = f('Repeat') && f('Repeat').value || 'هفتگی';
    return {
        branchId: branchId, branchName: branch ? branch.name : 'نامشخص',
        memberId: memberId, name: memberName,
        role: f('Role') && f('Role').value || 'استاد',
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

window.openAddMemberScheduleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getMemberScheduleAddModalHTML
        ? window.getMemberScheduleAddModalHTML() : '';
};

window.saveMemberSchedule = async function () {
    const data = readMemberScheduleForm('');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    expandRangesToRows({
        memberId: data.memberId, name: data.name, role: data.role, day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allMemberSchedules.unshift(r); });
    window.filterMemberSchedules();
    closeModal();
    alert('✅ بازه(های) زمانی ثبت شد');
};

window.viewMemberSchedule = async function (id) {
    const item = allMemberSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getMemberScheduleDetailsModalHTML
        ? window.getMemberScheduleDetailsModalHTML(item) : '';
};

window.editMemberSchedule = async function (id) {
    const item = allMemberSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getMemberScheduleEditModalHTML
        ? window.getMemberScheduleEditModalHTML(item) : '';
};

window.saveEditedMemberSchedule = async function (id) {
    const data = readMemberScheduleForm('editMs');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allMemberSchedules = allMemberSchedules.filter(function (x) { return x.id !== id; });
    expandRangesToRows({
        memberId: data.memberId, name: data.name, role: data.role, day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allMemberSchedules.unshift(r); });
    editingMemberScheduleRowId = null;
    window.filterMemberSchedules();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleMemberScheduleInlineEdit = async function (id) {
    editingMemberScheduleRowId = editingMemberScheduleRowId === id ? null : id;
    window.renderMemberSchedulesTable(filteredMemberSchedules);
};

window.saveInlineMemberSchedule = async function (id) {
    const data = readMemberScheduleForm('inlineMs' + id);
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allMemberSchedules = allMemberSchedules.filter(function (x) { return x.id !== id; });
    expandRangesToRows({
        memberId: data.memberId, name: data.name, role: data.role, day: data.day,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        repeatPeriod: data.repeatPeriod, repeatDate: data.repeatDate, timezone: data.timezone,
        summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allMemberSchedules.unshift(r); });
    editingMemberScheduleRowId = null;
    window.filterMemberSchedules();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteMemberSchedule = async function (id) {
    if (!(await AppDialog.confirm('حذف این زمان‌بندی؟'))) return;
    try {
        const body=new FormData(); body.append('_token',window.adminCsrfToken||'');
        const response=await fetch('/analytics/member-schedules/'+id+'/delete',{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body});
        const envelope=await response.json(); const result=envelope.data||{}; if(!response.ok||result.success===false)throw new Error(result.message||'حذف ناموفق بود.');
        allMemberSchedules=allMemberSchedules.filter(s=>s.id!==id); if(editingMemberScheduleRowId===id)editingMemberScheduleRowId=null; window.filterMemberSchedules();
    } catch(error) { alert(error.message||'حذف برنامه زمانی ناموفق بود.'); }
};

window.exportMemberSchedulesToExcel = async function () {
    const data = filteredMemberSchedules.length ? filteredMemberSchedules : allMemberSchedules;
    let csv = '\uFEFFردیف,نام عضو,نقش,روز,ساعت,دوره تکرار,منطقه زمانی,شعبه,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.name + '","' + item.role + '","' + item.day + '","' +
            (item.timeLabel || item.time || '') + '","' + (item.repeatPeriod || '') + '","' + (item.timezone || '') + '","' +
            item.branchName + '","' + item.status + '","' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'زمانبندی_اعضا_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportMemberSchedulesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getMemberSchedulePDFModalHTML
        ? window.getMemberSchedulePDFModalHTML(msPdfColumns) : '';
};

window.generateMemberSchedulesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('msPdfTitle') && document.getElementById('msPdfTitle').value || 'گزارش زمان‌بندی اعضا';
    const subtitle = document.getElementById('msPdfSubtitle') && document.getElementById('msPdfSubtitle').value || '';
    const footer = document.getElementById('msPdfFooter') && document.getElementById('msPdfFooter').value || '';
    const format = document.getElementById('msPdfFormat') && document.getElementById('msPdfFormat').value || 'a4';
    const orientation = document.getElementById('msPdfOrientation') && document.getElementById('msPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('msPdfIncludeDate') && document.getElementById('msPdfIncludeDate').checked;
    const headerColor = document.getElementById('msPdfHeaderColor') && document.getElementById('msPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('msPdfEvenRowColor') && document.getElementById('msPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('msPdfOddRowColor') && document.getElementById('msPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = msPdfColumns.filter(function (c) {
        return document.getElementById('msPdfCol-' + c.field) && document.getElementById('msPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredMemberSchedules.length ? filteredMemberSchedules : allMemberSchedules;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getMemberSchedulePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('زمانبندی_اعضا_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('memberSchedulesTable')) {
        window.renderMemberSchedulesBranchTabs();
        window.filterMemberSchedules();
    }
}, 200);
})();
