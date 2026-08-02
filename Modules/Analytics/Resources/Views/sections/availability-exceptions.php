<div id="availability-exceptions" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">تعطیلات و مرخصی‌ها</h1>
            <p class="text-gray-500 mt-1">ثبت تعطیلات شعبه‌ها و مرخصی اعضا</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddHolidayLeaveModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن تعطیل / مرخصی
            </button>
            <button onclick="exportHolidayLeavesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportHolidayLeavesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="holidayLeavesBranchTabs">
            <button onclick="filterHolidayLeavesByBranch('all')"
                    class="holiday-leave-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <input type="text" id="holidayLeaveSearch" placeholder="جستجو نام عضو..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterHolidayLeaves()">
            <input type="date" id="filterHolidayLeaveDate" onchange="filterHolidayLeaves()"
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4"
                   title="فیلتر تاریخ">
            <select id="filterHolidayLeaveTimezone" onchange="filterHolidayLeaves()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه مناطق زمانی</option>
                <option value="Asia/Tehran">تهران</option>
                <option value="Asia/Dubai">دبی</option>
                <option value="Asia/Istanbul">استانبول</option>
                <option value="Europe/London">لندن</option>
                <option value="Europe/Paris">پاریس</option>
                <option value="Europe/Berlin">برلین</option>
                <option value="Europe/Rome">رم</option>
                <option value="Europe/Amsterdam">آمستردام</option>
                <option value="America/New_York">نیویورک</option>
                <option value="America/Chicago">شیکاگو</option>
                <option value="America/Los_Angeles">لس‌آنجلس</option>
                <option value="America/Toronto">تورنتو</option>
                <option value="UTC">UTC</option>
            </select>
            <select id="filterHolidayLeaveStatus" onchange="filterHolidayLeaves()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="پر شده">پر شده</option>
                <option value="در انتظار تأیید">در انتظار تأیید</option>
            </select>
            <select id="filterHolidayLeaveType" onchange="filterHolidayLeaves()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="leave">مرخصی</option>
                <option value="official-holiday">تعطیل رسمی</option>
                <option value="mission">ماموریت</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]" id="holidayLeavesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('name')" class="flex items-center gap-1">نام عضو <span id="hlSortIcon-name">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('date')" class="flex items-center gap-1">تاریخ <span id="hlSortIcon-date">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('timeLabel')" class="flex items-center gap-1">ساعت <span id="hlSortIcon-timeLabel">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('typeLabel')" class="flex items-center gap-1">نوع <span id="hlSortIcon-typeLabel">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('timezone')" class="flex items-center gap-1">منطقه زمانی <span id="hlSortIcon-timezone">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('branchName')" class="flex items-center gap-1">شعبه <span id="hlSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortHolidayLeavesBy('status')" class="flex items-center gap-1">وضعیت <span id="hlSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="holidayLeavesPaginationInfo">نمایش ۱ تا ۱۰ از ۱۰۰ مورد</span>
            <div class="flex items-center gap-2" id="holidayLeavesPaginationButtons"></div>
        </div>
    </div>
</div>
