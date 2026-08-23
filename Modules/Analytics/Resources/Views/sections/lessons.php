<div id="lessons" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">درس‌ها</h1>
            <p class="text-gray-500 mt-1">درس‌ها و سطح مهارت اعضا</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddLessonModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن درس
            </button>
            <button onclick="exportLessonsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportLessonsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="lessonsBranchTabs">
            <button onclick="filterLessonsByBranch('all')" class="lesn-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="lessonSearch" placeholder="جستجو نام درس..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterLessons()">
            <select id="filterLessonStatus" onchange="filterLessons()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="active">فعال</option>
                <option value="inactive">غیرفعال</option>
                <option value="pending">در انتظار</option>
                <option value="removed">حذف‌شده</option>
            </select>
            <select id="filterLessonLevel" onchange="filterLessons()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه سطوح</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="lessonsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('title')" class="flex items-center gap-1">عنوان / درس <span id="lesnSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('levelTitle')" class="flex items-center gap-1">سطح <span id="lesnSortIcon-levelTitle">↕</span></button>
                        </th>
                        <th class="text-center py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('start_date')" class="flex w-full items-center justify-center gap-1">زمان شروع <span id="lesnSortIcon-start_date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('is_primary')" class="flex items-center gap-1">اصلی <span id="lesnSortIcon-is_primary">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('status')" class="flex items-center gap-1">وضعیت <span id="lesnSortIcon-status">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortLessonsBy('branchName')" class="flex items-center gap-1">سازمان <span id="lesnSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="lessonsPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ مورد</span>
            <div class="flex items-center gap-2" id="lessonsPaginationButtons"></div>
        </div>
    </div>
</div>
