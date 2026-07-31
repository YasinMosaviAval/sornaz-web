<div id="scheduling-rules" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">قوانین زمان‌بندی</h1>
            <p class="text-gray-500 mt-1">قوانین رزرو، لغو و تغییر زمان کلاس‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddRuleModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن قانون
            </button>
            <button onclick="exportRulesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportRulesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="rulesBranchTabs">
            <button onclick="filterRulesByBranch('all')"
                    class="rule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="ruleSearch" placeholder="جستجو عنوان / مقدار..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterRules()">
            <select id="filterRuleStatus" onchange="filterRules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="در انتظار تأیید">در انتظار تأیید</option>
                <option value="حذف‌شده">حذف‌شده</option>
            </select>
            <select id="filterRuleType" onchange="filterRules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="rulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortRulesBy('title')" class="flex items-center gap-1">عنوان قانون <span id="ruleSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortRulesBy('branchName')" class="flex items-center gap-1">شعبه <span id="ruleSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortRulesBy('type')" class="flex items-center gap-1">نوع <span id="ruleSortIcon-type">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortRulesBy('value')" class="flex items-center gap-1">مقدار <span id="ruleSortIcon-value">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortRulesBy('status')" class="flex items-center gap-1">وضعیت <span id="ruleSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="rulesPaginationInfo">نمایش ۱ تا ۱۰ از ۳۰ قانون</span>
            <div class="flex items-center gap-2" id="rulesPaginationButtons"></div>
        </div>
    </div>
</div>
