<div id="permissions" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت دسترسی‌ها</h1>
            <p class="text-gray-500 mt-1">مجوزها و دسترسی‌های نقش‌ها در شعبه‌ها</p>
        </div>

        <button onclick="showSection('profiles')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-id-card"></i> پروفایل‌ها
        </button>
        <button onclick="showSection('roles')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-user-tag"></i> نقش‌ها
        </button>

        <button onclick="openAddPermissionModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن دسترسی
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="permissionsBranchTabs">
            <button onclick="filterPermissionsByBranch('all')" 
                    class="permission-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- جدول دسترسی‌ها -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="permissionsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-4 font-medium">نام</th>
                        <th class="text-right py-4 px-4 font-medium">عنوان</th>
                        <th class="text-right py-4 px-4 font-medium">عنوان انگلیسی</th>
                        <th class="text-right py-4 px-4 font-medium">گروه</th>
                        <th class="text-right py-4 px-4 font-medium">شعبه</th>
                        <th class="w-28 py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>