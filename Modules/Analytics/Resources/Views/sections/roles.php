<div id="roles" class="section hidden">
    <style>
        .role-inline-expand { animation: roleSlideDown 220ms ease-out forwards; }
        @keyframes roleSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت نقش‌ها</h1>
            <p class="text-gray-500 mt-1">نقش‌های کاربران در شعبه‌های مختلف</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddRoleModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن نقش
            </button>
            <button onclick="exportRolesToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportRolesToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="rolesBranchTabs">
            <button type="button" onclick="filterRolesByBranch('all')" class="role-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white" data-value="all">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <input type="text" id="roleSearch" placeholder="جستجو نام / عنوان نقش..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterRoles()">
            <select id="filterRoleType" onchange="filterRoles()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="سیستم">سیستم</option>
                <option value="سفارشی">سفارشی</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="rolesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('name')" class="flex items-center gap-1">نام <span id="roleSortIcon-name">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('title')" class="flex items-center gap-1">عنوان <span id="roleSortIcon-title">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('title_en')" class="flex items-center gap-1">عنوان انگلیسی <span id="roleSortIcon-title_en">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('type')" class="flex items-center gap-1">نوع <span id="roleSortIcon-type">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('color')" class="flex items-center gap-1">رنگ <span id="roleSortIcon-color">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('order')" class="flex items-center gap-1">ترتیب <span id="roleSortIcon-order">↕</span></button></th>
                        <th class="text-right py-4 px-4 font-medium"><button onclick="sortRolesBy('branchName')" class="flex items-center gap-1">شعبه <span id="roleSortIcon-branchName">↕</span></button></th>
                        <th class="w-40 py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="rolesPaginationInfo">نمایش ۱ تا ۱۰ از ۰ نقش</span>
            <div class="flex items-center gap-2" id="rolesPaginationButtons"></div>
        </div>
    </div>
</div>
