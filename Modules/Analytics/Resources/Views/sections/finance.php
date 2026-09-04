<div id="finance" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">امور مالی</h1>
            <p class="text-gray-500 mt-1">فاکتورهای ترم‌ها و وضعیت اقساط آن‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddTransactionModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> ثبت تراکنش
            </button>
            <button onclick="exportFinanceToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportFinanceToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <section class="mb-6 flex flex-col gap-3 rounded-3xl border border-indigo-100 bg-indigo-50/50 p-5 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="font-bold">اشتراک سامانه مدیریت آموزشگاه</h2><p class="mt-1 text-sm text-gray-500">ماه اول رایگان است؛ پلن‌های ۱ ماهه ۲ میلیون، ۳ ماهه ۵ میلیون، ۶ ماهه ۹ میلیون و یک‌ساله ۱۶ میلیون تومان هستند.</p></div>
        <span id="academySubscriptionPrice" class="whitespace-nowrap rounded-xl bg-white px-4 py-2 text-sm text-indigo-700">ماه اول رایگان</span>
    </section>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="financeBranchTabs">
            <button onclick="filterFinanceByBranch('all')"
                    class="finance-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- کارت‌های خلاصه -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8" id="financeSummaryCards"></div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="financeSearch" placeholder="جستجو نام ترم یا دوره..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterFinance()">
            <select id="filterFinanceStatus" onchange="filterFinance()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="پیش‌نویس">پیش‌نویس</option>
                <option value="صادرشده">صادرشده</option>
                <option value="پرداخت جزئی">پرداخت جزئی</option>
                <option value="پرداخت‌شده">پرداخت‌شده</option>
                <option value="ماه اول رایگان">ماه اول رایگان</option>
                <option value="در انتظار پرداخت">در انتظار پرداخت</option>
                <option value="منقضی‌شده">منقضی‌شده</option>
                <option value="لغوشده">لغوشده</option>
            </select>
            <select id="filterFinanceType" onchange="filterFinance()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="فاکتور ترم">فاکتور ترم</option>
                <option value="اشتراک سامانه">اشتراک سامانه</option>
            </select>
            <select id="filterFinanceRange" onchange="onFinanceRangeChange()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه زمان‌ها</option>
                <option value="weekly">هفتگی</option>
                <option value="monthly">ماهانه</option>
                <option value="yearly">سالانه</option>
                <option value="custom">انتخابی</option>
            </select>
        </div>
        <div id="financeCustomRangeBox" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">از تاریخ</label>
                <input type="date" id="filterFinanceFrom" onchange="filterFinance()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">تا تاریخ</label>
                <input type="date" id="filterFinanceTo" onchange="filterFinance()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4">
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="financeTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('title')" class="flex items-center gap-1">فاکتور <span id="financeSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('branchName')" class="flex items-center gap-1">شعبه <span id="financeSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('course')" class="flex items-center gap-1">دوره <span id="financeSortIcon-course">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('amount')" class="flex items-center gap-1">مبلغ <span id="financeSortIcon-amount">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('date')" class="flex items-center gap-1">تاریخ <span id="financeSortIcon-date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortFinanceBy('status')" class="flex items-center gap-1">وضعیت <span id="financeSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="financePaginationInfo">در حال دریافت فاکتورها...</span>
            <div class="flex items-center gap-2" id="financePaginationButtons"></div>
        </div>
    </div>
</div>
