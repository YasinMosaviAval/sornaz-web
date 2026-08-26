<div id="availabilities" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">برنامه زمانی سازمان</h1>
            <p class="text-gray-500 mt-1">برنامه زمانی و ساعات کاری آموزشگاه و شعبه‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddBranchScheduleModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن زمان‌بندی
            </button>
            <button onclick="exportBranchSchedulesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportBranchSchedulesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="branchSchedulesBranchTabs">
            <button onclick="filterBranchSchedulesByBranch('all')"
                    class="branch-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <select id="filterBranchDay" onchange="filterBranchSchedules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه روزها</option>
                <option value="شنبه">شنبه</option>
                <option value="یکشنبه">یکشنبه</option>
                <option value="دوشنبه">دوشنبه</option>
                <option value="سه‌شنبه">سه‌شنبه</option>
                <option value="چهارشنبه">چهارشنبه</option>
                <option value="پنجشنبه">پنجشنبه</option>
                <option value="جمعه">جمعه</option>
            </select>
            <select id="filterBranchRepeat" onchange="filterBranchSchedules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه دوره‌های تکرار</option>
                <option value="هفتگی">هفتگی</option>
                <option value="دو هفته">دو هفته</option>
                <option value="سه هفته">سه هفته</option>
                <option value="چهار هفته">چهار هفته</option>
                <option value="ماهانه">ماهانه</option>
                <option value="سالانه">سالانه</option>
                <option value="بی‌تکرار">بی‌تکرار</option>
            </select>
            <select id="filterBranchTimezone" onchange="filterBranchSchedules()"
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
            <select id="filterBranchStatus" onchange="filterBranchSchedules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="در انتظار تأیید">در انتظار تأیید</option>
            </select>
            <select id="displayBranchTimezone" onchange="filterBranchSchedules()"
                    class="w-full border border-indigo-200 bg-indigo-50 rounded-2xl py-3 px-4">
                <option value="">نمایش پیش‌فرض مناطق زمانی</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="branchSchedulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('branchName')" class="flex items-center gap-1">سازمان <span id="bsSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('day')" class="flex items-center gap-1">روز <span id="bsSortIcon-day">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('timeLabel')" class="flex items-center gap-1">ساعت <span id="bsSortIcon-timeLabel">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('repeatPeriod')" class="flex items-center gap-1">دوره تکرار <span id="bsSortIcon-repeatPeriod">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('timezone')" class="flex items-center gap-1">منطقه زمانی <span id="bsSortIcon-timezone">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortBranchSchedulesBy('status')" class="flex items-center gap-1">وضعیت <span id="bsSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div id="branchSchedulesPagination" class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="branchSchedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۱۰۰ زمان‌بندی</span>
            <div class="flex items-center gap-2" id="branchSchedulesPaginationButtons"></div>
        </div>
    </div>
</div>
