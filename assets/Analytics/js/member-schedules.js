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

function memberScheduleField(prefix, name) { return document.getElementById(prefix ? prefix + name : 'ms' + name); }
function memberScheduleIso(date) { return date.toISOString().slice(0, 10); }
function memberScheduleDateLabel(iso) {
    const locale = (document.documentElement.lang || 'fa').toLowerCase().startsWith('fa') ? 'fa-IR-u-ca-persian' : 'en-US';
    return new Intl.DateTimeFormat(locale, {year:'numeric', month:'long', day:'numeric', weekday:'long'}).format(new Date(iso + 'T12:00:00'));
}
window.refreshMemberScheduleRepeatFields = function (prefix) {
    const repeat = memberScheduleField(prefix, 'Repeat')?.value || 'هفتگی';
    const dayWrap = memberScheduleField(prefix, 'DayWrap'), dateWrap = memberScheduleField(prefix, 'RepeatDateWrap');
    const day = memberScheduleField(prefix, 'Day'), date = memberScheduleField(prefix, 'RepeatDate');
    const interval = {'دو هفته':2,'سه هفته':3,'چهار هفته':4}[repeat] || 0;
    const dated = ['ماهانه','سالانه','بی‌تکرار'].includes(repeat);
    dayWrap?.classList.toggle('hidden', dated);
    dateWrap?.classList.toggle('hidden', repeat === 'هفتگی');
    if (!date) return;
    if (interval) {
        const jsDays={'یکشنبه':0,'دوشنبه':1,'سه‌شنبه':2,'چهارشنبه':3,'پنجشنبه':4,'جمعه':5,'شنبه':6};
        const target=jsDays[day?.value] ?? 6, today=new Date(); today.setHours(12,0,0,0);
        const delta=(target-today.getDay()+7)%7;
        const options=Array.from({length:interval},(_,i)=>{const d=new Date(today);d.setDate(today.getDate()+delta+i*7);return memberScheduleIso(d);});
        const selected=date.value;
        date.outerHTML='<select id="'+date.id+'" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">'+options.map(iso=>'<option value="'+iso+'" '+(iso===selected?'selected':'')+'>'+memberScheduleDateLabel(iso)+'</option>').join('')+'</select>';
    } else if (dated && date.tagName !== 'INPUT') {
        date.outerHTML='<input id="'+date.id+'" type="date" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">';
    }
    window.initLocalizedDateInputs?.(dateWrap || document);
};
window.toggleMemberScheduleRepeatDate = function (_, repeatValue) { window.refreshMemberScheduleRepeatFields(''); };

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

window.getMemberScheduleBranches = function () {
    if (typeof window.getBranchScheduleOrganizations === 'function') return window.getBranchScheduleOrganizations().map(function(o){return {id:o.user_id||o.id,name:o.name,kind:o.kind};});
    if (window.staffCatalog?.organizations?.length) return window.staffCatalog.organizations.map(function(o){return {id:o.user_id,name:o.name,kind:o.branch_id?'branch':'academy'};});
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' }, { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' }, { id: 4, name: 'شعبه کرج' }
    ];
};

window.getMemberScheduleMemberOptions = function () {
    if (typeof allStaff !== 'undefined' && allStaff.length) {
        return allStaff.filter(function(s){return s.type!=='student'&&s.type!=='waiting'&&s.type!=='waiting-list';}).map(function (s) {
            const lesson=(s.type==='teacher'&&s.lessonName&&s.lessonName!=='—')?' '+s.lessonName:'';
            const level=(window.staffCatalog?.levels||[]).find(function(x){return String(x.id)===String(s.levelId);});
            const label=s.name+' - '+(s.typeLabel||'عضو')+lesson+(level?' سطح '+level.name:'');
            return {value:s.id,label:label,id:s.id,userId:s.user_id||s.id,name:s.name,organizationUserId:s.organizationUserId,role:s.typeLabel||'عضو'};
        });
    }
    return memberScheduleMembers.map(function (m) { return { value: m.id, label: m.name, id: m.id, name: m.name }; });
};

window.refreshMemberScheduleMembers = function(prefix, selected) {
    const organization=memberScheduleField(prefix,'Branch')?.value;
    const select=memberScheduleField(prefix,'Member'); if(!select)return;
    const options=window.getMemberScheduleMemberOptions().filter(function(m){return !organization||String(m.organizationUserId)===String(organization);});
    select.innerHTML='<option value="">انتخاب عضو</option>'+options.map(function(m){return '<option value="'+m.value+'" data-role="'+m.role+'" '+(String(m.value)===String(selected||'')?'selected':'')+'>'+m.label+'</option>';}).join('');
    window.refreshMemberScheduleConflicts(prefix);
};

