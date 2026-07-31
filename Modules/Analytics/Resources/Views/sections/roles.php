<div id="roles" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت نقش‌ها</h1>
            <p class="text-gray-500 mt-1">نقش‌های کاربران در شعبه‌های مختلف</p>
        </div>
        <button onclick="openAddRoleModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن نقش
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="rolesBranchTabs">
            <button onclick="filterRolesByBranch('all')" 
                    class="role-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- جدول نقش‌ها -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="rolesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-4 font-medium">نام</th>
                        <th class="text-right py-4 px-4 font-medium">عنوان</th>
                        <th class="text-right py-4 px-4 font-medium">عنوان انگلیسی</th>
                        <th class="text-right py-4 px-4 font-medium">نوع</th>
                        <th class="text-right py-4 px-4 font-medium">رنگ</th>
                        <th class="text-right py-4 px-4 font-medium">ترتیب</th>
                        <th class="text-right py-4 px-4 font-medium">شعبه</th>
                        <th class="w-28 py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>