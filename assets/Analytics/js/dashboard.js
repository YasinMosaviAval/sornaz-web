(function () {
    'use strict';

    let dashboardData = null;
    let currentDashboardBranch = 'all';
    let loading = false;
    let pendingDashboardBranch = null;

    function endpoint() {
        const params = new URLSearchParams();
        if (currentDashboardBranch !== 'all') params.set('branchId', currentDashboardBranch === 'academy' ? '0' : currentDashboardBranch);
        return '/analytics/admin-dashboard' + (params.size ? '?' + params : '');
    }

    async function requestDashboard() {
        const response = await fetch(endpoint(), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const raw = await response.text();
        let payload;
        try {
            payload = JSON.parse(raw);
        } catch (_) {
            throw new Error(response.redirected
                ? 'نشست شما منقضی شده است. لطفاً دوباره وارد شوید.'
                : 'پاسخ نامعتبر از سرور دریافت شد. لطفاً صفحه را دوباره بارگذاری کنید.');
        }
        const envelope = payload.data ?? payload;
        if (response.status === 401) {
            const loginUrl = envelope.loginUrl || '/system/login';
            window.location.assign(loginUrl);
            throw new Error(envelope.message || 'نشست شما منقضی شده است.');
        }
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
        container.dataset.selectedValue = String(currentDashboardBranch);
        container.innerHTML = '';
        [{ id: 'all', name: 'همه' }, ...(dashboardData.branches || [])].forEach(branch => {
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
        if (String(branchId) === String(currentDashboardBranch)) return;
        currentDashboardBranch = branchId;
        if (loading) {
            pendingDashboardBranch = branchId;
            window.renderDashboardBranchTabs();
            window.applyAcademyOrganizationTabs?.();
            return;
        }
        await window.refreshDashboard();
    };

    window.renderDashboard = function () {
        if (!dashboardData) return;
        const setList = (id, html) => {
            const element = document.getElementById(id);
            if (element) element.innerHTML = html;
        };
        setList('dashboardStats', window.getDashboardStatsHTML?.(dashboardData.stats) || '');
        setList('dashboardActionItems', window.getDashboardActionItemsHTML?.(dashboardData.actionItems) || '');
        window.dashboardActionItems=dashboardData.actionItems||[];
        const actionCount=document.getElementById('dashboardActionCount');if(actionCount)actionCount.textContent=String((dashboardData.actionItems||[]).length);
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
            if(!window.termPermissions&&typeof window.loadTerms==='function')await window.loadTerms();
            if(currentDashboardBranch==='academy'){
                ['todayClasses','actionItems','urgentItems','recentPayments','recentDeposits','todayAbsences','unreadMessages','recentRegistrations','upcomingHolidays'].forEach(key=>{dashboardData[key]=(dashboardData[key]||[]).filter(item=>window.matchesOrganizationFilter(item,'academy'));});
                dashboardData.stats={...(dashboardData.stats||{}),activeStudents:0,todayClasses:0,monthlyIncome:'0',attendanceRate:'۰٪',pendingPayments:0,absencesToday:0,newMessages:dashboardData.unreadMessages.length,urgentAlerts:dashboardData.urgentItems.length,newStudentsWeek:0,pointsAwarded:0};
            }
            window.renderDashboardBranchTabs();
            window.renderDashboard();
        } catch (error) {
            alert(error.message || 'بارگذاری داشبورد ناموفق بود.');
        } finally {
            loading = false;
            refreshIcon?.classList.remove('fa-spin');
            if (pendingDashboardBranch !== null) {
                pendingDashboardBranch = null;
                window.refreshDashboard();
            }
        }
    };

    window.openDashboardHolidayConflict=async function(termId,sessionId){if(typeof window.loadTerms==='function')await window.loadTerms();await window.openTermSessionCancellation?.(termId,sessionId);const item=(window.dashboardActionItems||[]).find(row=>row.type==='national_holiday_conflict'&&row.termId===termId&&row.sessionId===sessionId),reason=document.getElementById('termCancellationReason');if(reason&&item){const description=String(item.holidayDescription||'').trim();reason.value=`تعطیلی رسمی «${item.holidayTitle}» در تاریخ ${window.formatLocalizedDate?.(item.date)||item.date}${description?' — '+description:''}`;reason.dispatchEvent(new Event('input',{bubbles:true}));}};
    window.decideDashboardCancellation=async function(termId,sessionId,approve){await window.decideTermSessionCancellation?.(termId,sessionId,approve);window.closeModal?.();await window.refreshDashboard();};

    setTimeout(() => {
        if (document.getElementById('dashboard')) window.refreshDashboard();
    }, 200);
    const dashboardHolidayChannel='BroadcastChannel' in window?new BroadcastChannel('sornaz-national-holidays'):null;
    const refreshForHolidayChange=()=>{if(document.getElementById('dashboard')&&!document.getElementById('dashboard').classList.contains('hidden'))window.refreshDashboard();};
    window.addEventListener('sornaz:data-changed',event=>{if(event.detail?.resource==='national_holidays')refreshForHolidayChange();});
    if(dashboardHolidayChannel)dashboardHolidayChannel.onmessage=event=>{if(event.data?.resource==='national_holidays')refreshForHolidayChange();};
    setInterval(refreshForHolidayChange,2000);
})();