window.refreshMemberScheduleConflicts = function(prefix) {
    const membershipId=memberScheduleField(prefix,'Member')?.value, selectedMember=window.getMemberScheduleMemberOptions().find(function(row){return String(row.value)===String(membershipId);}),memberId=selectedMember?.userId,day=memberScheduleField(prefix,'Day')?.value;
    const ownId=Number(memberScheduleField(prefix,'RecordId')?.value||0);
    const repeatDate=memberScheduleField(prefix,'RepeatDate')?.value||'',busy=new Set(allMemberSchedules.filter(function(row){return String(row.memberId)===String(memberId)&&row.day===day&&row.id!==ownId;}).flatMap(function(row){return row.slots||[];}));
    if(repeatDate)(window.allHolidayLeavesShared||[]).filter(function(row){return String(row.memberId)===String(memberId)&&row.date===repeatDate;}).forEach(function(row){(row.slots||[]).forEach(slot=>busy.add(slot));});
    const box=memberScheduleField(prefix,'TimeSlots'); if(!box)return;
    box.querySelectorAll('.ms-range-start option,.ms-range-end option').forEach(function(option){option.disabled=busy.has(option.value);});
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

window.buildMemberScheduleTimeSlotsHTML = function (containerId, branchId, selectedSlots) {
    const ranges=mergeConsecutiveSlots(selectedSlots||[]);
    return '<div class="ms-ranges space-y-3">'+(ranges.length?ranges:[{start:'',end:'',status:'فعال'}]).map(renderMemberScheduleRange).join('')+'</div><button type="button" onclick="addMemberScheduleRange(this)" class="mt-3 text-sm text-indigo-600"><i class="fas fa-plus ml-1"></i>افزودن بازه زمانی جدید</button>';
};
function memberScheduleTimeOptions(selected, end) { const values=[];for(let m=0;m<1440+(end?30:0);m+=30)values.push(minutesToTime(m));return '<option value="">انتخاب کنید</option>'+values.map(function(v){return '<option value="'+v+'" '+(v===selected?'selected':'')+'>'+v+'</option>';}).join(''); }
function renderMemberScheduleRange(range) { const status=range.status||'فعال',statuses=['فعال','غیرفعال','پر شده','در انتظار تأیید'];return '<div class="ms-range grid grid-cols-1 sm:grid-cols-[1fr_1fr_1fr_auto] gap-3 items-end rounded-2xl border bg-white p-4"><label class="text-sm">ساعت شروع<select class="ms-range-start mt-2 w-full border rounded-xl py-3 px-3" onchange="refreshMemberScheduleConflicts(\'\')">'+memberScheduleTimeOptions(range.start,false)+'</select></label><label class="text-sm">ساعت پایان<select class="ms-range-end mt-2 w-full border rounded-xl py-3 px-3">'+memberScheduleTimeOptions(range.end,true)+'</select></label><label class="text-sm">وضعیت<select class="ms-range-status mt-2 w-full border rounded-xl py-3 px-3">'+statuses.map(function(value){return '<option '+(value===status?'selected':'')+'>'+value+'</option>';}).join('')+'</select></label><button type="button" onclick="removeMemberScheduleRange(this)" class="mb-3 text-red-500">×</button></div>'; }
window.addMemberScheduleRange=function(button){button.previousElementSibling.insertAdjacentHTML('beforeend',renderMemberScheduleRange({}));};
window.removeMemberScheduleRange=function(button){const box=button.closest('.ms-ranges');button.closest('.ms-range').remove();if(!box.children.length)box.insertAdjacentHTML('beforeend',renderMemberScheduleRange({}));};

window.refreshMemberScheduleTimeSlots = async function (containerId, branchId, selectedSlots) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = window.buildMemberScheduleTimeSlotsHTML(containerId, branchId, selectedSlots || []);
    const prefix=containerId.endsWith('TimeSlots')?containerId.slice(0,-9):'';
    window.refreshMemberScheduleConflicts(prefix==='ms'?'':prefix);
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
let memberScheduleRealtimeVersion=null,memberScheduleRealtimeBusy=false;
const memberScheduleRealtimeChannel='BroadcastChannel'in window?new BroadcastChannel('sornaz-admin-data'):null;
function encodeMemberSchedulePayload(data){const bytes=new TextEncoder().encode(JSON.stringify(data));let binary='';bytes.forEach(b=>binary+=String.fromCharCode(b));return btoa(binary);}
async function memberScheduleApi(url,data){const token=window.adminCsrfToken||'',options={credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(data){options.method='POST';options.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';options.headers['X-CSRF-TOKEN']=token;options.body=new URLSearchParams({_token:token,payload_b64:encodeMemberSchedulePayload(data)}).toString();}const response=await fetch(url,options),payload=await response.json(),envelope=payload.data??payload;if(!response.ok||envelope.success===false)throw new Error(envelope.message||'عملیات برنامه زمانی ناموفق بود.');return envelope.data??envelope;}
window.loadMemberSchedules=async function(){const data=await memberScheduleApi('/academy/admin/member-schedules');window.staffCatalog=data.staff_catalog||window.staffCatalog||{};if(Array.isArray(data.members)){allStaff=data.members.filter(x=>x.type!=='student');}allMemberSchedules=data.schedules||[];filteredMemberSchedules=allMemberSchedules.slice();memberScheduleRealtimeVersion=data.version;window.renderMemberSchedulesBranchTabs();window.filterMemberSchedules();return data;};
async function saveMemberScheduleRequest(data,id){const result=await memberScheduleApi('/academy/admin/member-schedules'+(id?'/'+id+'/update':''),data);await window.loadMemberSchedules();memberScheduleRealtimeChannel?.postMessage({resource:'member_schedules',version:Date.now()});return result;}
async function pollMemberSchedules(){if(memberScheduleRealtimeBusy||document.hidden||!document.getElementById('memberSchedulesTable'))return;memberScheduleRealtimeBusy=true;try{const data=await memberScheduleApi('/academy/admin/member-schedules');if(memberScheduleRealtimeVersion!==null&&data.version!==memberScheduleRealtimeVersion){window.staffCatalog=data.staff_catalog||window.staffCatalog;allStaff=(data.members||[]).filter(x=>x.type!=='student');allMemberSchedules=data.schedules||[];filteredMemberSchedules=allMemberSchedules.slice();editingMemberScheduleRowId=null;window.filterMemberSchedules();}memberScheduleRealtimeVersion=data.version;}catch(e){}finally{memberScheduleRealtimeBusy=false;}}
setInterval(pollMemberSchedules,5000);memberScheduleRealtimeChannel?.addEventListener('message',function(e){if(e.data?.resource==='member_schedules')window.loadMemberSchedules();});
window.addEventListener('sornaz:data-changed',function(event){if(event.detail?.resource==='availability_exceptions')document.querySelectorAll('[id$="TimeSlots"]').forEach(function(box){if(!box.querySelector('.ms-range'))return;const prefix=box.id.slice(0,-9);window.refreshMemberScheduleConflicts(prefix==='ms'?'':prefix);});});

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
    const academyButton=document.createElement('button');academyButton.dataset.value='academy';academyButton.className='member-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border '+(currentMemberScheduleBranch==='academy'?'bg-indigo-600 text-white border-indigo-600':'border-gray-200 hover:bg-gray-50')+' transition';academyButton.textContent='آموزشگاه';academyButton.onclick=function(){window.filterMemberSchedulesByBranch('academy');};container.appendChild(academyButton);
    window.getMemberScheduleBranches().forEach(function (b) {
        const active = currentMemberScheduleBranch == b.id;
        const btn = document.createElement('button');
        btn.className = 'member-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50') + ' transition';
        btn.textContent = b.name;
        btn.dataset.value = b.id;
        btn.onclick = function () { window.filterMemberSchedulesByBranch(b.id); };
        container.appendChild(btn);
    });
};

window.filterMemberSchedulesByBranch = async function (branchId) {
    currentMemberScheduleBranch = branchId;
    const container=document.getElementById('memberSchedulesBranchTabs');if(container)container.dataset.selectedValue=String(branchId);
    document.querySelectorAll('.member-schedule-branch-tab').forEach(function (tab) {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.member-schedule-branch-tab');
    tabs.forEach(function(tab,index){const value=tab.dataset.value||(index===0?'all':'');if(String(value)===String(branchId)){tab.classList.add('bg-indigo-600','text-white','border-indigo-600');tab.classList.remove('border-gray-200');}});
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
        const matchBranch = window.matchesOrganizationFilter(s,currentMemberScheduleBranch);
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
                expand.className = 'bg-gray-50 admin-inline-expand';
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
    if (info?.parentElement) info.parentElement.classList.toggle('hidden', totalPages <= 1);
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
    const slots=[];container.querySelectorAll('.ms-range').forEach(function(row){const start=row.querySelector('.ms-range-start')?.value,end=row.querySelector('.ms-range-end')?.value;if(start&&end&&start<end)for(let m=timeToMinutes(start);m<timeToMinutes(end);m+=30)slots.push(minutesToTime(m));});return slots;
}

function readMemberScheduleForm(prefix) {
    const f = function (s) { return document.getElementById(prefix ? prefix + s : 'ms' + s); };
    const branchId = parseInt(f('Branch') && f('Branch').value || window.getMemberScheduleBranches()[0]?.id, 10);
    const branch = window.getMemberScheduleBranches().find(function (b) { return b.id === branchId; });
    const memberSel = f('Member');
    const memberId = memberSel && memberSel.value;
    const memberOption=memberSel && memberSel.selectedOptions[0];
    const member=window.getMemberScheduleMemberOptions().find(function(row){return String(row.value)===String(memberId)&&String(row.organizationUserId)===String(branchId);});
    const memberName = member?.name || memberOption?.textContent || '';
    const slots = readSelectedSlots(prefix);
    const ranges = Array.from(f('TimeSlots')?.querySelectorAll('.ms-range') || []).map(function(row){return {start:row.querySelector('.ms-range-start')?.value||'',end:row.querySelector('.ms-range-end')?.value||'',status:row.querySelector('.ms-range-status')?.value||'فعال'};}).filter(function(range){return range.start&&range.end&&range.start<range.end;});
    const repeatPeriod = f('Repeat') && f('Repeat').value || 'هفتگی';
    return {
        branchId: branchId, organizationUserId: branchId, branchName: branch ? branch.name : 'نامشخص',
        memberId: member?.userId || memberId, membershipId: Number(memberId), name: memberName,
        role: member?.role || f('Role') && f('Role').value || 'استاد',
        day: f('Day') && !f('DayWrap')?.classList.contains('hidden') ? f('Day').value : '',
        status: f('Status') && f('Status').value || 'فعال',
        repeatPeriod: repeatPeriod,
        repeatDate: repeatPeriod === 'هفتگی' ? '' : (f('RepeatDate') && f('RepeatDate').value || ''),
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
            status: range.status || base.status,
            timeLabel: rangeLabel(range), time: rangeLabel(range)
        });
    });
}

window.openAddMemberScheduleModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    try {
        if (!window.branchOfferingData && typeof window.loadBranchOfferings === 'function') await window.loadBranchOfferings();
        if ((!window.staffCatalog?.organizations?.length || typeof allStaff === 'undefined' || !allStaff.length) && typeof loadStaffData === 'function') await loadStaffData(false);
    } catch(error) { return alert(error.message || 'بارگذاری سازمان‌ها و اعضا ناموفق بود.'); }
    document.getElementById('modalContainer').innerHTML = window.getMemberScheduleAddModalHTML
        ? window.getMemberScheduleAddModalHTML() : '';
    window.refreshMemberScheduleRepeatFields('');
    window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));
    window.refreshMemberScheduleMembers('');
};

