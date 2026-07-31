<div id="member-schedules" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">زمان‌بندی اعضا</h1>
            <p class="text-gray-500 mt-1">برنامه زمانی اساتید، منشی‌ها و پرسنل</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddMemberScheduleModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن زمان‌بندی
            </button>
            <button onclick="exportMemberSchedulesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportMemberSchedulesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="memberSchedulesBranchTabs">
            <button onclick="filterMemberSchedulesByBranch('all')"
                    class="member-schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="memberScheduleSearch" placeholder="جستجو نام عضو..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterMemberSchedules()">
            <select id="filterMemberRole" onchange="filterMemberSchedules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه نقش‌ها</option>
                <option value="استاد">استاد</option>
                <option value="منشی">منشی</option>
                <option value="مدیر">مدیر</option>
                <option value="پرسنل">پرسنل</option>
            </select>
            <select id="filterMemberDay" onchange="filterMemberSchedules()"
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
            <select id="filterMemberStatus" onchange="filterMemberSchedules()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="پر شده">پر شده</option>
                <option value="در انتظار تأیید">در انتظار تأیید</option>
            </select>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="memberSchedulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('name')" class="flex items-center gap-1">نام عضو <span id="msSortIcon-name">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('role')" class="flex items-center gap-1">نقش <span id="msSortIcon-role">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('day')" class="flex items-center gap-1">روز <span id="msSortIcon-day">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('timeLabel')" class="flex items-center gap-1">ساعت <span id="msSortIcon-timeLabel">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('branchName')" class="flex items-center gap-1">شعبه <span id="msSortIcon-branchName">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortMemberSchedulesBy('status')" class="flex items-center gap-1">وضعیت <span id="msSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="memberSchedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۱۰۰ زمان‌بندی</span>
            <div class="flex items-center gap-2" id="memberSchedulesPaginationButtons"></div>
        </div>
    </div>
</div>
