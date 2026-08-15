(function () {
    'use strict';

    let dashboardData = null;
    let currentDashboardBranch = 'all';
    let loading = false;

    function endpoint() {
        const params = new URLSearchParams();
        if (currentDashboardBranch !== 'all') params.set('branchId', currentDashboardBranch);
        return '/analytics/admin-dashboard' + (params.size ? '?' + params : '');
    }

    async function requestDashboard() {
        const response = await fetch(endpoint(), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const payload = await response.json();
        const envelope = payload.data ?? payload;
        if (!response.ok || envelope.success === false) throw new Error(envelope.message || 'بارگذاری داشبورد ناموفق بود.');
        return envelope.data ?? envelope;
    }

    window.goToSectionFromDashboard = function (sectionId) {
        if (typeof window.showSection === 'function') return window.showSection(sectionId);
        document.querySelectorAll('.section').forEach(el => el.classList.add('hidden'));
        document.getElementById(sectionId)?.classList.remove('hidden');
    };

    window.renderDashboardBranchTabs = function () {
        const container = document.getElementById('dashboardBranchTabs');
        if (!container || !dashboardData) return;
        container.innerHTML = '';
        [{ id: 'all', name: 'همه شعبه‌ها' }, ...(dashboardData.branches || [])].forEach(branch => {
            const active = String(currentDashboardBranch) === String(branch.id);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
                (active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50');
            button.dataset.value = branch.id;
            button.textContent = branch.name;
            button.onclick = () => window.filterDashboardByBranch(branch.id);
            container.appendChild(button);
        });
    };

    window.filterDashboardByBranch = async function (branchId) {
        if (loading || String(branchId) === String(currentDashboardBranch)) return;
        currentDashboardBranch = branchId;
        await window.refreshDashboard();
    };

    window.renderDashboard = function () {
        if (!dashboardData) return;
        const setList = (id, html) => {
            const element = document.getElementById(id);
            if (element) element.innerHTML = html;
        };
        setList('dashboardStats', window.getDashboardStatsHTML?.(dashboardData.stats) || '');
        setList('todayClassesList', window.getDashboardTodayClassesHTML?.(dashboardData.todayClasses) || '');
        setList('urgentItemsList', window.getDashboardUrgentHTML?.(dashboardData.urgentItems) || '');
        setList('recentPaymentsList', window.getDashboardPaymentsHTML?.(dashboardData.recentPayments) || '');
        setList('recentDepositsList', window.getDashboardDepositsHTML?.(dashboardData.recentDeposits) || '');
        setList('todayAbsencesList', window.getDashboardAbsencesHTML?.(dashboardData.todayAbsences) || '');
        setList('unreadMessagesList', window.getDashboardMessagesHTML?.(dashboardData.unreadMessages) || '');
        setList('recentRegistrationsList', window.getDashboardRegistrationsHTML?.(dashboardData.recentRegistrations) || '');
        setList('upcomingHolidaysList', window.getDashboardHolidaysHTML?.(dashboardData.upcomingHolidays) || '');
        setList('dashboardQuickLinks', window.getDashboardQuickLinksHTML?.() || '');
    };

    window.refreshDashboard = async function () {
        if (loading) return;
        loading = true;
        const refreshIcon = document.querySelector('#dashboard button[onclick="refreshDashboard()"] i');
        refreshIcon?.classList.add('fa-spin');
        try {
            dashboardData = await requestDashboard();
            window.renderDashboardBranchTabs();
            window.renderDashboard();
        } catch (error) {
            alert(error.message || 'بارگذاری داشبورد ناموفق بود.');
        } finally {
            loading = false;
            refreshIcon?.classList.remove('fa-spin');
        }
    };

    setTimeout(() => {
        if (document.getElementById('dashboard')) window.refreshDashboard();
    }, 200);
})();
