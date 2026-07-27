<div id="dashboard" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">داشبورد مدیریت</h1>
            <p class="text-gray-500 mt-1">خلاصه فوری و مهم امروز</p>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="dashboardBranchTabs">
            <button onclick="filterDashboardByBranch('all')" 
                    class="dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- کارت‌های آماری فوری -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="dashboardStats">
        <!-- توسط JS پر می‌شود -->
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- کلاس‌های امروز -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-day text-indigo-600"></i>
                کلاس‌های امروز
            </h2>
            <div class="space-y-3" id="todayClassesList"></div>
        </div>

        <!-- اعلان‌ها و هشدارهای فوری -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                موارد فوری
            </h2>
            <div class="space-y-3" id="urgentItemsList"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخرین پرداخت‌ها -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-money-check text-emerald-600"></i>
                آخرین پرداخت‌ها
            </h2>
            <div class="space-y-3" id="recentPaymentsList"></div>
        </div>

        <!-- غیبت‌های امروز -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-user-times text-rose-600"></i>
                غیبت‌های امروز
            </h2>
            <div class="space-y-3" id="todayAbsencesList"></div>
        </div>
    </div>
</div>