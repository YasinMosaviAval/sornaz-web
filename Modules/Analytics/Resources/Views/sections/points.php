<div id="points" class="section hidden">
    <style>
        .point-inline-expand { animation: pointSlideDown 220ms ease-out forwards; }
        @keyframes pointSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">سیستم امتیازها</h1>
            <p class="text-gray-500 mt-1">تعریف و مدیریت قوانین امتیازدهی عمومی، تخصصی و اختصاصی</p>
        </div>
        <button onclick="openAddPointModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن قانون امتیاز
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="pointsBranchTabs">
            <button type="button" onclick="filterPointsByBranch('all')"
                    class="point-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="pointSearch" placeholder="جستجو عنوان / عملیات / خلاصه..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterPoints()">
            <select id="filterPointType" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="general">عمومی</option>
                <option value="specialized">تخصصی</option>
                <option value="custom">اختصاصی</option>
            </select>
            <select id="filterPointCategory" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه دسته‌ها</option>
                <option value="attendance">حضور</option>
                <option value="academic">آموزشی</option>
                <option value="event">رویداد</option>
                <option value="social">اجتماعی</option>
                <option value="financial">مالی</option>
                <option value="profile">پروفایل</option>
                <option value="achievement">دستاورد</option>
            </select>
            <select id="filterPointStatus" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]" id="pointsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('title')" class="flex items-center gap-1">عنوان <span id="pointSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('type')" class="flex items-center gap-1">نوع <span id="pointSortIcon-type">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('category')" class="flex items-center gap-1">دسته <span id="pointSortIcon-category">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('points')" class="flex items-center gap-1">امتیاز <span id="pointSortIcon-points">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('action')" class="flex items-center gap-1">عملیات <span id="pointSortIcon-action">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('branchName')" class="flex items-center gap-1">شعبه <span id="pointSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortPointsBy('status')" class="flex items-center gap-1">وضعیت <span id="pointSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="pointsPaginationInfo">نمایش ۱ تا ۱۰ از ۰ قانون</span>
            <div class="flex items-center gap-2" id="pointsPaginationButtons"></div>
        </div>
    </div>
</div>
