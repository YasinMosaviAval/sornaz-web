<div id="permissions" class="section hidden">
    <style>
        .permission-inline-expand { animation: permSlideDown 220ms ease-out forwards; }
        @keyframes permSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت دسترسی‌ها</h1>
            <p class="text-gray-500 mt-1">مجوزها و دسترسی‌های نقش‌ها در شعبه‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddPermissionModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن دسترسی
            </button>
            <button onclick="exportPermissionsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportPermissionsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="permissionsBranchTabs">
            <button type="button" onclick="filterPermissionsByBranch('all')" class="permission-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white" data-value="all">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <input type="text" id="permissionSearch" placeholder="جستجو نام / عنوان دسترسی..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterPermissions()">
            <select id="filterPermissionGroup" onchange="filterPermissions()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه گروه‌ها</option>
                <option value="زبان">زبان</option>
                <option value="درس">درس</option>
                <option value="ابزار">ابزار</option>
                <option value="هنرجو">هنرجو</option>
                <option value="استاد">استاد</option>
                <option value="مالی">مالی</option>
                <option value="گزارش">گزارش</option>
                <option value="عمومی">عمومی</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="permissionsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortPermissionsBy('name')" class="flex items-center gap-1">نام <span id="permSortIcon-name">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortPermissionsBy('title')" class="flex items-center gap-1">عنوان <span id="permSortIcon-title">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortPermissionsBy('title_en')" class="flex items-center gap-1">عنوان انگلیسی <span id="permSortIcon-title_en">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortPermissionsBy('group')" class="flex items-center gap-1">گروه <span id="permSortIcon-group">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortPermissionsBy('branchName')" class="flex items-center gap-1">شعبه <span id="permSortIcon-branchName">↕</span></button></th>
                        <th class="w-40 py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="permissionsPaginationInfo">نمایش ۱ تا ۱۰ از ۰ دسترسی</span>
            <div class="flex items-center gap-2" id="permissionsPaginationButtons"></div>
        </div>
    </div>
</div>
