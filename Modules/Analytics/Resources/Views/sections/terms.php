<div id="terms" class="section hidden">
    <style>
        .term-inline-expand { animation: termSlideDown 220ms ease-out forwards; }
        @keyframes termSlideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت ترم‌ها</h1>
            <p class="text-gray-500 mt-1">ترم‌های در حال برگزاری، پایان‌یافته و تعلیق‌شده</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddTermModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-plus"></i> افزودن ترم جدید
            </button>
            <button onclick="exportTermsToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportTermsToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="termsBranchTabs">
            <button onclick="filterTermsByBranch('all')"
                    class="term-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="termSearch" placeholder="جستجو نام ترم..."
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                       onkeyup="filterTerms()">
            </div>
            <div>
                <select id="filterTermStatus" onchange="filterTerms()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="در حال برگزاری">در حال برگزاری</option>
                    <option value="در انتظار">در انتظار</option>
                    <option value="پایان‌یافته">پایان‌یافته</option>
                    <option value="تعلیق‌شده">تعلیق‌شده</option>
                </select>
            </div>
            <div>
                <select id="filterTermCourse" onchange="filterTerms()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه دوره‌ها</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="termsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('name')" class="flex items-center gap-1">
                                نام ترم <span id="termSortIcon-name">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('branchName')" class="flex items-center gap-1">
                                شعبه <span id="termSortIcon-branchName">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('course')" class="flex items-center gap-1">
                                دوره مرتبط <span id="termSortIcon-course">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('start')" class="flex items-center gap-1">
                                تاریخ شروع <span id="termSortIcon-start">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('end')" class="flex items-center gap-1">
                                تاریخ پایان <span id="termSortIcon-end">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortTermsBy('status')" class="flex items-center gap-1">
                                وضعیت <span id="termSortIcon-status">↕</span>
                            </button>
                        </th>
                        <th class="w-52 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="termsPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ ترم</span>
            <div class="flex items-center gap-2" id="termsPaginationButtons"></div>
        </div>
    </div>
</div>
