<div id="dashboard" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">داشبورد مدیریت</h1>
            <p class="text-gray-500 mt-1">خلاصه فوری و مهم امروز</p>
        </div>
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

    <div class="mb-8 overflow-hidden rounded-3xl border border-red-100 bg-white shadow">
        <div class="flex items-center justify-between border-b border-red-100 bg-red-50/60 p-6"><div><h2 class="text-xl font-semibold text-red-800"><i class="fas fa-tasks ml-2"></i>اقدامات نیازمند بررسی</h2><p class="mt-1 text-sm text-red-600">تمام مواردی که به تأیید یا تعیین تاریخ جدید نیاز دارند</p></div><span id="dashboardActionCount" class="rounded-full bg-red-600 px-3 py-1 text-sm text-white">۰</span></div>
        <div id="dashboardActionItems" class="grid gap-4 p-6 xl:grid-cols-2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-day text-indigo-600"></i>
                    کلاس‌های امروز
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('schedules')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="todayClassesList"></div>
        </div>

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

        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-times text-orange-500"></i>
                    تعطیلات و مرخصی‌های نزدیک
                </h2>
                <button type="button" onclick="goToSectionFromDashboard('availability-exceptions')" class="text-indigo-600 text-sm hover:underline">مشاهده همه</button>
            </div>
            <div class="space-y-3" id="upcomingHolidaysList"></div>
        </div>

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
</div>
