(function () {
'use strict';
// ==================== برنامه زمانی سازمان (ایزوله) ====================
window.branchScheduleDaysList = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
window.branchScheduleStatusesList = ['فعال', 'غیرفعال'];
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

window.getBranchScheduleBranches = function () {
    return Array.isArray(window.branchOfferingBranches) ? window.branchOfferingBranches : [];
};

window.getBranchScheduleOrganizations = function () {
    const data=window.branchOfferingData||{};
    const organizations = Array.isArray(data.organizations)&&data.organizations.length ? data.organizations : (window.branchScheduleOrganizations||[]);
    const derived=[];
    (data.branches||window.branchOfferingBranches||[]).forEach(function(branch){
        if(branch.academy_user_id)derived.push({id:branch.academy_id,user_id:branch.academy_user_id,kind:'academy',name:branch.academy_name});
        derived.push({id:branch.id,user_id:branch.user_id,kind:'branch',name:branch.name});
    });
    const source=(organizations||[]).concat(derived).filter(function(o,index,list){return o.user_id&&list.findIndex(function(x){return String(x.user_id)===String(o.user_id);})===index;});
    return source.map(function (o) {
        return { id: o.user_id, user_id: o.user_id, organizationId: o.id, kind: o.kind, name: o.name };
    });
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

function timezoneOffsetMinutes(timezone, date) {
    const parts = new Intl.DateTimeFormat('en-US', {
        timeZone: timezone, year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23'
    }).formatToParts(date).reduce(function (result, part) {
        if (part.type !== 'literal') result[part.type] = parseInt(part.value, 10);
        return result;
    }, {});
    return Math.round((Date.UTC(parts.year, parts.month - 1, parts.day, parts.hour, parts.minute, parts.second) - date.getTime()) / 60000);
}

function shiftBranchScheduleRanges(ranges, shift) {
    const shifted = [];
    ranges.forEach(function (range) {
        const duration = timeToMinutes(range.end) - timeToMinutes(range.start);
        if (duration >= 1440) {
            shifted.push({start: '00:00', end: '24:00', status: range.status});
            return;
        }
        let start = (timeToMinutes(range.start) + shift) % 1440;
        if (start < 0) start += 1440;
        const end = start + duration;
        if (end <= 1440) {
            shifted.push({start: minutesToTime(start), end: minutesToTime(end), status: range.status});
        } else {
            shifted.push({start: minutesToTime(start), end: '24:00', status: range.status});
            shifted.push({start: '00:00', end: minutesToTime(end - 1440), status: range.status});
        }
    });
    return shifted.filter(function (range) { return timeToMinutes(range.end) > timeToMinutes(range.start); })
        .sort(function (a, b) { return timeToMinutes(a.start) - timeToMinutes(b.start); });
}

window.changeBranchScheduleTimezone = function (select, prefix) {
    const previous = select.dataset.previousTimezone || select.value;
    const current = select.value;
    if (previous === current) return;
    const container = document.getElementById(prefix ? prefix + 'TimeSlots' : 'bsTimeSlots');
    const rangesBox = container && container.querySelector('.bs-ranges');
    const ranges = rangesBox ? Array.from(rangesBox.querySelectorAll('.bs-range')).map(function (row) {
        return {
            start: row.querySelector('.bs-range-start').value,
            end: row.querySelector('.bs-range-end').value,
            status: row.querySelector('.bs-range-status').value
        };
    }).filter(function (range) { return range.start && range.end; }) : [];
    try {
        const reference = new Date();
        reference.setUTCSeconds(0, 0);
        const shift = timezoneOffsetMinutes(current, reference) - timezoneOffsetMinutes(previous, reference);
        if (ranges.length && shift) {
            const converted = shiftBranchScheduleRanges(ranges, shift);
            container.innerHTML = window.buildBranchScheduleTimeSlotsHTML(container.id, '', [], null, converted);
            window.refreshBranchScheduleRanges(container.querySelector('.bs-ranges'));
        }
        select.dataset.previousTimezone = current;
    } catch (error) {
        select.value = previous;
        alert('تبدیل ساعت‌ها برای منطقه زمانی انتخاب‌شده امکان‌پذیر نیست.');
    }
};

window.buildBranchScheduleTimeSlotsHTML = function (containerId, branchId, selectedSlots, rangeStatuses, explicitRanges) {
    const ranges = explicitRanges && explicitRanges.length ? explicitRanges.map(function(r){return Object.assign({},r);}) : mergeConsecutiveSlots(selectedSlots || []);
    (rangeStatuses || []).forEach(function(status,index){if(ranges[index])ranges[index].status=status;});
    return '<div class="bs-ranges space-y-3">' + (ranges.length ? ranges : [{start:'',end:'',status:'فعال'}]).map(renderRangeEditor).join('') + '</div>' +
        '<button type="button" onclick="addBranchScheduleRange(this)" class="mt-3 text-sm text-indigo-600 hover:text-indigo-800"><i class="fas fa-plus ml-1"></i>افزودن بازه زمانی جدید</button>';
};

function renderRangeEditor(range) {
    return '<div class="bs-range grid grid-cols-1 sm:grid-cols-[1fr_1fr_1fr_auto] gap-3 items-end rounded-2xl border border-gray-200 bg-white p-4">' +
        '<label class="text-sm">ساعت شروع<select class="bs-range-start mt-2 w-full border rounded-xl py-3 px-3" onchange="refreshBranchScheduleRanges(this.closest(\'.bs-ranges\'))"></select></label>' +
        '<label class="text-sm">ساعت پایان<select class="bs-range-end mt-2 w-full border rounded-xl py-3 px-3 disabled:bg-gray-100" data-value="' + (range.end || '') + '" onchange="refreshBranchScheduleRanges(this.closest(\'.bs-ranges\'))"></select></label>' +
        '<label class="text-sm">وضعیت<select class="bs-range-status mt-2 w-full border rounded-xl py-3 px-3"><option '+(range.status!=='غیرفعال'?'selected':'')+'>فعال</option><option '+(range.status==='غیرفعال'?'selected':'')+'>غیرفعال</option></select></label>' +
        '<button type="button" title="حذف بازه" onclick="removeBranchScheduleRange(this)" class="bs-range-remove mb-2 text-red-500 px-2">×</button>' +
        '<input type="hidden" class="bs-range-initial-start" value="' + (range.start || '') + '"></div>';
}
window.addBranchScheduleRange = function (button) { const box=button.previousElementSibling; box.insertAdjacentHTML('beforeend',renderRangeEditor({})); refreshBranchScheduleRanges(box); };
window.removeBranchScheduleRange = function (button) { const box=button.closest('.bs-ranges'); button.closest('.bs-range').remove(); if(!box.children.length)box.insertAdjacentHTML('beforeend',renderRangeEditor({})); refreshBranchScheduleRanges(box); };
window.refreshBranchScheduleRanges = function (box) {
    if(!box)return; const slots=getFullDaySlots(), rows=Array.from(box.querySelectorAll('.bs-range'));
    const chosen=rows.map(function(row){return {row:row,start:row.querySelector('.bs-range-start').value||row.querySelector('.bs-range-initial-start').value,end:row.querySelector('.bs-range-end').value||row.querySelector('.bs-range-end').dataset.value};});
    rows.forEach(function(row,index){
        row.querySelector('.bs-range-remove').classList.toggle('hidden',index===0);
        const startEl=row.querySelector('.bs-range-start'), endEl=row.querySelector('.bs-range-end'), current=chosen[index];
        const others=chosen.filter(function(_,i){return i!==index&&chosen[i].start&&chosen[i].end;}).sort(function(a,b){return timeToMinutes(a.start)-timeToMinutes(b.start);});
        const starts=slots.filter(function(t){const m=timeToMinutes(t);return !others.some(function(r){return m>=timeToMinutes(r.start)-30&&m<=timeToMinutes(r.end);});});
        startEl.innerHTML='<option value="">انتخاب ساعت شروع</option>'+starts.map(function(t){return '<option value="'+t+'">'+t+'</option>';}).join('');
        if(starts.indexOf(current.start)!==-1)startEl.value=current.start;
        const selectedStart=startEl.value, sm=timeToMinutes(selectedStart), next=others.find(function(r){return timeToMinutes(r.start)>sm;}), limit=next?timeToMinutes(next.start)-30:1440;
        const ends=[]; for(let m=sm+30;m<=limit;m+=30)ends.push(minutesToTime(m));
        endEl.disabled=!selectedStart; endEl.innerHTML='<option value="">انتخاب ساعت پایان</option>'+ends.map(function(t){return '<option value="'+t+'">'+t+'</option>';}).join('');
        if(ends.indexOf(current.end)!==-1)endEl.value=current.end;
        endEl.dataset.value=endEl.value; row.querySelector('.bs-range-initial-start').value=startEl.value;
    });
};

window.refreshBranchScheduleTimeSlots = async function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildBranchScheduleTimeSlotsHTML(containerId, branchId, selectedSlots || []);
};

let allBranchSchedules = [];
(function buildSample() { return;
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
let branchScheduleRealtimeVersion = null;
let branchScheduleRealtimeBusy = false;
const branchScheduleRealtimeChannel = 'BroadcastChannel' in window ? new BroadcastChannel('sornaz-admin-data') : null;

function matchesBranchScheduleOrganization(item, filter) {
    if (filter === 'all' || filter === '' || filter == null) return true;
    if (filter === 'academy') return item.organizationKind === 'academy';
    return item.organizationKind === 'branch' && String(item.branchId) === String(filter);
}

function populateBranchScheduleDisplayTimezones() {
    const select = document.getElementById('displayBranchTimezone');
    if (!select) return;
    const selected = select.value;
    select.innerHTML = '<option value="">نمایش پیش‌فرض مناطق زمانی</option>' + (window.branchScheduleTimezoneList || []).map(function (timezone) {
        return '<option value="' + timezone.value + '">' + timezone.label + '</option>';
    }).join('');
    select.value = selected;
}

function branchScheduleDayDate(day) {
    const jsDays = {'یکشنبه':0,'دوشنبه':1,'سه‌شنبه':2,'چهارشنبه':3,'پنجشنبه':4,'جمعه':5,'شنبه':6};
    const date = new Date();
    date.setHours(12, 0, 0, 0);
    date.setDate(date.getDate() + ((jsDays[day] - date.getDay() + 7) % 7));
    return {year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate()};
}

function branchScheduleLocalToUtc(date, minutes, timezone) {
    const dayShift = Math.floor(minutes / 1440), normalized = minutes % 1440;
    const wall = Date.UTC(date.year, date.month - 1, date.day + dayShift, Math.floor(normalized / 60), normalized % 60);
    let instant = new Date(wall);
    instant = new Date(wall - timezoneOffsetMinutes(timezone, instant) * 60000);
    return new Date(wall - timezoneOffsetMinutes(timezone, instant) * 60000);
}

function branchScheduleZonedParts(date, timezone) {
    return new Intl.DateTimeFormat('en-US', {
        timeZone: timezone, year: 'numeric', month: '2-digit', day: '2-digit', weekday: 'short',
        hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
    }).formatToParts(date).reduce(function (result, part) {
        if (part.type !== 'literal') result[part.type] = part.value;
        return result;
    }, {});
}

function convertBranchScheduleForDisplay(item, timezone) {
    if (!timezone) return [Object.assign({}, item)];
    if (item.timezone === timezone) return [Object.assign({}, item, {displaySourceIds:[item.id],displaySegment:0})];
    const parts = String(item.timeLabel || item.time || '').split('-');
    if (!parts[0] || !parts[1]) return [Object.assign({}, item, {timezone: timezone})];
    const sourceDate = branchScheduleDayDate(item.day);
    const startInstant = branchScheduleLocalToUtc(sourceDate, timeToMinutes(parts[0]), item.timezone || 'Asia/Tehran');
    const endInstant = branchScheduleLocalToUtc(sourceDate, timeToMinutes(parts[1]), item.timezone || 'Asia/Tehran');
    const start = branchScheduleZonedParts(startInstant, timezone), end = branchScheduleZonedParts(endInstant, timezone);
    const dayNames = {Sat:'شنبه',Sun:'یکشنبه',Mon:'دوشنبه',Tue:'سه‌شنبه',Wed:'چهارشنبه',Thu:'پنجشنبه',Fri:'جمعه'};
    const startMinutes = Number(start.hour) * 60 + Number(start.minute), endMinutes = Number(end.hour) * 60 + Number(end.minute);
    const sameDate = start.year === end.year && start.month === end.month && start.day === end.day;
    const ranges = sameDate && endMinutes > startMinutes
        ? [{day:dayNames[start.weekday],start:startMinutes,end:endMinutes}]
        : [{day:dayNames[start.weekday],start:startMinutes,end:1440},{day:dayNames[end.weekday],start:0,end:endMinutes}];
    return ranges.filter(function (range) { return range.end > range.start; }).map(function (range, index) {
        const startTime = minutesToTime(range.start), endTime = minutesToTime(range.end), slots = [];
        for (let minute = range.start; minute < range.end; minute += 30) slots.push(minutesToTime(minute));
        return Object.assign({}, item, {
            day: range.day, timezone: timezone, timeLabel: startTime + '-' + endTime, time: startTime + '-' + endTime,
            slots: slots, displaySourceIds: [item.id], displaySegment: index
        });
    });
}

function mergeDisplayedBranchSchedules(items) {
    const merged = [];
    items.slice().sort(function (a, b) {
        return String(a.branchName).localeCompare(String(b.branchName)) || window.branchScheduleDaysList.indexOf(a.day) - window.branchScheduleDaysList.indexOf(b.day) || timeToMinutes(a.timeLabel.split('-')[0]) - timeToMinutes(b.timeLabel.split('-')[0]);
    }).forEach(function (item) {
        const previous = merged[merged.length - 1], parts = item.timeLabel.split('-');
        const same = previous && String(previous.user_id || previous.organizationUserId) === String(item.user_id || item.organizationUserId) && previous.day === item.day && previous.timezone === item.timezone &&
            previous.status === item.status && previous.repeatPeriod === item.repeatPeriod;
        const previousParts = previous ? previous.timeLabel.split('-') : [];
        if (same && timeToMinutes(parts[0]) <= timeToMinutes(previousParts[1])) {
            previousParts[1] = minutesToTime(Math.max(timeToMinutes(previousParts[1]), timeToMinutes(parts[1])));
            previous.timeLabel = previous.time = previousParts.join('-');
            previous.displaySourceIds = (previous.displaySourceIds || [previous.id]).concat(item.displaySourceIds || [item.id]);
        } else merged.push(item);
    });
    return merged;
}

function withOrganizationDayRanges(item) {
    const organizationId=item.user_id||item.organizationUserId||item.branchId;
    const peers=allBranchSchedules.filter(function(x){return String(x.user_id||x.organizationUserId||x.branchId)===String(organizationId)&&x.day===item.day;});
    const sorted=peers.slice().sort(function(a,b){return timeToMinutes((a.slots||[])[0])-timeToMinutes((b.slots||[])[0]);});
    const ranges=sorted.map(function(x){const parts=String(x.timeLabel||x.time||'').split('-');return {start:parts[0],end:parts[1],status:x.status||'فعال'};});
    return Object.assign({},item,{slots:sorted.flatMap(function(x){return x.slots||[];}),rangeStatuses:ranges.map(function(x){return x.status;}),ranges:ranges});
}

const bsPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'branchName', label: 'سازمان' },
    { field: 'day', label: 'روز' },
    { field: 'timeLabel', label: 'ساعت' },
    { field: 'repeatPeriod', label: 'دوره تکرار' },
    { field: 'timezone', label: 'منطقه زمانی' },
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
        btn.dataset.value = String(b.id);
        btn.onclick = function () { window.filterBranchSchedulesByBranch(b.id); };
        container.appendChild(btn);
    });
    if(typeof window.applyAcademyOrganizationTabs==='function')window.applyAcademyOrganizationTabs();
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
    const displayTimezone = document.getElementById('displayBranchTimezone') && document.getElementById('displayBranchTimezone').value || '';

    const sourceRows = allBranchSchedules.filter(function (s) {
        const matchBranch = matchesBranchScheduleOrganization(s,currentBranchScheduleBranch);
        const matchStatus = !status || s.status === status;
        const matchRepeat = !repeat || s.repeatPeriod === repeat;
        const matchTz = !timezone || s.timezone === timezone;
        return matchBranch && matchStatus && matchRepeat && matchTz;
    });
    const displayRows = displayTimezone ? mergeDisplayedBranchSchedules(sourceRows.flatMap(function (item) {
        return convertBranchScheduleForDisplay(item, displayTimezone);
    })) : sourceRows;
    filteredBranchSchedules = displayRows.filter(function (item) { return !day || item.day === day; });

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
                const editItem = item.displaySourceIds ? allBranchSchedules.find(function (source) { return source.id === item.displaySourceIds[0]; }) || item : item;
                const expand = document.createElement('tr');
                expand.className = 'bg-gray-50 admin-inline-expand';
                expand.innerHTML = window.getBranchScheduleInlineExpandRowHTML ? window.getBranchScheduleInlineExpandRowHTML(withOrganizationDayRanges(editItem)) : '';
                tbody.appendChild(expand);
                setTimeout(function(){const box=document.querySelector('#inlineBs'+item.id+'TimeSlots .bs-ranges');if(box)window.refreshBranchScheduleRanges(box);},0);
            }
        });
    }
    updateBranchSchedulesPagination(list.length, start, end, totalPages);
    window.updateBranchScheduleSortIcons();
};

