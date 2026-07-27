<div id="reports" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">گزارش‌های آموزشگاه</h1>
            <p class="text-gray-500 mt-1">گزارش‌های آماری و عملکردی شعبه‌ها</p>
        </div>
        <button onclick="exportReports()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-file-excel text-green-600"></i>
            خروجی اکسل
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="reportsBranchTabs">
            <button onclick="filterReportsByBranch('all')" 
                    class="report-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- کارت‌های خلاصه -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="reportsSummaryCards">
        <!-- توسط JS پر می‌شود -->
    </div>

    <!-- جدول گزارش‌ها -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]" id="reportsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">عنوان گزارش</th>
                        <th class="text-right py-5 px-5 font-medium">شعبه</th>
                        <th class="text-right py-5 px-5 font-medium">نوع</th>
                        <th class="text-right py-5 px-5 font-medium">تاریخ</th>
                        <th class="text-right py-5 px-5 font-medium">وضعیت</th>
                        <th class="w-32 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>