<div id="dashboard" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">داشبورد مدیریت</h1>
            <p class="text-gray-500 mt-1">خلاصه فوری و مهم امروز</p>
        </div>
        <button type="button" onclick="refreshDashboard()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2 text-sm">
            <i class="fas fa-sync-alt"></i> به‌روزرسانی
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="dashboardBranchTabs">
            <button type="button" onclick="filterDashboardByBranch('all')"
                    class="dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">همه شعبه‌ها</button>
        </div>
    </div>

    <!-- کارت‌های آماری -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="dashboardStats"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- کلاس‌های امروز -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-day text-indigo-600"></i>
                    کلاس‌های امروز
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('member-schedules')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="todayClassesList"></div>
        </div>

        <!-- موارد فوری -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    موارد فوری
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('notifications')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="urgentItemsList"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- آخرین پرداخت‌ها -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-money-check text-emerald-600"></i>
                    آخرین پرداخت‌ها
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('reports')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="recentPaymentsList"></div>
        </div>

        <!-- آخرین واریزها -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-university text-teal-600"></i>
                    آخرین واریزها
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('reports')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="recentDepositsList"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- غیبت‌های امروز -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-user-times text-rose-600"></i>
                    غیبت‌های امروز
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('students')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="todayAbsencesList"></div>
        </div>

        <!-- پیام‌های خوانده‌نشده -->
        <div class="dashboard-unread-card bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <span class="dashboard-unread-icon inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-envelope"></i></span>
                    پیام‌های خوانده‌نشده
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('messages')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="dashboard-unread-list space-y-3" id="unreadMessagesList"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- ثبت‌نام‌های اخیر -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-user-plus text-violet-600"></i>
                    ثبت‌نام‌های اخیر
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('students')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="recentRegistrationsList"></div>
        </div>

        <!-- تعطیلات و مرخصی‌های نزدیک -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-times text-orange-500"></i>
                    تعطیلات و مرخصی‌های نزدیک
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('availabilities-exceptions')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="upcomingHolidaysList"></div>
        </div>
    </div>

    <!-- میانبرهای سریع -->
    <div class="bg-white rounded-3xl p-6 shadow">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i>
            میانبرهای سریع
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3" id="dashboardQuickLinks"></div>
    </div>
</div>
