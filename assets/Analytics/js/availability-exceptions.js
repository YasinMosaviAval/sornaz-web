(function () {
'use strict';
// ==================== تعطیلات و مرخصی‌ها (ایزوله) ====================
window.holidayLeaveStatusesList = ['فعال', 'غیرفعال', 'پر شده', 'در انتظار تأیید'];
window.holidayLeaveTimezoneList = [
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
window.holidayLeaveTypeList = [
    { value: 'leave', label: 'مرخصی' },
    { value: 'official-holiday', label: 'تعطیل رسمی' },
    { value: 'mission', label: 'ماموریت' }
];

const hlBranchWorkingHours = {
    1: { start: '08:00', end: '22:00' },
    2: { start: '09:00', end: '21:00' },
    3: { start: '08:30', end: '20:00' },
    4: { start: '09:00', end: '20:00' }
};

let holidayLeaveMembers = [
    { id: 1, name: 'استاد محمد موسوی' }, { id: 2, name: 'استاد علی رضایی' },
    { id: 3, name: 'زهرا کریمی' }, { id: 4, name: 'استاد بهرامی' },
    { id: 5, name: 'امیر نوری' }, { id: 6, name: 'استاد کاظمی' },
    { id: 7, name: 'نگار احمدی' }, { id: 8, name: 'استاد نوری' },
    { id: 9, name: 'پارسا جعفری' }, { id: 10, name: 'هستی محمدی' }
];

window.getHolidayLeaveBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
};

window.getHolidayLeaveMemberOptions = function () {
    if (typeof allStaff !== 'undefined' && allStaff.length) {
        return allStaff.map(function (s) { return { value: s.id, label: s.name, id: s.id, name: s.name }; });
    }
    return holidayLeaveMembers.map(function (m) { return { value: m.id, label: m.name, id: m.id, name: m.name }; });
};

function hlTimeToMinutes(t) {
    if (!t) return 0;
    const p = String(t).split(':');
    return parseInt(p[0], 10) * 60 + parseInt(p[1] || 0, 10);
}
function hlMinutesToTime(m) {
    const h = Math.floor(m / 60);
    const mm = m % 60;
    return String(h).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
}
function getHolidayLeaveBranchSlots(branchId) {
    const wh = hlBranchWorkingHours[branchId] || { start: '08:00', end: '22:00' };
    const start = hlTimeToMinutes(wh.start);
    const end = hlTimeToMinutes(wh.end);
    const slots = [];
    for (let m = start; m < end; m += 30) slots.push(hlMinutesToTime(m));
    return slots;
}
function hlMergeConsecutiveSlots(slots) {
    if (!slots || !slots.length) return [];
    const mins = slots.map(hlTimeToMinutes).sort(function (a, b) { return a - b; });
    const ranges = [];
    let rangeStart = mins[0], prev = mins[0];
    for (let i = 1; i < mins.length; i++) {
        if (mins[i] === prev + 30) prev = mins[i];
        else {
            ranges.push({ start: hlMinutesToTime(rangeStart), end: hlMinutesToTime(prev + 30) });
            rangeStart = mins[i]; prev = mins[i];
        }
    }
    ranges.push({ start: hlMinutesToTime(rangeStart), end: hlMinutesToTime(prev + 30) });
    return ranges;
}
function hlRangeLabel(range) { return range.start + '-' + range.end; }

window.buildHolidayLeaveTimeSlotsHTML = function (containerId, branchId, selectedSlots) {
    const slots = getHolidayLeaveBranchSlots(parseInt(branchId, 10) || 1);
    const selected = (selectedSlots || []).map(String);
    return '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">' +
        slots.map(function (s) {
            const checked = selected.indexOf(s) !== -1 ? 'checked' : '';
            return '<label class="inline-flex items-center gap-2 text-sm border border-gray-200 rounded-xl px-2 py-1.5 hover:bg-gray-50 cursor-pointer">' +
                '<input type="checkbox" class="hl-time-slot" value="' + s + '" ' + checked + '> ' + s +
                '</label>';
        }).join('') + '</div>';
};

window.refreshHolidayLeaveTimeSlots = function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildHolidayLeaveTimeSlotsHTML(containerId, branchId, selectedSlots || []);
};

window.promptAddHolidayLeaveMember = function (selectId) {
    const name = prompt('نام عضو جدید را وارد کنید:');
    const sel = document.getElementById(selectId);
    if (!name || !name.trim()) { if (sel) sel.value = ''; return; }
    const m = { id: Date.now(), name: name.trim() };
    holidayLeaveMembers.push(m);
    if (sel) {
        const opt = document.createElement('option');
        opt.value = m.id; opt.textContent = m.name;
        sel.insertBefore(opt, sel.lastElementChild);
        sel.value = m.id;
    }
};

let allHolidayLeaves = [];
(function buildSample() {
    const branches = window.getHolidayLeaveBranches();
    let id = 1;
    for (let i = 0; i < 100 && allHolidayLeaves.length < 100; i++) {
        const member = holidayLeaveMembers[Math.floor(Math.random() * holidayLeaveMembers.length)];
        const status = window.holidayLeaveStatusesList[Math.floor(Math.random() * window.holidayLeaveStatusesList.length)];
        const type = window.holidayLeaveTypeList[Math.floor(Math.random() * window.holidayLeaveTypeList.length)];
        const branch = branches[Math.floor(Math.random() * branches.length)];
        const allSlots = getHolidayLeaveBranchSlots(branch.id);
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
        const d = new Date();
        d.setDate(d.getDate() + Math.floor(Math.random() * 60) - 15);
        const dateStr = d.toISOString().split('T')[0];
        hlMergeConsecutiveSlots(picked).forEach(function (range) {
            const rangeSlots = [];
            for (let m = hlTimeToMinutes(range.start); m < hlTimeToMinutes(range.end); m += 30) rangeSlots.push(hlMinutesToTime(m));
            const tz = window.holidayLeaveTimezoneList[Math.floor(Math.random() * window.holidayLeaveTimezoneList.length)];
            allHolidayLeaves.push({
                id: id++, memberId: member.id, name: member.name, date: dateStr,
                slots: rangeSlots, timeLabel: hlRangeLabel(range), time: hlRangeLabel(range),
                branchId: branch.id, branchName: branch.name, status: status,
                type: type.value, typeLabel: type.label,
                timezone: tz.value,
                summary: 'مرخصی / تعطیل ' + member.name + ' در ' + dateStr,
                description: 'ثبت تعطیل یا مرخصی در ' + branch.name + ' — ' + hlRangeLabel(range)
            });
        });
    }
    allHolidayLeaves = allHolidayLeaves.slice(0, 100);
})();
if (Array.isArray(window.adminAvailabilityExceptionsData) && window.adminAvailabilityExceptionsData.length) allHolidayLeaves = window.adminAvailabilityExceptionsData;

let currentHolidayLeaveBranch = 'all';
let holidayLeavesCurrentPage = 1;
const holidayLeavesPerPage = 10;
let filteredHolidayLeaves = allHolidayLeaves.slice();
let editingHolidayLeaveRowId = null;
let hlSortField = '';
let hlSortDirection = 'asc';

const hlPdfColumns = [
    { field: 'index', label: 'ردیف' }, { field: 'name', label: 'نام عضو' },
    { field: 'date', label: 'تاریخ' }, { field: 'timeLabel', label: 'ساعت' },
    { field: 'typeLabel', label: 'نوع' }, { field: 'timezone', label: 'منطقه زمانی' },
    { field: 'branchName', label: 'شعبه' }, { field: 'status', label: 'وضعیت' }
];

function sortHolidayLeaveItems() {
    if (!hlSortField) return;
    filteredHolidayLeaves.sort(function (a, b) {
        let av = a[hlSortField], bv = b[hlSortField];
        if (hlSortField === 'timeLabel' || hlSortField === 'time') {
            av = (a.slots && a.slots[0]) || a.timeLabel || '';
            bv = (b.slots && b.slots[0]) || b.timeLabel || '';
        } else {
            av = String(av || '').toLowerCase();
            bv = String(bv || '').toLowerCase();
        }
        if (av < bv) return hlSortDirection === 'asc' ? -1 : 1;
        if (av > bv) return hlSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateHolidayLeaveSortIcons = function () {
    ['name', 'date', 'timeLabel', 'typeLabel', 'timezone', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('hlSortIcon-' + f);
        if (!icon) return;
        icon.textContent = hlSortField === f ? (hlSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortHolidayLeavesBy = function (field) {
    if (hlSortField === field) hlSortDirection = hlSortDirection === 'asc' ? 'desc' : 'asc';
    else { hlSortField = field; hlSortDirection = 'asc'; }
    sortHolidayLeaveItems();
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
    window.updateHolidayLeaveSortIcons();
};

window.renderHolidayLeavesBranchTabs = function () {
    const container = document.getElementById('holidayLeavesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.holiday-leave-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    window.getHolidayLeaveBranches().forEach(function (b) {
        const active = currentHolidayLeaveBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'holiday-leave-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.onclick = function () { window.filterHolidayLeavesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterHolidayLeavesByBranch = function (branchId) {
    currentHolidayLeaveBranch = branchId;
    document.querySelectorAll('.holiday-leave-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.holiday-leave-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = window.getHolidayLeaveBranches().find(function (b) { return b.id == branchId; });
        tabs.forEach(function (tab) {
            if (name && tab.textContent === name.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    window.filterHolidayLeaves();
};

window.filterHolidayLeaves = function () {
    const search = (document.getElementById('holidayLeaveSearch') && document.getElementById('holidayLeaveSearch').value || '').trim().toLowerCase();
    const date = document.getElementById('filterHolidayLeaveDate') && document.getElementById('filterHolidayLeaveDate').value || '';
    const status = document.getElementById('filterHolidayLeaveStatus') && document.getElementById('filterHolidayLeaveStatus').value || '';
    const timezone = document.getElementById('filterHolidayLeaveTimezone') && document.getElementById('filterHolidayLeaveTimezone').value || '';
    const type = document.getElementById('filterHolidayLeaveType') && document.getElementById('filterHolidayLeaveType').value || '';

    filteredHolidayLeaves = allHolidayLeaves.filter(function (s) {
        const matchBranch = currentHolidayLeaveBranch === 'all' || s.branchId == currentHolidayLeaveBranch;
        const matchSearch = !search || (s.name || '').toLowerCase().includes(search) || (s.summary || '').toLowerCase().includes(search);
        const matchDate = !date || s.date === date;
        const matchStatus = !status || s.status === status;
        const matchTz = !timezone || s.timezone === timezone;
        const matchType = !type || s.type === type;
        return matchBranch && matchSearch && matchDate && matchStatus && matchTz && matchType;
    });

    holidayLeavesCurrentPage = 1;
    sortHolidayLeaveItems();
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
};

window.renderHolidayLeavesTable = function (list) {
    list = list || filteredHolidayLeaves;
    const tbody = document.querySelector('#holidayLeavesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / holidayLeavesPerPage) || 1;
    if (holidayLeavesCurrentPage > totalPages) holidayLeavesCurrentPage = totalPages;

    const start = (holidayLeavesCurrentPage - 1) * holidayLeavesPerPage;
    const end = start + holidayLeavesPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';
    if (!pageItems.length) {
        tbody.innerHTML = window.getHolidayLeaveEmptyRowHTML ? window.getHolidayLeaveEmptyRowHTML() : '';
    } else {
        pageItems.forEach(function (item) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getHolidayLeaveRowHTML ? window.getHolidayLeaveRowHTML(item) : '';
            tbody.appendChild(tr);
            if (editingHolidayLeaveRowId === item.id) {
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50';
                expand.innerHTML = window.getHolidayLeaveInlineExpandRowHTML ? window.getHolidayLeaveInlineExpandRowHTML(item) : '';
                tbody.appendChild(expand);
            }
        });
    }
    updateHolidayLeavesPagination(list.length, start, end, totalPages);
    window.updateHolidayLeaveSortIcons();
};

function updateHolidayLeavesPagination(total, start, end, totalPages) {
    const info = document.getElementById('holidayLeavesPaginationInfo');
    if (info) {
        info.textContent = 'نمایش ' + (total === 0 ? 0 : start + 1) + ' تا ' + Math.min(end, total) + ' از ' + total + ' مورد';
    }
    const pagination = document.getElementById('holidayLeavesPaginationButtons');
    if (!pagination) return;
    let html = '<button onclick="changeHolidayLeavesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (holidayLeavesCurrentPage === 1 ? 'disabled' : '') + '>اول</button>'
        + '<button onclick="changeHolidayLeavesPage(' + (holidayLeavesCurrentPage - 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (holidayLeavesCurrentPage === 1 ? 'disabled' : '') + '>قبلی</button>';
    let sp = Math.max(1, holidayLeavesCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
    if (ep - sp < 4) sp = Math.max(1, ep - 4);
    for (let i = sp; i <= ep; i++) {
        html += '<button onclick="changeHolidayLeavesPage(' + i + ')" class="px-3 py-1.5 rounded-lg ' + (i === holidayLeavesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') + '">' + i + '</button>';
    }
    html += '<button onclick="changeHolidayLeavesPage(' + (holidayLeavesCurrentPage + 1) + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (holidayLeavesCurrentPage === totalPages ? 'disabled' : '') + '>بعدی</button>'
        + '<button onclick="changeHolidayLeavesPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' + (holidayLeavesCurrentPage === totalPages ? 'disabled' : '') + '>آخر</button>';
    pagination.innerHTML = html;
}

window.changeHolidayLeavesPage = function (page) {
    const totalPages = Math.ceil(filteredHolidayLeaves.length / holidayLeavesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    holidayLeavesCurrentPage = page;
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
};

function hlReadSelectedSlots(prefix) {
    const containerId = prefix ? prefix + 'TimeSlots' : 'hlTimeSlots';
    const container = document.getElementById(containerId);
    if (!container) return [];
    return Array.from(container.querySelectorAll('.hl-time-slot:checked')).map(function (cb) { return cb.value; });
}

function readHolidayLeaveForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'hl' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value, 10);
    const branch = window.getHolidayLeaveBranches().find(function (b) { return b.id === branchId; });
    const memberSel = f('Member');
    const memberId = memberSel && memberSel.value;
    const memberName = memberSel && memberSel.selectedOptions[0] && memberId && memberId !== '__new__'
        ? memberSel.selectedOptions[0].textContent : '';
    const slots = hlReadSelectedSlots(prefix);
    const ranges = hlMergeConsecutiveSlots(slots);
    return {
        branchId: branchId, branchName: branch ? branch.name : 'نامشخص',
        memberId: memberId, name: memberName,
        date: f('Date') && f('Date').value || '',
        status: f('Status') && f('Status').value || 'فعال',
        type: f('Type') && f('Type').value || 'leave',
        typeLabel: (window.holidayLeaveTypeList || []).find(function (item) { return item.value === (f('Type') && f('Type').value || 'leave'); })?.label || 'مرخصی',
        timezone: f('Timezone') && f('Timezone').value || 'Asia/Tehran',
        summary: f('Summary') && f('Summary').value.trim() || '',
        description: f('Description') && f('Description').value.trim() || '',
        slots: slots, ranges: ranges
    };
}

function hlExpandRangesToRows(base, ranges) {
    return ranges.map(function (range, idx) {
        const rangeSlots = [];
        for (let m = hlTimeToMinutes(range.start); m < hlTimeToMinutes(range.end); m += 30) rangeSlots.push(hlMinutesToTime(m));
        return Object.assign({}, base, {
            id: Date.now() + idx, slots: rangeSlots,
            timeLabel: hlRangeLabel(range), time: hlRangeLabel(range)
        });
    });
}

window.openAddHolidayLeaveModal = function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveAddModalHTML
        ? window.getHolidayLeaveAddModalHTML() : '';
};

window.saveHolidayLeave = function () {
    const data = readHolidayLeaveForm('');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    hlExpandRangesToRows({
        memberId: data.memberId, name: data.name, date: data.date,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        timezone: data.timezone, summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allHolidayLeaves.unshift(r); });
    window.filterHolidayLeaves();
    closeModal();
    alert('✅ ثبت شد');
};

window.viewHolidayLeave = function (id) {
    const item = allHolidayLeaves.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveDetailsModalHTML
        ? window.getHolidayLeaveDetailsModalHTML(item) : '';
};

window.editHolidayLeave = function (id) {
    const item = allHolidayLeaves.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveEditModalHTML
        ? window.getHolidayLeaveEditModalHTML(item) : '';
};

window.saveEditedHolidayLeave = function (id) {
    const data = readHolidayLeaveForm('editHl');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allHolidayLeaves = allHolidayLeaves.filter(function (x) { return x.id !== id; });
    hlExpandRangesToRows({
        memberId: data.memberId, name: data.name, date: data.date,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        timezone: data.timezone, summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allHolidayLeaves.unshift(r); });
    editingHolidayLeaveRowId = null;
    window.filterHolidayLeaves();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleHolidayLeaveInlineEdit = function (id) {
    editingHolidayLeaveRowId = editingHolidayLeaveRowId === id ? null : id;
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
};

window.saveInlineHolidayLeave = function (id) {
    const data = readHolidayLeaveForm('inlineHl' + id);
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    allHolidayLeaves = allHolidayLeaves.filter(function (x) { return x.id !== id; });
    hlExpandRangesToRows({
        memberId: data.memberId, name: data.name, date: data.date,
        branchId: data.branchId, branchName: data.branchName, status: data.status,
        timezone: data.timezone, summary: data.summary, description: data.description
    }, data.ranges).forEach(function (r) { allHolidayLeaves.unshift(r); });
    editingHolidayLeaveRowId = null;
    window.filterHolidayLeaves();
    alert('✅ تغییرات ذخیره شد');
};

window.deleteHolidayLeave = function (id) {
    if (!confirm('حذف این مورد؟')) return;
    allHolidayLeaves = allHolidayLeaves.filter(function (s) { return s.id !== id; });
    if (editingHolidayLeaveRowId === id) editingHolidayLeaveRowId = null;
    window.filterHolidayLeaves();
};

window.exportHolidayLeavesToExcel = function () {
    const data = filteredHolidayLeaves.length ? filteredHolidayLeaves : allHolidayLeaves;
    let csv = '\uFEFFردیف,نام عضو,تاریخ,ساعت,منطقه زمانی,شعبه,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.name + '","' + (item.date || '') + '","' +
            (item.timeLabel || item.time || '') + '","' + (item.timezone || '') + '","' +
            item.branchName + '","' + item.status + '","' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'تعطیلات_مرخصی_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportHolidayLeavesToPDF = function () {
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeavePDFModalHTML
        ? window.getHolidayLeavePDFModalHTML(hlPdfColumns) : '';
};

window.generateHolidayLeavesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('hlPdfTitle') && document.getElementById('hlPdfTitle').value || 'گزارش تعطیلات و مرخصی‌ها';
    const subtitle = document.getElementById('hlPdfSubtitle') && document.getElementById('hlPdfSubtitle').value || '';
    const footer = document.getElementById('hlPdfFooter') && document.getElementById('hlPdfFooter').value || '';
    const format = document.getElementById('hlPdfFormat') && document.getElementById('hlPdfFormat').value || 'a4';
    const orientation = document.getElementById('hlPdfOrientation') && document.getElementById('hlPdfOrientation').value || 'landscape';
    const includeDate = document.getElementById('hlPdfIncludeDate') && document.getElementById('hlPdfIncludeDate').checked;
    const headerColor = document.getElementById('hlPdfHeaderColor') && document.getElementById('hlPdfHeaderColor').value || '#eff6ff';
    const evenRowColor = document.getElementById('hlPdfEvenRowColor') && document.getElementById('hlPdfEvenRowColor').value || '#ffffff';
    const oddRowColor = document.getElementById('hlPdfOddRowColor') && document.getElementById('hlPdfOddRowColor').value || '#f8fafc';
    const selectedColumns = hlPdfColumns.filter(function (c) {
        return document.getElementById('hlPdfCol-' + c.field) && document.getElementById('hlPdfCol-' + c.field).checked;
    });
    if (!selectedColumns.length) return alert('حداقل یک ستون انتخاب کنید.');
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredHolidayLeaves.length ? filteredHolidayLeaves : allHolidayLeaves;
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];
    for (let p = 0; p < totalPages; p++) {
        const pageRows = data.slice(p * rowsPerPage, (p + 1) * rowsPerPage);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'direction:rtl;position:fixed;top:-9999px;left:-9999px;width:' + (orientation === 'portrait' ? '900' : '1400') + 'px;padding:30px;background:#fff;font-family:Vazirmatn,Tahoma,sans-serif;';
        wrap.innerHTML = window.getHolidayLeavePDFPageHTML(p + 1, pageRows, p === 0, {
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
    doc.save('تعطیلات_مرخصی_' + date + '.pdf');
    closeModal();
};

setTimeout(function () {
    if (document.getElementById('holidayLeavesTable')) {
        window.renderHolidayLeavesBranchTabs();
        window.filterHolidayLeaves();
    }
}, 200);
})();
