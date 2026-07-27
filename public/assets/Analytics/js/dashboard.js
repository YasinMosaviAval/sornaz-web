// ==================== داده داشبورد ====================
let dashboardData = {
    stats: {
        activeStudents: 248,
        todayClasses: 18,
        monthlyIncome: "۴۸.۵ میلیون",
        attendanceRate: "۹۲٪",
        pendingPayments: 12,
        absencesToday: 5,
        newMessages: 3,
        urgentAlerts: 4
    },
    todayClasses: [
        { time: "۱۰:۰۰", student: "سارا احمدی", instrument: "پیانو", teacher: "استاد موسوی", branch: "شعبه مرکزی" },
        { time: "۱۱:۳۰", student: "امیر حسینی", instrument: "گیتار", teacher: "استاد رضایی", branch: "شعبه مرکزی" },
        { time: "۱۴:۰۰", student: "زهرا کریمی", instrument: "ویولن", teacher: "استاد بهرامی", branch: "شعبه ونک" },
        { time: "۱۶:۰۰", student: "گروه ۶ نفره", instrument: "آواز", teacher: "استاد کاظمی", branch: "شعبه سعادت‌آباد" },
        { time: "۱۸:۰۰", student: "علی محمدی", instrument: "درام", teacher: "استاد نوری", branch: "شعبه کرج" }
    ],
    urgentItems: [
        { type: "بدهی", text: "۱۲ هنرجو بدهکار — مجموع ۸.۵ میلیون تومان", color: "rose" },
        { type: "غیبت", text: "۵ غیبت ثبت‌شده امروز", color: "amber" },
        { type: "پیام", text: "۳ پیام خوانده‌نشده", color: "indigo" },
        { type: "کلاس", text: "۲ کلاس بدون استاد تا ۱ ساعت دیگر", color: "red" }
    ],
    recentPayments: [
        { name: "سارا احمدی", amount: "۱,۲۰۰,۰۰۰", time: "۱۰ دقیقه پیش", branch: "شعبه مرکزی" },
        { name: "امیر حسینی", amount: "۹۵۰,۰۰۰", time: "۱ ساعت پیش", branch: "شعبه ونک" },
        { name: "نگار رضایی", amount: "۱,۱۰۰,۰۰۰", time: "۳ ساعت پیش", branch: "شعبه سعادت‌آباد" }
    ],
    todayAbsences: [
        { student: "علی محمدی", instrument: "پیانو", teacher: "استاد موسوی", time: "۱۰:۰۰", branch: "شعبه مرکزی" },
        { student: "مهسا جعفری", instrument: "گیتار", teacher: "استاد رضایی", time: "۱۱:۳۰", branch: "شعبه ونک" },
        { student: "پارسا نوری", instrument: "ویولن", teacher: "استاد بهرامی", time: "۱۴:۰۰", branch: "شعبه سعادت‌آباد" }
    ]
};

let currentDashboardBranch = 'all';

window.renderDashboardBranchTabs = function() {
    const container = document.getElementById('dashboardBranchTabs');
    if (!container) return;
    container.querySelectorAll('.dashboard-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterDashboardByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterDashboardByBranch = function(branchId) {
    currentDashboardBranch = branchId;

    document.querySelectorAll('.dashboard-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.dashboard-branch-tab');
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

    renderDashboard();
};

window.renderDashboard = function() {
    // آمار
    const stats = document.getElementById('dashboardStats');
    if (stats) {
        const s = dashboardData.stats;
        stats.innerHTML = `
            <div class="bg-white rounded-3xl p-6 card-hover shadow">
                <div class="flex justify-between">
                    <div>
                        <p class="text-gray-500">هنرجویان فعال</p>
                        <p class="text-4xl font-bold text-indigo-600 mt-2">${s.activeStudents}</p>
                    </div>
                    <i class="fas fa-users text-5xl text-indigo-200"></i>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 card-hover shadow">
                <div class="flex justify-between">
                    <div>
                        <p class="text-gray-500">کلاس‌های امروز</p>
                        <p class="text-4xl font-bold text-amber-600 mt-2">${s.todayClasses}</p>
                    </div>
                    <i class="fas fa-calendar text-5xl text-amber-200"></i>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 card-hover shadow">
                <div class="flex justify-between">
                    <div>
                        <p class="text-gray-500">درآمد ماهانه</p>
                        <p class="text-4xl font-bold text-emerald-600 mt-2">${s.monthlyIncome}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-5xl text-emerald-200"></i>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 card-hover shadow">
                <div class="flex justify-between">
                    <div>
                        <p class="text-gray-500">نرخ حضور</p>
                        <p class="text-4xl font-bold text-rose-600 mt-2">${s.attendanceRate}</p>
                    </div>
                    <i class="fas fa-chart-pie text-5xl text-rose-200"></i>
                </div>
            </div>
        `;
    }

    // کلاس‌های امروز
    const todayList = document.getElementById('todayClassesList');
    if (todayList) {
        todayList.innerHTML = dashboardData.todayClasses.map(c => `
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl">
                <div>
                    <p class="font-medium">${c.student}</p>
                    <p class="text-sm text-gray-500">${c.instrument} — ${c.teacher}</p>
                </div>
                <div class="text-left">
                    <p class="font-mono text-lg">${c.time}</p>
                    <p class="text-xs text-gray-400">${c.branch}</p>
                </div>
            </div>
        `).join('');
    }

    // موارد فوری
    const urgentList = document.getElementById('urgentItemsList');
    if (urgentList) {
        const colorMap = { rose: 'bg-rose-50 border-rose-200 text-rose-700', amber: 'bg-amber-50 border-amber-200 text-amber-700', indigo: 'bg-indigo-50 border-indigo-200 text-indigo-700', red: 'bg-red-50 border-red-200 text-red-700' };
        urgentList.innerHTML = dashboardData.urgentItems.map(u => `
            <div class="p-3 rounded-2xl border ${colorMap[u.color] || 'bg-gray-50'}">
                <span class="text-xs font-bold">${u.type}</span>
                <p class="text-sm mt-1">${u.text}</p>
            </div>
        `).join('');
    }

    // آخرین پرداخت‌ها
    const paymentsList = document.getElementById('recentPaymentsList');
    if (paymentsList) {
        paymentsList.innerHTML = dashboardData.recentPayments.map(p => `
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-2xl">
                <div>
                    <p class="font-medium">${p.name}</p>
                    <p class="text-xs text-gray-400">${p.branch} — ${p.time}</p>
                </div>
                <p class="font-bold text-emerald-600">${p.amount} تومان</p>
            </div>
        `).join('');
    }

    // غیبت‌های امروز
    const absencesList = document.getElementById('todayAbsencesList');
    if (absencesList) {
        absencesList.innerHTML = dashboardData.todayAbsences.map(a => `
            <div class="flex justify-between items-center p-3 bg-rose-50 rounded-2xl border border-rose-100">
                <div>
                    <p class="font-medium">${a.student}</p>
                    <p class="text-sm text-gray-500">${a.instrument} — ${a.teacher}</p>
                </div>
                <div class="text-left">
                    <p class="font-mono">${a.time}</p>
                    <p class="text-xs text-gray-400">${a.branch}</p>
                </div>
            </div>
        `).join('');
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('dashboardStats')) {
        renderDashboardBranchTabs();
        renderDashboard();
    }
}, 200);