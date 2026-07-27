// ==================== داده نمونه زمان‌بندی اعضا ====================
let allMemberSchedules = [
    { id: 1, name: "استاد محمد موسوی", role: "استاد", day: "شنبه", time: "۰۹:۰۰-۱۴:۰۰", branchId: 1, branchName: "شعبه مرکزی", status: "فعال" },
    { id: 2, name: "استاد علی رضایی", role: "استاد", day: "یکشنبه", time: "۱۰:۰۰-۱۶:۰۰", branchId: 1, branchName: "شعبه مرکزی", status: "فعال" },
    { id: 3, name: "زهرا کریمی", role: "منشی", day: "شنبه تا چهارشنبه", time: "۰۸:۰۰-۱۶:۰۰", branchId: 2, branchName: "شعبه ونک", status: "فعال" },
    { id: 4, name: "استاد بهرامی", role: "استاد", day: "دوشنبه", time: "۱۴:۰۰-۲۰:۰۰", branchId: 2, branchName: "شعبه ونک", status: "فعال" },
    { id: 5, name: "امیر نوری", role: "مدیر", day: "شنبه تا پنجشنبه", time: "۰۹:۰۰-۱۷:۰۰", branchId: 1, branchName: "شعبه مرکزی", status: "فعال" },
    { id: 6, name: "استاد کاظمی", role: "استاد", day: "سه‌شنبه", time: "۰۹:۰۰-۱۳:۰۰", branchId: 3, branchName: "شعبه سعادت‌آباد", status: "فعال" },
    { id: 7, name: "نگار احمدی", role: "منشی", day: "شنبه تا چهارشنبه", time: "۰۸:۳۰-۱۶:۳۰", branchId: 3, branchName: "شعبه سعادت‌آباد", status: "فعال" },
    { id: 8, name: "استاد نوری", role: "استاد", day: "چهارشنبه", time: "۱۵:۰۰-۲۱:۰۰", branchId: 4, branchName: "شعبه کرج", status: "مرخصی" },
    { id: 9, name: "پارسا جعفری", role: "پرسنل", day: "شنبه تا پنجشنبه", time: "۰۸:۰۰-۱۶:۰۰", branchId: 4, branchName: "شعبه کرج", status: "فعال" },
    { id: 10, name: "استاد کاظمی", role: "استاد", day: "پنجشنبه", time: "۱۰:۰۰-۱۴:۰۰", branchId: 2, branchName: "شعبه ونک", status: "فعال" },
    { id: 11, name: "هستی محمدی", role: "منشی", day: "شنبه تا چهارشنبه", time: "۰۹:۰۰-۱۷:۰۰", branchId: 1, branchName: "شعبه مرکزی", status: "فعال" },
    { id: 12, name: "استاد نوری", role: "استاد", day: "یکشنبه", time: "۱۶:۰۰-۲۰:۰۰", branchId: 3, branchName: "شعبه سعادت‌آباد", status: "فعال" }
];

let currentMemberScheduleBranch = 'all';
let memberSchedulesCurrentPage = 1;
const memberSchedulesPerPage = 10;
let filteredMemberSchedules = [...allMemberSchedules];

