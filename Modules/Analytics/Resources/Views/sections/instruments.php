<div id="instruments" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">سازها</h1>
            <p class="text-gray-500 mt-1">سازها و سطح مهارت اعضا</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddInstrumentModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن ساز
            </button>
            <button onclick="exportInstrumentsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportInstrumentsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="instrumentsBranchTabs">
            <button onclick="filterInstrumentsByBranch('all')" class="inst-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="instrumentSearch" placeholder="جستجو نام ساز..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterInstruments()">
            <select id="filterInstrumentStatus" onchange="filterInstruments()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="در انتظار">در انتظار</option>
                <option value="حذف‌شده">حذف‌شده</option>
            </select>
            <select id="filterInstrumentLevel" onchange="filterInstruments()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه سطوح</option>
                <option value="1">مبتدی</option>
                <option value="2">متوسط</option>
                <option value="3">پیشرفته</option>
                <option value="4">حرفه‌ای</option>
                <option value="5">کارشناسی</option>
                <option value="6">کارشناسی ارشد</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="instrumentsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('title')" class="flex items-center gap-1">عنوان / ساز <span id="instSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('levelTitle')" class="flex items-center gap-1">سطح <span id="instSortIcon-levelTitle">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('years_of_experience')" class="flex items-center gap-1">سابقه <span id="instSortIcon-years_of_experience">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('is_primary')" class="flex items-center gap-1">اصلی <span id="instSortIcon-is_primary">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('status')" class="flex items-center gap-1">وضعیت <span id="instSortIcon-status">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortInstrumentsBy('branchName')" class="flex items-center gap-1">شعبه <span id="instSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="instrumentsPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ مورد</span>
            <div class="flex items-center gap-2" id="instrumentsPaginationButtons"></div>
        </div>
    </div>
</div>
