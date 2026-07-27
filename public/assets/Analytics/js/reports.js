// ==================== داده نمونه گزارش‌ها ====================
let allReports = [
    { id: 1, title: "گزارش حضور و غیاب ماهانه", branchId: 1, branchName: "شعبه مرکزی", type: "حضور و غیاب", date: "۱۴۰۴/۰۵/۰۱", status: "آماده" },
    { id: 2, title: "گزارش درآمد تیرماه", branchId: 1, branchName: "شعبه مرکزی", type: "مالی", date: "۱۴۰۴/۰۴/۳۱", status: "آماده" },
    { id: 3, title: "آمار ثبت‌نام هنرجویان جدید", branchId: 2, branchName: "شعبه ونک", type: "ثبت‌نام", date: "۱۴۰۴/۰۵/۱۰", status: "آماده" },
    { id: 4, title: "گزارش عملکرد اساتید", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "آموزشی", date: "۱۴۰۴/۰۴/۲۸", status: "در حال تهیه" },
    { id: 5, title: "گزارش بدهی هنرجویان", branchId: 4, branchName: "شعبه کرج", type: "مالی", date: "۱۴۰۴/۰۵/۰۵", status: "آماده" },
    { id: 6, title: "آمار کلاس‌های برگزار شده", branchId: 1, branchName: "شعبه مرکزی", type: "آموزشی", date: "۱۴۰۴/۰۵/۱۲", status: "آماده" },
    { id: 7, title: "گزارش رضایت هنرجویان", branchId: 2, branchName: "شعبه ونک", type: "نظرسنجی", date: "۱۴۰۴/۰۴/۲۰", status: "آماده" }
];

let currentReportBranch = 'all';

window.renderReportsBranchTabs = function() {
    const container = document.getElementById('reportsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.report-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'report-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterReportsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterReportsByBranch = function(branchId) {
    currentReportBranch = branchId;

    document.querySelectorAll('.report-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.report-branch-tab');
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

    renderReportsTable();
    renderReportsSummary();
};

window.renderReportsSummary = function() {
    const container = document.getElementById('reportsSummaryCards');
    if (!container) return;

    const list = currentReportBranch === 'all' ? allReports : allReports.filter(r => r.branchId == currentReportBranch);

    container.innerHTML = `
        <div class="bg-white rounded-3xl p-5 shadow">
            <p class="text-gray-500 text-sm">کل گزارش‌ها</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">${list.length}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow">
            <p class="text-gray-500 text-sm">گزارش‌های آماده</p>
            <p class="text-3xl font-bold text-green-600 mt-1">${list.filter(r => r.status === 'آماده').length}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow">
            <p class="text-gray-500 text-sm">در حال تهیه</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">${list.filter(r => r.status === 'در حال تهیه').length}</p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow">
            <p class="text-gray-500 text-sm">گزارش‌های مالی</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">${list.filter(r => r.type === 'مالی').length}</p>
        </div>
    `;
};

window.renderReportsTable = function() {
    const tbody = document.querySelector('#reportsTable tbody');
    if (!tbody) return;

    const list = currentReportBranch === 'all' ? allReports : allReports.filter(r => r.branchId == currentReportBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">گزارشی یافت نشد</td></tr>`
        : list.map(r => {
            const statusClass = r.status === 'آماده' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${r.title}</td>
                <td class="py-4 px-5">${r.branchName}</td>
                <td class="py-4 px-5">${r.type}</td>
                <td class="py-4 px-5">${r.date}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${r.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewReport(${r.id})" class="text-indigo-600 hover:underline text-sm">مشاهده</button>
                </td>
            </tr>`;
        }).join('');
};

window.viewReport = function(id) {
    const r = allReports.find(x => x.id === id);
    if (r) alert(`گزارش: ${r.title}\nشعبه: ${r.branchName}\nنوع: ${r.type}\nتاریخ: ${r.date}`);
};

window.exportReports = function() {
    alert('خروجی اکسل گزارش‌ها (در نسخه واقعی فایل دانلود می‌شود)');
};

// Init
setTimeout(() => {
    if (document.getElementById('reportsTable')) {
        renderReportsBranchTabs();
        filterReportsByBranch('all');
    }
}, 200);