(function () {
'use strict';
// ==================== داشبورد مدیریت ====================

window.getDashboardBranches = function () {
    if (typeof allBranches !== 'undefined' && allBranches.length) return allBranches;
    return [
        { id: 1, name: 'شعبه مرکزی' },
        { id: 2, name: 'شعبه ونک' },
        { id: 3, name: 'شعبه سعادت‌آباد' },
        { id: 4, name: 'شعبه کرج' }
    ];
};

/** داده پایه (همه شعبه‌ها) */
const dashboardBase = {
    stats: {
        activeStudents: 248,
        todayClasses: 18,
        monthlyIncome: '۴۸.۵ میلیون',
        attendanceRate: '۹۲٪',
        pendingPayments: 12,
        absencesToday: 5,
        newMessages: 3,
        urgentAlerts: 4,
        newStudentsWeek: 9,
        pointsAwarded: 1560
    },
    todayClasses: [
        { time: '۱۰:۰۰', student: 'سارا احمدی', instrument: 'پیانو', teacher: 'استاد موسوی', branch: 'شعبه مرکزی', branchId: 1 },
        { time: '۱۱:۳۰', student: 'امیر حسینی', instrument: 'گیتار', teacher: 'استاد رضایی', branch: 'شعبه مرکزی', branchId: 1 },
        { time: '۱۴:۰۰', student: 'زهرا کریمی', instrument: 'ویولن', teacher: 'استاد بهرامی', branch: 'شعبه ونک', branchId: 2 },
        { time: '۱۶:۰۰', student: 'گروه ۶ نفره', instrument: 'آواز', teacher: 'استاد کاظمی', branch: 'شعبه سعادت‌آباد', branchId: 3 },
        { time: '۱۸:۰۰', student: 'علی محمدی', instrument: 'درام', teacher: 'استاد نوری', branch: 'شعبه کرج', branchId: 4 },
        { time: '۱۹:۰۰', student: 'نگار رضایی', instrument: 'فلوت', teacher: 'استاد احمدی', branch: 'شعبه ونک', branchId: 2 }
    ],
    urgentItems: [
        { type: 'بدهی', text: '۱۲ هنرجو بدهکار — مجموع ۸.۵ میلیون تومان', color: 'rose', section: 'students', branchId: null },
        { type: 'غیبت', text: '۵ غیبت ثبت‌شده امروز', color: 'amber', section: 'students', branchId: null },
        { type: 'پیام', text: '۳ پیام خوانده‌نشده', color: 'indigo', section: 'messages', branchId: null },
        { type: 'کلاس', text: '۲ کلاس بدون استاد تا ۱ ساعت دیگر', color: 'red', section: 'member-schedules', branchId: 1 },
        { type: 'اعلان', text: '۱ اعلان سیستمی با اولویت بالا', color: 'amber', section: 'notifications', branchId: null }
    ],
    recentPayments: [
        { name: 'سارا احمدی', amount: '۱,۲۰۰,۰۰۰', time: '۱۰ دقیقه پیش', branch: 'شعبه مرکزی', branchId: 1 },
        { name: 'امیر حسینی', amount: '۹۵۰,۰۰۰', time: '۱ ساعت پیش', branch: 'شعبه ونک', branchId: 2 },
        { name: 'نگار رضایی', amount: '۱,۱۰۰,۰۰۰', time: '۳ ساعت پیش', branch: 'شعبه سعادت‌آباد', branchId: 3 },
        { name: 'پارسا نوری', amount: '۸۰۰,۰۰۰', time: 'دیروز', branch: 'شعبه کرج', branchId: 4 }
    ],
    recentDeposits: [
        { title: 'واریز بانکی هنرجو', amount: '۳,۵۰۰,۰۰۰', bank: 'ملت', time: '۳۰ دقیقه پیش', branch: 'شعبه مرکزی', branchId: 1 },
        { title: 'واریز کارت به کارت', amount: '۱,۲۰۰,۰۰۰', bank: 'سامان', time: '۲ ساعت پیش', branch: 'شعبه ونک', branchId: 2 },
        { title: 'واریز شهریه گروهی', amount: '۴,۸۰۰,۰۰۰', bank: 'ملی', time: 'امروز', branch: 'شعبه سعادت‌آباد', branchId: 3 },
        { title: 'واریز پیش‌پرداخت', amount: '۲,۰۰۰,۰۰۰', bank: 'پاسارگاد', time: 'دیروز', branch: 'شعبه کرج', branchId: 4 }
    ],
    todayAbsences: [
        { student: 'علی محمدی', instrument: 'پیانو', teacher: 'استاد موسوی', time: '۱۰:۰۰', branch: 'شعبه مرکزی', branchId: 1 },
        { student: 'مهسا جعفری', instrument: 'گیتار', teacher: 'استاد رضایی', time: '۱۱:۳۰', branch: 'شعبه ونک', branchId: 2 },
        { student: 'پارسا نوری', instrument: 'ویولن', teacher: 'استاد بهرامی', time: '۱۴:۰۰', branch: 'شعبه سعادت‌آباد', branchId: 3 }
    ],
    unreadMessages: [
        { from: 'سارا احمدی', preview: 'سلام، امکان تغییر ساعت کلاس هست؟', time: '۱۵ دقیقه پیش', branch: 'شعبه مرکزی', branchId: 1 },
        { from: 'استاد موسوی', preview: 'تأیید برنامه هفته آینده', time: '۱ ساعت پیش', branch: 'شعبه مرکزی', branchId: 1 },
        { from: 'والد امیر', preview: 'سؤال درباره شهریه', time: '۳ ساعت پیش', branch: 'شعبه ونک', branchId: 2 }
    ],
    recentRegistrations: [
        { name: 'هستی کاظمی', instrument: 'پیانو', date: 'امروز', branch: 'شعبه مرکزی', branchId: 1 },
        { name: 'کیان حیدری', instrument: 'گیتار', date: 'دیروز', branch: 'شعبه ونک', branchId: 2 },
        { name: 'آرین جعفری', instrument: 'آواز', date: '۲ روز پیش', branch: 'شعبه سعادت‌آباد', branchId: 3 }
    ],
    upcomingHolidays: [
        { title: 'تعطیلی رسمی', date: '۱۴۰۴/۰۵/۱۵', branch: 'همه شعبه‌ها', branchId: null },
        { title: 'مرخصی استاد موسوی', date: '۱۴۰۴/۰۵/۱۸', branch: 'شعبه مرکزی', branchId: 1 },
        { title: 'تعمیرات شعبه ونک', date: '۱۴۰۴/۰۵/۲۰', branch: 'شعبه ونک', branchId: 2 }
    ]
};

/** آمار تقریبی به‌ازای هر شعبه */
const branchStatsMap = {
    1: { activeStudents: 95, todayClasses: 7, monthlyIncome: '۱۸.۲ میلیون', attendanceRate: '۹۴٪', pendingPayments: 4, absencesToday: 2, newMessages: 2, urgentAlerts: 2, newStudentsWeek: 4, pointsAwarded: 520 },
    2: { activeStudents: 68, todayClasses: 5, monthlyIncome: '۱۴.۱ میلیون', attendanceRate: '۹۱٪', pendingPayments: 3, absencesToday: 1, newMessages: 1, urgentAlerts: 1, newStudentsWeek: 2, pointsAwarded: 410 },
    3: { activeStudents: 52, todayClasses: 4, monthlyIncome: '۱۰.۵ میلیون', attendanceRate: '۹۰٪', pendingPayments: 3, absencesToday: 1, newMessages: 0, urgentAlerts: 1, newStudentsWeek: 2, pointsAwarded: 380 },
    4: { activeStudents: 33, todayClasses: 2, monthlyIncome: '۵.۷ میلیون', attendanceRate: '۸۸٪', pendingPayments: 2, absencesToday: 1, newMessages: 0, urgentAlerts: 0, newStudentsWeek: 1, pointsAwarded: 250 }
};

let currentDashboardBranch = 'all';

function byBranch(list, branchId) {
    if (branchId === 'all' || branchId == null) return list.slice();
    return list.filter(function (item) {
        return item.branchId == null || String(item.branchId) === String(branchId);
    });
}

function getFilteredDashboard() {
    const b = currentDashboardBranch;
    const stats = b === 'all' ? dashboardBase.stats : (branchStatsMap[b] || dashboardBase.stats);
    return {
        stats: stats,
        todayClasses: byBranch(dashboardBase.todayClasses, b),
        urgentItems: byBranch(dashboardBase.urgentItems, b),
        recentPayments: byBranch(dashboardBase.recentPayments, b),
        recentDeposits: byBranch(dashboardBase.recentDeposits, b),
        todayAbsences: byBranch(dashboardBase.todayAbsences, b),
        unreadMessages: byBranch(dashboardBase.unreadMessages, b),
        recentRegistrations: byBranch(dashboardBase.recentRegistrations, b),
        upcomingHolidays: byBranch(dashboardBase.upcomingHolidays, b)
    };
}

window.goToSectionFromDashboard = function (sectionId) {
    if (typeof window.showSection === 'function') {
        window.showSection(sectionId);
    } else {
        // fallback: نمایش سکشن
        document.querySelectorAll('.section').forEach(function (el) { el.classList.add('hidden'); });
        const el = document.getElementById(sectionId);
        if (el) el.classList.remove('hidden');
    }
};

window.renderDashboardBranchTabs = function () {
    const container = document.getElementById('dashboardBranchTabs');
    if (!container) return;
    container.querySelectorAll('.dashboard-branch-tab:not([data-value="all"])').forEach(function (t) { t.remove(); });
    window.getDashboardBranches().forEach(function (b) {
        const active = String(currentDashboardBranch) === String(b.id);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
            (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
        btn.dataset.value = b.id;
        btn.textContent = b.name;
        btn.onclick = function () { window.filterDashboardByBranch(b.id); };
        container.appendChild(btn);
    });
    const allTab = container.querySelector('[data-value="all"]');
    if (allTab) {
        const isAll = currentDashboardBranch === 'all';
        allTab.classList.toggle('bg-indigo-600', isAll);
        allTab.classList.toggle('text-white', isAll);
        allTab.classList.toggle('border-indigo-600', isAll);
        if (!isAll) {
            allTab.classList.add('border', 'border-gray-200');
            allTab.classList.remove('bg-indigo-600', 'text-white');
        }
    }
};

window.filterDashboardByBranch = function (branchId) {
    currentDashboardBranch = branchId;
    document.querySelectorAll('.dashboard-branch-tab').forEach(function (tab) {
        const active = String(tab.dataset.value) === String(branchId);
        tab.classList.toggle('bg-indigo-600', active);
        tab.classList.toggle('text-white', active);
        tab.classList.toggle('border-indigo-600', active);
        if (!active) {
            tab.classList.add('border', 'border-gray-200');
            tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        } else tab.classList.remove('border-gray-200');
    });
    window.renderDashboard();
};

window.renderDashboard = function () {
    const data = getFilteredDashboard();

    const statsEl = document.getElementById('dashboardStats');
    if (statsEl && window.getDashboardStatsHTML) {
        statsEl.innerHTML = window.getDashboardStatsHTML(data.stats);
    }

    const setList = function (id, html) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
    };

    setList('todayClassesList', window.getDashboardTodayClassesHTML ? window.getDashboardTodayClassesHTML(data.todayClasses) : '');
    setList('urgentItemsList', window.getDashboardUrgentHTML ? window.getDashboardUrgentHTML(data.urgentItems) : '');
    setList('recentPaymentsList', window.getDashboardPaymentsHTML ? window.getDashboardPaymentsHTML(data.recentPayments) : '');
    setList('recentDepositsList', window.getDashboardDepositsHTML ? window.getDashboardDepositsHTML(data.recentDeposits) : '');
    setList('todayAbsencesList', window.getDashboardAbsencesHTML ? window.getDashboardAbsencesHTML(data.todayAbsences) : '');
    setList('unreadMessagesList', window.getDashboardMessagesHTML ? window.getDashboardMessagesHTML(data.unreadMessages) : '');
    setList('recentRegistrationsList', window.getDashboardRegistrationsHTML ? window.getDashboardRegistrationsHTML(data.recentRegistrations) : '');
    setList('upcomingHolidaysList', window.getDashboardHolidaysHTML ? window.getDashboardHolidaysHTML(data.upcomingHolidays) : '');
    setList('dashboardQuickLinks', window.getDashboardQuickLinksHTML ? window.getDashboardQuickLinksHTML() : '');
};

window.refreshDashboard = function () {
    window.renderDashboard();
};

setTimeout(function () {
    if (document.getElementById('dashboardStats') || document.getElementById('dashboard')) {
        window.renderDashboardBranchTabs();
        window.renderDashboard();
    }
}, 200);
})();
