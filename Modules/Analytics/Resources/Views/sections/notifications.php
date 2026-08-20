<div id="notifications" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت اعلان‌ها</h1>
            <p class="text-gray-500 mt-1">اعلان‌های سیستمی و اختصاصی شعبه‌ها</p>
        </div>
        <button onclick="openAddNotificationModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> ثبت اعلان جدید
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="notificationsBranchTabs">
            <button type="button" onclick="filterNotificationsByBranch('all')"
                    class="notification-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="notificationSearch" placeholder="جستجو عنوان / متن اعلان..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterNotifications()">
            <select id="filterNotificationStatus" onchange="filterNotifications()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="منتشر شده">منتشر شده</option>
                <option value="پیش‌نویس">پیش‌نویس</option>
                <option value="منقضی">منقضی</option>
            </select>
            <select id="filterNotificationPriority" onchange="filterNotifications()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه اولویت‌ها</option>
                <option value="بالا">بالا</option>
                <option value="متوسط">متوسط</option>
                <option value="کم">کم</option>
            </select>
            <select id="filterNotificationAudience" onchange="filterNotifications()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه مخاطبان</option>
                <option value="همه">همه</option>
                <option value="هنرجویان">هنرجویان</option>
                <option value="اساتید">اساتید</option>
                <option value="والدین">والدین</option>
                <option value="پرسنل">پرسنل</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="notificationsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('title')" class="flex items-center gap-1">عنوان اعلان <span id="notifSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">گیرنده</th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('branchName')" class="flex items-center gap-1">شعبه <span id="notifSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('audience')" class="flex items-center gap-1">مخاطب <span id="notifSortIcon-audience">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('priority')" class="flex items-center gap-1">اولویت <span id="notifSortIcon-priority">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('date')" class="flex items-center gap-1">تاریخ انتشار <span id="notifSortIcon-date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortNotificationsBy('status')" class="flex items-center gap-1">وضعیت <span id="notifSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="notificationsPaginationInfo">نمایش ۱ تا ۱۰ از ۰ اعلان</span>
            <div class="flex items-center gap-2" id="notificationsPaginationButtons"></div>
        </div>
    </div>
</div>