window.renderMemberSchedulesBranchTabs = function() {
    const container = document.getElementById('memberSchedulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.member-schedule-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'member-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterMemberSchedulesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterMemberSchedulesByBranch = function(branchId) {
    currentMemberScheduleBranch = branchId;

    document.querySelectorAll('.member-schedule-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.member-schedule-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) {
            tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            tabs[0].classList.remove('border-gray-200');
        }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    filterMemberSchedules();
};

window.filterMemberSchedules = function() {
    const search = (document.getElementById('memberScheduleSearch')?.value || '').trim().toLowerCase();
    const role = document.getElementById('filterMemberRole')?.value || '';
    const day = document.getElementById('filterMemberDay')?.value || '';

    filteredMemberSchedules = allMemberSchedules.filter(s => {
        const matchBranch = currentMemberScheduleBranch === 'all' || s.branchId == currentMemberScheduleBranch;
        const matchSearch = !search || s.name.toLowerCase().includes(search);
        const matchRole = !role || s.role === role;
        const matchDay = !day || s.day.includes(day);
        return matchBranch && matchSearch && matchRole && matchDay;
    });

    memberSchedulesCurrentPage = 1;
    renderMemberSchedulesTable();
};

window.renderMemberSchedulesTable = function() {
    const tbody = document.querySelector('#memberSchedulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(filteredMemberSchedules.length / memberSchedulesPerPage) || 1;
    if (memberSchedulesCurrentPage > totalPages) memberSchedulesCurrentPage = totalPages;

    const start = (memberSchedulesCurrentPage - 1) * memberSchedulesPerPage;
    const pageData = filteredMemberSchedules.slice(start, start + memberSchedulesPerPage);

    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-gray-400">زمان‌بندی‌ای یافت نشد</td></tr>`;
    } else {
        pageData.forEach(s => {
            const statusClass = s.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700';
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${s.name}</td>
                <td class="py-4 px-5">${s.role}</td>
                <td class="py-4 px-5">${s.day}</td>
                <td class="py-4 px-5 font-mono">${s.time}</td>
                <td class="py-4 px-5">${s.branchName}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${s.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="editMemberSchedule(${s.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteMemberSchedule(${s.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    updateMemberSchedulesPagination(filteredMemberSchedules.length, start, totalPages);
};

function updateMemberSchedulesPagination(total, start, totalPages) {
    const info = document.getElementById('memberSchedulesPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(start + memberSchedulesPerPage, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} زمان‌بندی`;
    }

    const pagination = document.getElementById('memberSchedulesPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeMemberSchedulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${memberSchedulesCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeMemberSchedulesPage(${memberSchedulesCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${memberSchedulesCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, memberSchedulesCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeMemberSchedulesPage(${i})" class="px-3 py-1.5 rounded-lg ${i === memberSchedulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeMemberSchedulesPage(${memberSchedulesCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${memberSchedulesCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeMemberSchedulesPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${memberSchedulesCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;
    pagination.innerHTML = html;
}

window.changeMemberSchedulesPage = function(page) {
    const totalPages = Math.ceil(filteredMemberSchedules.length / memberSchedulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    memberSchedulesCurrentPage = page;
    renderMemberSchedulesTable();
};

window.openAddMemberScheduleModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن زمان‌بندی عضو</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام عضو *</label>
                    <input id="msName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نقش</label>
                    <select id="msRole" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="استاد">استاد</option>
                        <option value="منشی">منشی</option>
                        <option value="مدیر">مدیر</option>
                        <option value="پرسنل">پرسنل</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">روز</label>
                        <input id="msDay" type="text" placeholder="شنبه تا چهارشنبه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">ساعت</label>
                        <input id="msTime" type="text" placeholder="۰۹:۰۰-۱۷:۰۰" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="msBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveMemberSchedule()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveMemberSchedule = function() {
    const name = document.getElementById('msName')?.value.trim();
    if (!name) return alert('نام عضو الزامی است');

    const branchId = parseInt(document.getElementById('msBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allMemberSchedules.unshift({
        id: Date.now(),
        name,
        role: document.getElementById('msRole').value,
        day: document.getElementById('msDay').value || '—',
        time: document.getElementById('msTime').value || '—',
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        status: "فعال"
    });

    filterMemberSchedules();
    closeModal();
    alert('✅ زمان‌بندی ثبت شد');
};

window.editMemberSchedule = function(id) {
    alert('ویرایش زمان‌بندی عضو');
};

window.deleteMemberSchedule = function(id) {
    if (confirm('حذف این زمان‌بندی؟')) {
        allMemberSchedules = allMemberSchedules.filter(s => s.id !== id);
        filterMemberSchedules();
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('memberSchedulesTable')) {
        renderMemberSchedulesBranchTabs();
        filterMemberSchedules();
    }
}, 200);