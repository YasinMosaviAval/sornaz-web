// ==================== داده نمونه برنامه زمانی ====================
const days = ["شنبه", "یکشنبه", "دوشنبه", "سه‌شنبه", "چهارشنبه", "پنجشنبه", "جمعه"];
const scheduleTypes = ["خصوصی", "گروهی", "آنلاین"];

let allSchedules = [
    { id: 1, day: "شنبه", time: "۱۰:۰۰-۱۱:۰۰", student: "سارا احمدی", teacher: "استاد موسوی", instrument: "پیانو", classroom: "کلاس پیانو ۱", branchId: 1, branchName: "شعبه مرکزی", type: "خصوصی" },
    { id: 2, day: "شنبه", time: "۱۱:۳۰-۱۲:۳۰", student: "امیر حسینی", teacher: "استاد رضایی", instrument: "گیتار", classroom: "کلاس گیتار A", branchId: 1, branchName: "شعبه مرکزی", type: "خصوصی" },
    { id: 3, day: "یکشنبه", time: "۱۶:۰۰-۱۷:۳۰", student: "گروه ۸ نفره", teacher: "استاد بهرامی", instrument: "ویولن", classroom: "سالن تمرین گروهی", branchId: 2, branchName: "شعبه ونک", type: "گروهی" },
    { id: 4, day: "دوشنبه", time: "۰۹:۰۰-۱۰:۰۰", student: "زهرا کریمی", teacher: "استاد موسوی", instrument: "پیانو", classroom: "کلاس پیانو ۲", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "خصوصی" },
    { id: 5, day: "سه‌شنبه", time: "۱۴:۰۰-۱۵:۰۰", student: "علی محمدی", teacher: "استاد کاظمی", instrument: "آواز", classroom: "کلاس آواز", branchId: 2, branchName: "شعبه ونک", type: "خصوصی" },
    { id: 6, day: "چهارشنبه", time: "۱۸:۰۰-۱۹:۰۰", student: "نگار رضایی", teacher: "استاد نوری", instrument: "درام", classroom: "کلاس درام", branchId: 4, branchName: "شعبه کرج", type: "آنلاین" },
    { id: 7, day: "پنجشنبه", time: "۱۱:۰۰-۱۲:۰۰", student: "پارسا نوری", teacher: "استاد موسوی", instrument: "پیانو", classroom: "کلاس پیانو ۱", branchId: 1, branchName: "شعبه مرکزی", type: "خصوصی" },
    { id: 8, day: "شنبه", time: "۱۵:۰۰-۱۶:۳۰", student: "گروه ۶ نفره", teacher: "استاد بهرامی", instrument: "گیتار", classroom: "سالن تمرین گروهی", branchId: 1, branchName: "شعبه مرکزی", type: "گروهی" },
    { id: 9, day: "یکشنبه", time: "۱۰:۰۰-۱۱:۰۰", student: "مهسا جعفری", teacher: "استاد کاظمی", instrument: "آواز", classroom: "کلاس آواز", branchId: 2, branchName: "شعبه ونک", type: "خصوصی" },
    { id: 10, day: "دوشنبه", time: "۱۷:۰۰-۱۸:۰۰", student: "کیان نوری", teacher: "استاد رضایی", instrument: "گیتار", classroom: "کلاس گیتار A", branchId: 1, branchName: "شعبه مرکزی", type: "خصوصی" },
    { id: 11, day: "سه‌شنبه", time: "۰۹:۳۰-۱۰:۳۰", student: "هستی احمدی", teacher: "استاد نوری", instrument: "ویولن", classroom: "کلاس ویولن", branchId: 2, branchName: "شعبه ونک", type: "خصوصی" },
    { id: 12, day: "چهارشنبه", time: "۱۶:۰۰-۱۷:۰۰", student: "آرین محمدی", teacher: "استاد کاظمی", instrument: "پیانو", classroom: "کلاس پیانو ۲", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "آنلاین" }
];

let currentScheduleBranch = 'all';
let schedulesCurrentPage = 1;
const schedulesPerPage = 10;
let filteredSchedules = [...allSchedules];

