<div id="users" class="section hidden">
    <style>
        .user-inline-expand { animation: userSlideDown 220ms ease-out forwards; }
        @keyframes userSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت کاربران</h1>
            <p class="text-gray-500 mt-1">لیست کاربران، شعبه‌ها، نقش‌ها و دسترسی‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddUserModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-user-plus"></i> افزودن کاربر
            </button>
            <button onclick="exportUsersToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportUsersToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="usersBranchTabs">
            <button type="button" onclick="filterUsersByBranch('all')"
                    class="user-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">همه شعبه‌ها</button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="userSearch" placeholder="جستجو نام / موبایل / ایمیل..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterUsers()">
            <select id="filterUserRole" onchange="filterUsers()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه نقش‌ها</option>
            </select>
            <select id="filterUserStatus" onchange="filterUsers()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="معلق">معلق</option>
            </select>
            <select id="filterUserType" onchange="filterUsers()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="staff">پرسنل</option>
                <option value="student">هنرجو</option>
                <option value="parent">والد</option>
                <option value="admin">مدیر</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1300px]" id="usersTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('name')" class="flex items-center gap-1">نام <span id="userSortIcon-name">↕</span></button>
                        </th>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('phone')" class="flex items-center gap-1">موبایل <span id="userSortIcon-phone">↕</span></button>
                        </th>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('userTypeLabel')" class="flex items-center gap-1">نوع <span id="userSortIcon-userTypeLabel">↕</span></button>
                        </th>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('roleTitle')" class="flex items-center gap-1">نقش <span id="userSortIcon-roleTitle">↕</span></button>
                        </th>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('branchName')" class="flex items-center gap-1">شعبه <span id="userSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-4 px-4 font-medium">دسترسی‌ها</th>
                        <th class="text-right py-4 px-4 font-medium">
                            <button onclick="sortUsersBy('status')" class="flex items-center gap-1">وضعیت <span id="userSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="usersPaginationInfo">نمایش ۱ تا ۱۰ از ۰ کاربر</span>
            <div class="flex items-center gap-2" id="usersPaginationButtons"></div>
        </div>
    </div>
</div>