window.cycleMemberScheduleStatus = async function(id) {
    const item=allMemberSchedules.find(function(row){return row.id===id;});
    if(!item||item.readOnly)return;
    try{await memberScheduleApi('/academy/admin/member-schedules/'+id+'/status',{});await window.loadMemberSchedules();memberScheduleRealtimeChannel?.postMessage({resource:'member_schedules',version:Date.now()});}catch(error){alert(error.message);}
};

window.saveMemberSchedule = async function () {
    const data = readMemberScheduleForm('');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.day && !data.repeatDate) return alert('روز یا تاریخ شروع الزامی است');
    if (data.repeatPeriod !== 'هفتگی' && !data.repeatDate) return alert('انتخاب اولین تاریخ الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    try{await saveMemberScheduleRequest(data,0);closeModal();alert('✅ بازه(های) زمانی در دیتابیس ثبت شد');}catch(error){alert(error.message);}
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
    window.refreshMemberScheduleRepeatFields('editMs');
    window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));
};

window.saveEditedMemberSchedule = async function (id) {
    const data = readMemberScheduleForm('editMs');
    if (!data.memberId || data.memberId === '__new__') return alert('انتخاب عضو الزامی است');
    if (!data.day) return alert('روز الزامی است');
    if (!data.ranges.length) return alert('حداقل یک بازه ساعتی انتخاب کنید');
    try{await saveMemberScheduleRequest(data,id);editingMemberScheduleRowId=null;closeModal();alert('✅ تغییرات در دیتابیس ذخیره شد');}catch(error){alert(error.message);}
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
    try{await saveMemberScheduleRequest(data,id);editingMemberScheduleRowId=null;alert('✅ تغییرات در دیتابیس ذخیره شد');}catch(error){alert(error.message);}
};

window.deleteMemberSchedule = async function (id) {
    if (!(await AppDialog.confirmDelete(allMemberSchedules, id, 'زمان‌بندی'))) return;
    try {
        const body=new FormData(); body.append('_token',window.adminCsrfToken||'');
        await memberScheduleApi('/academy/admin/member-schedules/'+id+'/delete',{});editingMemberScheduleRowId=null;await window.loadMemberSchedules();memberScheduleRealtimeChannel?.postMessage({resource:'member_schedules',version:Date.now()});
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
