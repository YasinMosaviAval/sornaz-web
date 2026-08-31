(function () {
'use strict';
// ==================== تعطیلات و مرخصی‌ها (ایزوله) ====================
window.holidayLeaveStatusesList = ['فعال', 'غیرفعال', 'در انتظار تأیید'];
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
    { value: 'holiday', label: 'تعطیل رسمی' }, { value: 'closed', label: 'تعطیل' },
    { value: 'unavailable', label: 'عدم دسترسی' }, { value: 'busy', label: 'مشغول' },
    { value: 'vacation', label: 'مرخصی' }, { value: 'blocked', label: 'مسدود' }
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

window.getHolidayLeaveBranches = function () { return (window.holidayLeaveCatalog?.organizations||window.staffCatalog?.organizations||[]).map(function(o){return{id:o.user_id,name:o.name,kind:o.branch_id?'branch':'academy',branchId:o.branch_id||0};}); };
window.isHolidayLeaveOrganizationFixed = function(){return (window.holidayLeaveCatalog?.organization_selection||window.staffCatalog?.organization_selection)==='fixed';};

window.getHolidayLeaveMemberOptions = function (organizationUserId) {const organizations=window.getHolidayLeaveBranches().filter(o=>!organizationUserId||String(o.id)===String(organizationUserId)).map(o=>({value:'organization:'+o.id,label:(o.kind==='academy'?'آموزشگاه: ':'شعبه: ')+o.name,userId:o.id,name:o.name,organizationUserId:o.id,targetType:'organization'}));const members=(window.holidayLeaveMembers||[]).filter(s=>!organizationUserId||String(s.organizationUserId)===String(organizationUserId)).map(function(s){return{value:'member:'+s.id,label:s.name+' - '+(s.typeLabel||s.role||'عضو'),id:s.id,userId:s.user_id,name:s.name,organizationUserId:s.organizationUserId,role:s.typeLabel||'عضو',targetType:'member'};});return organizations.concat(members);};
window.refreshHolidayLeaveMembers=function(prefix,selected){const f=n=>document.getElementById(prefix?prefix+n:'hl'+n),org=f('Branch')?.value,select=f('Member');if(!select)return;const options=window.getHolidayLeaveMemberOptions(org),selectedValue=selected?(String(selected).includes(':')?selected:'member:'+selected):'';select.innerHTML='<option value="">انتخاب شخص یا سازمان</option>'+options.map(m=>'<option value="'+m.value+'" '+(String(m.value)===String(selectedValue)?'selected':'')+'>'+m.label+'</option>').join('');window.refreshHolidayLeaveConflicts(prefix);};
window.toggleHolidayLeaveAllDay=function(prefix){const f=n=>document.getElementById(prefix?prefix+n:'hl'+n),allDay=!!f('AllDay')?.checked;f('TimeSection')?.classList.toggle('hidden',allDay);};

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

function hlTimeOptions(selected,end){const values=[];for(let m=0;m<1440+(end?30:0);m+=30)values.push(hlMinutesToTime(m));return '<option value="">انتخاب کنید</option>'+values.map(v=>'<option value="'+v+'" '+(v===selected?'selected':'')+'>'+v+'</option>').join('');}
function hlRenderRange(range){return '<div class="hl-range grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 items-end rounded-2xl border bg-white p-4"><label class="text-sm">ساعت شروع<select class="hl-range-start mt-2 w-full border rounded-xl py-3 px-3" onchange="refreshHolidayLeaveConflicts(\'\')">'+hlTimeOptions(range.start,false)+'</select></label><label class="text-sm">ساعت پایان<select class="hl-range-end mt-2 w-full border rounded-xl py-3 px-3">'+hlTimeOptions(range.end,true)+'</select></label><button type="button" onclick="removeHolidayLeaveRange(this)" class="mb-3 text-red-500">×</button></div>';}
window.buildHolidayLeaveTimeSlotsHTML=function(_,branchId,selectedSlots){const ranges=hlMergeConsecutiveSlots(selectedSlots||[]);return '<div class="hl-ranges space-y-3">'+(ranges.length?ranges:[{start:'',end:''}]).map(hlRenderRange).join('')+'</div><button type="button" onclick="addHolidayLeaveRange(this)" class="mt-3 text-sm text-indigo-600"><i class="fas fa-plus ml-1"></i>افزودن بازه زمانی جدید</button>';};
window.addHolidayLeaveRange=function(button){button.previousElementSibling.insertAdjacentHTML('beforeend',hlRenderRange({}));};window.removeHolidayLeaveRange=function(button){const box=button.closest('.hl-ranges');button.closest('.hl-range').remove();if(!box.children.length)box.insertAdjacentHTML('beforeend',hlRenderRange({}));};

window.refreshHolidayLeaveTimeSlots = async function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildHolidayLeaveTimeSlotsHTML(containerId, branchId, selectedSlots || []);
};

window.refreshHolidayLeaveConflicts=function(prefix){const f=n=>document.getElementById(prefix?prefix+n:'hl'+n),target=f('Member')?.value,member=window.getHolidayLeaveMemberOptions(f('Branch')?.value).find(x=>String(x.value)===String(target)),date=f('DateValue')?.value||f('Date')?.value,own=Number(f('RecordId')?.value||0);const busy=new Set((window.allHolidayLeavesShared||[]).filter(x=>String(x.memberId)===String(member?.userId)&&x.date===date&&Number(x.id)!==own).flatMap(x=>x.slots||[]));f('TimeSlots')?.querySelectorAll('.hl-range-start option,.hl-range-end option').forEach(o=>o.disabled=busy.has(o.value));};

let allHolidayLeaves = Array.isArray(window.adminAvailabilityExceptionsData) ? window.adminAvailabilityExceptionsData.slice() : [];
window.allHolidayLeavesShared=allHolidayLeaves;
let holidayLeaveRealtimeVersion=null,holidayLeaveRealtimeBusy=false;const holidayLeaveChannel='BroadcastChannel'in window?new BroadcastChannel('sornaz-admin-data'):null;
function hlEncode(data){const bytes=new TextEncoder().encode(JSON.stringify(data));let binary='';bytes.forEach(b=>binary+=String.fromCharCode(b));return btoa(binary);}
async function holidayLeaveApi(url,data){const token=window.adminCsrfToken||'',options={credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(data){options.method='POST';options.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';options.headers['X-CSRF-TOKEN']=token;options.body=new URLSearchParams({_token:token,payload_b64:hlEncode(data)}).toString();}const response=await fetch(url,options),payload=await response.json(),envelope=payload.data??payload;if(!response.ok||envelope.success===false)throw new Error(envelope.message||'عملیات تعطیلات و مرخصی‌ها ناموفق بود.');return envelope.data??envelope;}
window.loadHolidayLeaves=async function(){const data=await holidayLeaveApi('/academy/admin/availability-exceptions');window.holidayLeaveCatalog=data.staff_catalog||{};window.holidayLeaveMembers=data.members||[];allHolidayLeaves=data.exceptions||[];window.allHolidayLeavesShared=allHolidayLeaves;filteredHolidayLeaves=allHolidayLeaves.slice();holidayLeaveRealtimeVersion=data.version;window.renderHolidayLeavesBranchTabs();window.filterHolidayLeaves();window.dispatchEvent(new CustomEvent('sornaz:data-changed',{detail:{resource:'availability_exceptions'}}));return data;};

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

window.updateHolidayLeaveSortIcons = async function () {
    ['name', 'date', 'timeLabel', 'typeLabel', 'timezone', 'branchName', 'status'].forEach(function (f) {
        const icon = document.getElementById('hlSortIcon-' + f);
        if (!icon) return;
        icon.textContent = hlSortField === f ? (hlSortDirection === 'asc' ? '↑' : '↓') : '↕';
    });
};

window.sortHolidayLeavesBy = async function (field) {
    if (hlSortField === field) hlSortDirection = hlSortDirection === 'asc' ? 'desc' : 'asc';
    else { hlSortField = field; hlSortDirection = 'asc'; }
    sortHolidayLeaveItems();
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
    window.updateHolidayLeaveSortIcons();
};

window.renderHolidayLeavesBranchTabs = async function () {
    const container = document.getElementById('holidayLeavesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.holiday-leave-branch-tab:not(:first-child)').forEach(function (t) { t.remove(); });
    const academyButton=document.createElement('button');academyButton.dataset.value='academy';academyButton.className='holiday-leave-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border '+(currentHolidayLeaveBranch==='academy'?'bg-indigo-600 text-white border-indigo-600':'border-gray-200 hover:bg-gray-50')+' transition';academyButton.textContent='آموزشگاه';academyButton.onclick=function(){window.filterHolidayLeavesByBranch('academy');};container.appendChild(academyButton);
    window.getHolidayLeaveBranches().forEach(function (b) {
        const active = currentHolidayLeaveBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'holiday-leave-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.dataset.value = b.id;
        btn.onclick = function () { window.filterHolidayLeavesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterHolidayLeavesByBranch = async function (branchId) {
    currentHolidayLeaveBranch = branchId;
    const container=document.getElementById('holidayLeavesBranchTabs');if(container)container.dataset.selectedValue=String(branchId);
    document.querySelectorAll('.holiday-leave-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.holiday-leave-branch-tab');
    tabs.forEach(function(tab,index){const value=tab.dataset.value||(index===0?'all':'');if(String(value)===String(branchId)){tab.classList.add('bg-indigo-600','text-white','border-indigo-600');tab.classList.remove('border-gray-200');}});
    window.filterHolidayLeaves();
};

window.filterHolidayLeaves = async function () {
    const search = (document.getElementById('holidayLeaveSearch') && document.getElementById('holidayLeaveSearch').value || '').trim().toLowerCase();
    const status = document.getElementById('filterHolidayLeaveStatus') && document.getElementById('filterHolidayLeaveStatus').value || '';
    const timezone = document.getElementById('filterHolidayLeaveTimezone') && document.getElementById('filterHolidayLeaveTimezone').value || '';
    const type = document.getElementById('filterHolidayLeaveType') && document.getElementById('filterHolidayLeaveType').value || '';

    filteredHolidayLeaves = allHolidayLeaves.filter(function (s) {
        const matchBranch = window.matchesOrganizationFilter(s,currentHolidayLeaveBranch);
        const matchSearch = !search || (s.name || '').toLowerCase().includes(search) || (s.summary || '').toLowerCase().includes(search);
        const matchStatus = !status || s.status === status;
        const matchTz = !timezone || s.timezone === timezone;
        const matchType = !type || s.type === type;
        return matchBranch && matchSearch && matchStatus && matchTz && matchType;
    });

    holidayLeavesCurrentPage = 1;
    sortHolidayLeaveItems();
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
};

window.renderHolidayLeavesTable = async function (list) {
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
                expand.className = 'bg-gray-50 admin-inline-expand';
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
    if (info?.parentElement) info.parentElement.classList.toggle('hidden', totalPages <= 1);
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

window.changeHolidayLeavesPage = async function (page) {
    const totalPages = Math.ceil(filteredHolidayLeaves.length / holidayLeavesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    holidayLeavesCurrentPage = page;
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
};

function hlReadSelectedSlots(prefix) {
    const containerId = prefix ? prefix + 'TimeSlots' : 'hlTimeSlots';
    const container = document.getElementById(containerId);
    if (!container) return [];
    const slots=[];container.querySelectorAll('.hl-range').forEach(function(row){const start=row.querySelector('.hl-range-start')?.value,end=row.querySelector('.hl-range-end')?.value;if(start&&end&&start<end)for(let m=hlTimeToMinutes(start);m<hlTimeToMinutes(end);m+=30)slots.push(hlMinutesToTime(m));});return slots;
}

function readHolidayLeaveForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'hl' + s); };
    const branchId = f('Branch') && f('Branch').value;
    const branch = window.getHolidayLeaveBranches().find(function (b) { return String(b.id) === String(branchId); });
    const memberSel = f('Member');
    const targetValue = memberSel && memberSel.value;
    const targetParts = String(targetValue||'').split(':');
    const targetType = targetParts[0] === 'organization' ? 'organization' : 'member';
    const memberId = targetType === 'member' ? Number(targetParts[1]||0) : 0;
    const memberName = memberSel && memberSel.selectedOptions[0] && targetValue
        ? memberSel.selectedOptions[0].textContent : '';
    const allDay = !!f('AllDay')?.checked;
    const slots = allDay ? [] : hlReadSelectedSlots(prefix);
    const ranges = allDay ? [] : hlMergeConsecutiveSlots(slots);
    return {
        branchId: branchId, branchName: branch ? branch.name : 'نامشخص',
        memberId: memberId, name: memberName, targetType: targetType,
        membershipId: Number(memberId), organizationUserId: Number(branchId), allDay: allDay,
        date: f('DateValue') && f('DateValue').value || f('Date') && f('Date').value || '',
        type: f('Type') && f('Type').value || 'vacation',
        typeLabel: (window.holidayLeaveTypeList || []).find(function (item) { return item.value === (f('Type') && f('Type').value || 'vacation'); })?.label || 'مرخصی',
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

window.openAddHolidayLeaveModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveAddModalHTML
        ? window.getHolidayLeaveAddModalHTML() : '';window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));window.refreshHolidayLeaveMembers('');
};

window.saveHolidayLeave = async function () {
    const data = readHolidayLeaveForm('');
    if (!data.memberId && data.targetType!=='organization') return alert('انتخاب شخص یا سازمان الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.allDay && !data.ranges.length) return alert('حداقل یک بازه ساعت تعطیلی انتخاب کنید');
    try{await holidayLeaveApi('/academy/admin/availability-exceptions',data);await window.loadHolidayLeaves();holidayLeaveChannel?.postMessage({resource:'availability_exceptions',version:Date.now()});closeModal();alert('✅ در دیتابیس ثبت شد');}catch(error){alert(error.message);}
};

window.viewHolidayLeave = async function (id) {
    const item = allHolidayLeaves.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveDetailsModalHTML
        ? window.getHolidayLeaveDetailsModalHTML(item) : '';
};

window.editHolidayLeave = async function (id) {
    const item = allHolidayLeaves.find(function (x) { return x.id === id; });
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getHolidayLeaveEditModalHTML
        ? window.getHolidayLeaveEditModalHTML(item) : '';window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));window.refreshHolidayLeaveMembers('editHl',item.membershipId?'member:'+item.membershipId:'organization:'+item.organizationUserId);window.toggleHolidayLeaveAllDay('editHl');
};

window.saveEditedHolidayLeave = async function (id) {
    const data = readHolidayLeaveForm('editHl');
    if (!data.memberId && data.targetType!=='organization') return alert('انتخاب شخص یا سازمان الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.allDay && !data.ranges.length) return alert('حداقل یک بازه ساعت تعطیلی انتخاب کنید');
    try{await holidayLeaveApi('/academy/admin/availability-exceptions/'+id+'/update',data);editingHolidayLeaveRowId=null;await window.loadHolidayLeaves();holidayLeaveChannel?.postMessage({resource:'availability_exceptions',version:Date.now()});closeModal();alert('✅ تغییرات در دیتابیس ذخیره شد');}catch(error){alert(error.message);}
};

window.toggleHolidayLeaveInlineEdit = async function (id) {
    editingHolidayLeaveRowId = editingHolidayLeaveRowId === id ? null : id;
    window.renderHolidayLeavesTable(filteredHolidayLeaves);
    if(editingHolidayLeaveRowId===id){const item=allHolidayLeaves.find(x=>x.id===id);window.initLocalizedDateInputs?.(document);window.refreshHolidayLeaveMembers('inlineHl'+id,item?.membershipId?'member:'+item.membershipId:'organization:'+item?.organizationUserId);window.toggleHolidayLeaveAllDay('inlineHl'+id);}
};

window.saveInlineHolidayLeave = async function (id) {
    const data = readHolidayLeaveForm('inlineHl' + id);
    if (!data.memberId && data.targetType!=='organization') return alert('انتخاب شخص یا سازمان الزامی است');
    if (!data.date) return alert('تاریخ الزامی است');
    if (!data.allDay && !data.ranges.length) return alert('حداقل یک بازه ساعت تعطیلی انتخاب کنید');
    try{await holidayLeaveApi('/academy/admin/availability-exceptions/'+id+'/update',data);editingHolidayLeaveRowId=null;await window.loadHolidayLeaves();holidayLeaveChannel?.postMessage({resource:'availability_exceptions',version:Date.now()});alert('✅ تغییرات در دیتابیس ذخیره شد');}catch(error){alert(error.message);}
};

window.deleteHolidayLeave = async function (id) {
    if (!(await AppDialog.confirmDelete(allHolidayLeaves, id, 'تعطیلی یا مرخصی'))) return;
    try {
        await holidayLeaveApi('/academy/admin/availability-exceptions/'+id+'/delete',{});if(editingHolidayLeaveRowId===id)editingHolidayLeaveRowId=null;await window.loadHolidayLeaves();holidayLeaveChannel?.postMessage({resource:'availability_exceptions',version:Date.now()});
    } catch(error) { alert(error.message||'حذف تعطیلی یا مرخصی ناموفق بود.'); }
};
window.cycleHolidayLeaveStatus=async function(id){try{await holidayLeaveApi('/academy/admin/availability-exceptions/'+id+'/status',{});await window.loadHolidayLeaves();holidayLeaveChannel?.postMessage({resource:'availability_exceptions',version:Date.now()});}catch(error){alert(error.message);}};

window.exportHolidayLeavesToExcel = async function () {
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

window.exportHolidayLeavesToPDF = async function () {
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
        window.loadHolidayLeaves().catch(function(error){console.error(error);window.renderHolidayLeavesBranchTabs();window.filterHolidayLeaves();});
    }
}, 200);
async function pollHolidayLeaves(){if(holidayLeaveRealtimeBusy||document.hidden||!document.getElementById('holidayLeavesTable'))return;holidayLeaveRealtimeBusy=true;try{const data=await holidayLeaveApi('/academy/admin/availability-exceptions');if(holidayLeaveRealtimeVersion!==null&&data.version!==holidayLeaveRealtimeVersion){window.holidayLeaveCatalog=data.staff_catalog||{};window.holidayLeaveMembers=data.members||[];allHolidayLeaves=data.exceptions||[];window.allHolidayLeavesShared=allHolidayLeaves;filteredHolidayLeaves=allHolidayLeaves.slice();editingHolidayLeaveRowId=null;window.renderHolidayLeavesBranchTabs();window.filterHolidayLeaves();}holidayLeaveRealtimeVersion=data.version;}catch(error){}finally{holidayLeaveRealtimeBusy=false;}}
setInterval(pollHolidayLeaves,5000);holidayLeaveChannel?.addEventListener('message',event=>{if(event.data?.resource==='availability_exceptions')window.loadHolidayLeaves();});
})();