window.renderSchedulesBranchTabs = function() {
    const container = document.getElementById('schedulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.schedule-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterSchedulesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterSchedulesByBranch = function(branchId) {
    currentScheduleBranch = branchId;

    document.querySelectorAll('.schedule-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.schedule-branch-tab');
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

    filterSchedules();
};

window.filterSchedules = function() {
    const search = (document.getElementById('scheduleSearch')?.value || '').trim().toLowerCase();
    const day = document.getElementById('filterScheduleDay')?.value || '';
    const type = document.getElementById('filterScheduleType')?.value || '';

    filteredSchedules = allSchedules.filter(s => {
        const matchBranch = currentScheduleBranch === 'all' || s.branchId == currentScheduleBranch;
        const matchSearch = !search || s.student.toLowerCase().includes(search) || s.teacher.toLowerCase().includes(search);
        const matchDay = !day || s.day === day;
        const matchType = !type || s.type === type;
        return matchBranch && matchSearch && matchDay && matchType;
    });

    schedulesCurrentPage = 1;
    renderSchedulesTable();
};

window.renderSchedulesTable = function() {
    const tbody = document.querySelector('#schedulesTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(filteredSchedules.length / schedulesPerPage) || 1;
    if (schedulesCurrentPage > totalPages) schedulesCurrentPage = totalPages;

    const start = (schedulesCurrentPage - 1) * schedulesPerPage;
    const pageData = filteredSchedules.slice(start, start + schedulesPerPage);

    tbody.innerHTML = '';

    if (pageData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="py-12 text-center text-gray-400">برنامه‌ای یافت نشد</td></tr>`;
    } else {
        pageData.forEach(s => {
            const typeClass = s.type === 'خصوصی' ? 'bg-indigo-100 text-indigo-700' : s.type === 'گروهی' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700';
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = `
                <td class="py-4 px-5 font-medium">${s.day}</td>
                <td class="py-4 px-5 font-mono">${s.time}</td>
                <td class="py-4 px-5">${s.student}</td>
                <td class="py-4 px-5">${s.teacher}</td>
                <td class="py-4 px-5">${s.instrument}</td>
                <td class="py-4 px-5">${s.classroom}</td>
                <td class="py-4 px-5">${s.branchName}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${typeClass}">${s.type}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="editSchedule(${s.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteSchedule(${s.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    updateSchedulesPagination(filteredSchedules.length, start, totalPages);
};

function updateSchedulesPagination(total, start, totalPages) {
    const info = document.getElementById('schedulesPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(start + schedulesPerPage, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} برنامه`;
    }

    const pagination = document.getElementById('schedulesPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeSchedulesPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${schedulesCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeSchedulesPage(${schedulesCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${schedulesCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, schedulesCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeSchedulesPage(${i})" class="px-3 py-1.5 rounded-lg ${i === schedulesCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeSchedulesPage(${schedulesCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${schedulesCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeSchedulesPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${schedulesCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;
    pagination.innerHTML = html;
}

window.changeSchedulesPage = function(page) {
    const totalPages = Math.ceil(filteredSchedules.length / schedulesPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    schedulesCurrentPage = page;
    renderSchedulesTable();
};

// ==================== افزودن برنامه زمانی ====================
window.openAddScheduleModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const dayOptions = days.map(d => `<option value="${d}">${d}</option>`).join('');
    const typeOptions = scheduleTypes.map(t => `<option value="${t}">${t}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن برنامه زمانی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">روز *</label>
                        <select id="schDay" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${dayOptions}</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">ساعت *</label>
                        <input id="schTime" type="text" placeholder="۱۰:۰۰-۱۱:۰۰" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">هنرجو *</label>
                        <input id="schStudent" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">استاد *</label>
                        <input id="schTeacher" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">ساز</label>
                        <input id="schInstrument" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">کلاس فیزیکی</label>
                        <input id="schClassroom" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">شعبه</label>
                        <select id="schBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع کلاس</label>
                        <select id="schType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                    </div>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveSchedule()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveSchedule = function() {
    const day = document.getElementById('schDay')?.value;
    const time = document.getElementById('schTime')?.value.trim();
    const student = document.getElementById('schStudent')?.value.trim();
    const teacher = document.getElementById('schTeacher')?.value.trim();
    if (!day || !time || !student || !teacher) return alert('روز، ساعت، هنرجو و استاد الزامی است');

    const branchId = parseInt(document.getElementById('schBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allSchedules.unshift({
        id: Date.now(),
        day,
        time,
        student,
        teacher,
        instrument: document.getElementById('schInstrument').value || '—',
        classroom: document.getElementById('schClassroom').value || '—',
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type: document.getElementById('schType').value
    });

    filterSchedules();
    closeModal();
    alert('✅ برنامه زمانی ثبت شد');
};

window.editSchedule = function(id) {
    alert('ویرایش برنامه زمانی');
};

window.deleteSchedule = function(id) {
    if (confirm('حذف این برنامه؟')) {
        allSchedules = allSchedules.filter(s => s.id !== id);
        filterSchedules();
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('schedulesTable')) {
        renderSchedulesBranchTabs();
        filterSchedules();
    }
}, 200);