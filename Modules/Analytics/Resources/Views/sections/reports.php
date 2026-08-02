<div id="reports" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">گزارش‌های آموزشگاه</h1>
            <p class="text-gray-500 mt-1">گزارش‌های آماری و عملکردی شعبه‌ها (تولید خودکار سیستم)</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportReportsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportReportsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="reportsBranchTabs">
            <button type="button" onclick="filterReportsByBranch('all')"
                    class="report-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">همه شعبه‌ها</button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <input type="text" id="reportSearch" placeholder="جستجو عنوان گزارش..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterReports()">
            <select id="filterReportType" onchange="filterReports()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="حضور و غیاب">حضور و غیاب</option>
                <option value="مالی">مالی</option>
                <option value="ثبت‌نام">ثبت‌نام</option>
                <option value="آموزشی">آموزشی</option>
                <option value="نظرسنجی">نظرسنجی</option>
                <option value="عملکرد">عملکرد</option>
            </select>
            <select id="filterReportStatus" onchange="filterReports()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="آماده">آماده</option>
                <option value="در حال تهیه">در حال تهیه</option>
            </select>
            <select id="filterReportPeriod" onchange="onReportPeriodChange()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه زمان‌ها</option>
                <option value="weekly">هفتگی</option>
                <option value="monthly">ماهانه</option>
                <option value="yearly">سالانه</option>
                <option value="custom">زمان انتخابی</option>
            </select>
            <div id="reportCustomDateFromWrap" class="hidden">
                <input type="date" id="reportDateFrom" onchange="filterReports()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4" title="از تاریخ">
            </div>
            <div id="reportCustomDateToWrap" class="hidden">
                <input type="date" id="reportDateTo" onchange="filterReports()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4" title="تا تاریخ">
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="reportsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('title')" class="flex items-center gap-1">عنوان گزارش <span id="reportSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('branchName')" class="flex items-center gap-1">شعبه <span id="reportSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('type')" class="flex items-center gap-1">نوع <span id="reportSortIcon-type">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('periodLabel')" class="flex items-center gap-1">بازه زمانی <span id="reportSortIcon-periodLabel">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('date')" class="flex items-center gap-1">تاریخ <span id="reportSortIcon-date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortReportsBy('status')" class="flex items-center gap-1">وضعیت <span id="reportSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-32 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="reportsPaginationInfo">نمایش ۱ تا ۱۰ از ۰ گزارش</span>
            <div class="flex items-center gap-2" id="reportsPaginationButtons"></div>
        </div>
    </div>
</div>