function updateBranchSchedulesPagination(total, start, end, totalPages) {
    const wrapper = document.getElementById('branchSchedulesPagination');
    if (wrapper) wrapper.classList.toggle('hidden', total < 11);
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

function readRanges(prefix) {
    const container=document.getElementById(prefix ? prefix+'TimeSlots' : 'bsTimeSlots');
    return container ? Array.from(container.querySelectorAll('.bs-range')).map(function(row){return {start:row.querySelector('.bs-range-start').value,end:row.querySelector('.bs-range-end').value,status:row.querySelector('.bs-range-status').value};}).filter(function(r){return r.start&&r.end;}).sort(function(a,b){return timeToMinutes(a.start)-timeToMinutes(b.start);}) : [];
}

function readBranchScheduleForm(prefix, fallback) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'bs' + s); };
    fallback = fallback || {};
    const branchId = parseInt(f('Branch') ? f('Branch').value : (fallback.user_id || fallback.organizationUserId || fallback.branchId), 10);
    const branch = window.getBranchScheduleOrganizations().find(function (b) { return b.id === branchId; });
    const ranges = readRanges(prefix);
    const slots = ranges.flatMap(function(range){const a=[];for(let m=timeToMinutes(range.start);m<timeToMinutes(range.end);m+=30)a.push(minutesToTime(m));return a;});
    const repeatPeriod = 'هفتگی';
    return {
        branchId: branchId, organizationUserId: branchId, branchName: branch ? branch.name : 'نامشخص',
        day: f('Day') ? f('Day').value : (fallback.day || ''),
        status: f('Status') && f('Status').value || 'فعال',
        repeatPeriod: repeatPeriod,
        repeatDate: '',
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

function encodeBranchSchedulePayload(data) {
    const bytes = new TextEncoder().encode(JSON.stringify(data));
    let binary = '';
    bytes.forEach(function (byte) { binary += String.fromCharCode(byte); });
    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

async function saveBranchScheduleRequest(data, id) {
    const token = window.adminCsrfToken || '';
    const url = id
        ? '/academy/admin/branch-offerings/schedules/' + id + '/update'
        : '/academy/admin/branch-offerings/schedules';
    const response = await fetch(url, {
        method: 'POST', credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': token
        },
        body: new URLSearchParams({_token: token, payload_b64: encodeBranchSchedulePayload(data)}).toString()
    });
    const raw = await response.text();
    let payload;
    try { payload = JSON.parse(raw); } catch (error) { throw new Error('پاسخ معتبر JSON از سرور دریافت نشد.'); }
    const envelope = payload.data ?? payload;
    if (!response.ok || envelope.success === false) throw new Error(envelope.message || 'ذخیره برنامه زمانی ناموفق بود.');
    window.branchOfferingData = null;
    await window.loadBranchOfferings();
    return envelope.data ?? envelope;
}

async function ensureBranchScheduleCatalog() {
    let data=window.branchOfferingData;
    if(!data&&typeof window.loadBranchOfferings==='function')data=await window.loadBranchOfferings();
    if(!data)return null;
    window.branchOfferingData=data;
    window.branchOfferingBranches=data.branches||window.branchOfferingBranches||[];
    window.branchScheduleOrganizations=data.organizations||window.branchScheduleOrganizations||[];
    window.branchScheduleOrganizationSelection=data.organization_selection||window.branchScheduleOrganizationSelection||'select';
    if(Array.isArray(data.timezones)&&data.timezones.length)window.branchScheduleTimezoneList=data.timezones;
    if(Array.isArray(data.schedules)){allBranchSchedules=data.schedules;filteredBranchSchedules=allBranchSchedules.slice();}
    return data;
}

async function pollBranchSchedulesRealtime() {
    if(branchScheduleRealtimeBusy||document.hidden||!document.getElementById('branchSchedulesTable'))return;
    branchScheduleRealtimeBusy=true;
    try {
        const response=await fetch('/academy/admin/branch-offerings/schedules/realtime-version',{credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
        const payload=await response.json(),envelope=payload.data??payload,state=envelope.data??envelope;
        if(!response.ok||envelope.success===false)return;
        if(branchScheduleRealtimeVersion===null){branchScheduleRealtimeVersion=state.version;return;}
        if(state.version!==branchScheduleRealtimeVersion){branchScheduleRealtimeVersion=state.version;editingBranchScheduleRowId=null;window.branchOfferingData=null;await window.loadBranchOfferings();branchScheduleRealtimeChannel?.postMessage({resource:'organization_schedules',version:state.version});window.dispatchEvent(new CustomEvent('sornaz:data-changed',{detail:{resource:'organization_schedules'}}));}
    } catch(error) {} finally { branchScheduleRealtimeBusy=false; }
}

branchScheduleRealtimeChannel?.addEventListener('message',async function(event){
    if(event.data?.resource!=='organization_schedules'||event.data.version===branchScheduleRealtimeVersion)return;
    branchScheduleRealtimeVersion=event.data.version;editingBranchScheduleRowId=null;window.branchOfferingData=null;await window.loadBranchOfferings();
});

window.openAddBranchScheduleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    try { await ensureBranchScheduleCatalog(); } catch(error) { return alert(error.message||'بارگذاری سازمان‌ها ناموفق بود.'); }
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleAddModalHTML
        ? window.getBranchScheduleAddModalHTML() : '';
    setTimeout(function(){const box=document.querySelector('#bsTimeSlots .bs-ranges');if(box)window.refreshBranchScheduleRanges(box);window.loadExistingBranchScheduleDay('',true);},0);
};

window.loadExistingBranchScheduleDay = function(prefix, force) {
    const f=function(n){return document.getElementById(prefix?prefix+n:'bs'+n);}, org=f('Branch'),day=f('Day'),box=f('TimeSlots'); if(!org||!day||!box)return;
    const rows=allBranchSchedules.filter(function(x){return String(x.user_id||x.organizationUserId||x.branchId)===String(org.value)&&x.day===day.value;});
    if(!rows.length&&!force){box.innerHTML=window.buildBranchScheduleTimeSlotsHTML(box.id,org.value,[]);window.refreshBranchScheduleRanges(box.querySelector('.bs-ranges'));return;}
    const sorted=rows.slice().sort(function(a,b){return timeToMinutes((a.slots||[])[0])-timeToMinutes((b.slots||[])[0]);});
    const ranges=sorted.map(function(x){const parts=String(x.timeLabel||x.time||'').split('-');return {start:parts[0],end:parts[1],status:x.status||'فعال'};});
    const slots=sorted.flatMap(function(x){return x.slots||[];}); box.innerHTML=window.buildBranchScheduleTimeSlotsHTML(box.id,org.value,slots,null,ranges); window.refreshBranchScheduleRanges(box.querySelector('.bs-ranges'));
};

window.cycleBranchScheduleStatus = async function(id) {
    const item=allBranchSchedules.find(function(x){return x.id===id;}); if(!item)return;
    const grouped=withOrganizationDayRanges(item), ranges=grouped.ranges;
    const target=String(item.timeLabel||item.time||'').split('-');
    ranges.forEach(function(range){if(range.start===target[0]&&range.end===target[1])range.status=item.status==='فعال'?'غیرفعال':'فعال';});
    try { await saveBranchScheduleRequest(Object.assign({},grouped,{organizationUserId:grouped.user_id||grouped.organizationUserId,ranges:ranges,repeatPeriod:'هفتگی'}),id); } catch(error){alert(error.message);}
};

document.addEventListener('click',function(event){if(editingBranchScheduleRowId&&!event.target.closest('.bs-inline-editor')&&!event.target.closest('[onclick^="toggleBranchScheduleInlineEdit"]')){editingBranchScheduleRowId=null;window.renderBranchSchedulesTable(filteredBranchSchedules);}},true);

window.saveBranchSchedule = async function () {
    const data = readBranchScheduleForm('');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    try {
        await saveBranchScheduleRequest(data, 0);
        closeModal();
        alert('✅ بازه(های) زمانی ثبت شد');
    } catch (error) { alert(error.message); }
};

window.viewBranchSchedule = async function (id) {
    const item = allBranchSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleDetailsModalHTML
        ? window.getBranchScheduleDetailsModalHTML(item) : '';
};

window.editBranchSchedule = async function (id) {
    try { await ensureBranchScheduleCatalog(); } catch(error) { return alert(error.message||'بارگذاری سازمان‌ها ناموفق بود.'); }
    const item = allBranchSchedules.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getBranchScheduleEditModalHTML
        ? window.getBranchScheduleEditModalHTML(withOrganizationDayRanges(item)) : '';
    setTimeout(function(){const box=document.querySelector('#editBsTimeSlots .bs-ranges');if(box)window.refreshBranchScheduleRanges(box);},0);
};

window.saveEditedBranchSchedule = async function (id) {
    const data = readBranchScheduleForm('editBs');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    try {
        await saveBranchScheduleRequest(data, id);
        editingBranchScheduleRowId = null;
        closeModal();
        alert('✅ تغییرات ذخیره شد');
    } catch (error) { alert(error.message); }
};

window.toggleBranchScheduleInlineEdit = async function (id) {
    editingBranchScheduleRowId = editingBranchScheduleRowId === id ? null : id;
    window.renderBranchSchedulesTable(filteredBranchSchedules);
};

window.saveInlineBranchSchedule = async function (id) {
    const item = allBranchSchedules.find(function (row) { return row.id === id; });
    if (!item) return alert('برنامه زمانی مورد نظر پیدا نشد');
    const data = readBranchScheduleForm('inlineBs' + id, item);
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    try {
        await saveBranchScheduleRequest(data, id);
        editingBranchScheduleRowId = null;
        alert('✅ تغییرات ذخیره شد');
    } catch (error) { alert(error.message); }
};

window.deleteBranchSchedule = async function (id) {
    if (!(await AppDialog.confirmDelete(allBranchSchedules, id, 'زمان‌بندی'))) return;
    await branchOfferingDelete('schedule',id);
    allBranchSchedules = allBranchSchedules.filter(function (s) { return s.id !== id; });
    if (editingBranchScheduleRowId === id) editingBranchScheduleRowId = null;
    window.filterBranchSchedules();
};
window.addEventListener('branch-offerings-loaded',function(e){window.branchScheduleOrganizations=e.detail.organizations||window.branchScheduleOrganizations||[];window.branchScheduleOrganizationSelection=e.detail.organization_selection||window.branchScheduleOrganizationSelection||'select';window.branchOfferingBranches=e.detail.branches||window.branchOfferingBranches||[];allBranchSchedules=e.detail.schedules||[];if(Array.isArray(e.detail.timezones)&&e.detail.timezones.length)window.branchScheduleTimezoneList=e.detail.timezones;filteredBranchSchedules=allBranchSchedules.slice();populateBranchScheduleDisplayTimezones();window.renderBranchSchedulesBranchTabs();window.filterBranchSchedules();});
if(window.branchOfferingData){allBranchSchedules=window.branchOfferingData.schedules||[];filteredBranchSchedules=allBranchSchedules.slice();populateBranchScheduleDisplayTimezones();window.renderBranchSchedulesBranchTabs();window.filterBranchSchedules();}

window.exportBranchSchedulesToExcel = async function () {
    const data = filteredBranchSchedules.length ? filteredBranchSchedules : allBranchSchedules;
    let csv = '\uFEFFردیف,روز,ساعت,دوره تکرار,منطقه زمانی,سازمان,وضعیت,خلاصه\n';
    data.forEach(function (item, i) {
        csv += (i + 1) + ',"' + item.day + '","' +
            (item.timeLabel || item.time || '') + '","' + (item.repeatPeriod || '') + '","' + (item.timezone || '') + '","' +
            item.branchName + '","' + item.status + '","' + (item.summary || '') + '"\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'زمانبندی_سازمان_' + new Date().toLocaleDateString('fa-IR') + '.csv';
    link.click();
};

window.exportBranchSchedulesToPDF = async function () {
    document.getElementById('modalContainer').innerHTML = window.getBranchSchedulePDFModalHTML
        ? window.getBranchSchedulePDFModalHTML(bsPdfColumns) : '';
};

window.generateBranchSchedulesPDF = async function () {
    if (!window.html2canvas) return alert('ابزار PDF بارگذاری نشده است.');
    const title = document.getElementById('bsPdfTitle') && document.getElementById('bsPdfTitle').value || 'گزارش برنامه زمانی سازمان';
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
    doc.save('زمانبندی_سازمان_' + date + '.pdf');
    closeModal();
};

setTimeout(async function () {
    if (document.getElementById('branchSchedulesTable')) {
        try { await ensureBranchScheduleCatalog(); } catch(error) { console.error(error); }
        populateBranchScheduleDisplayTimezones();
        window.renderBranchSchedulesBranchTabs();
        window.filterBranchSchedules();
    }
}, 200);
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){pollBranchSchedulesRealtime();setInterval(pollBranchSchedulesRealtime,2000);document.addEventListener('visibilitychange',function(){if(!document.hidden)pollBranchSchedulesRealtime();});});
else { pollBranchSchedulesRealtime();setInterval(pollBranchSchedulesRealtime,2000);document.addEventListener('visibilitychange',function(){if(!document.hidden)pollBranchSchedulesRealtime();}); }
})();
