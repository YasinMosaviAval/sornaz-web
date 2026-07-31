<div id="schedules" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">برنامه زمانی کلاس‌ها</h1>
            <p class="text-gray-500 mt-1">زمان‌بندی جلسات خصوصی و گروهی در شعبه‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddScheduleModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن برنامه
            </button>
            <button onclick="exportSchedulesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportSchedulesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="schedulesBranchTabs">
            <button onclick="filterSchedulesByBranch('all')"
                    class="schedule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <input type="text" id="scheduleSearch" placeholder="جستجو عنوان / هنرجو / استاد..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterSchedules()">
            <select id="filterScheduleDay" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه روزها</option>
                <option value="شنبه">شنبه</option>
                <option value="یکشنبه">یکشنبه</option>
                <option value="دوشنبه">دوشنبه</option>
                <option value="سه‌شنبه">سه‌شنبه</option>
                <option value="چهارشنبه">چهارشنبه</option>
                <option value="پنجشنبه">پنجشنبه</option>
                <option value="جمعه">جمعه</option>
            </select>
            <select id="filterScheduleType" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="خصوصی">خصوصی</option>
                <option value="گروهی">گروهی</option>
                <option value="آنلاین">آنلاین</option>
            </select>
            <select id="filterScheduleStatus" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
                <option value="تأیید شده">تأیید شده</option>
                <option value="در انتظار تأیید">در انتظار تأیید</option>
                <option value="رد شده">رد شده</option>
                <option value="حذف‌شده">حذف‌شده</option>
                <option value="پایان یافته">پایان یافته</option>
            </select>
            <select id="filterScheduleInstrument" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه سازها</option>
            </select>
            <select id="filterScheduleClassroom" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه کلاس‌ها</option>
            </select>
            <div>
                <label class="block text-xs text-gray-500 mb-1">از ساعت</label>
                <input type="time" id="filterScheduleTimeFrom" onchange="filterSchedules()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">تا ساعت</label>
                <input type="time" id="filterScheduleTimeTo" onchange="filterSchedules()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4">
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1300px]" id="schedulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('title')" class="flex items-center gap-1">عنوان <span id="scheduleSortIcon-title">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('day')" class="flex items-center gap-1">روز <span id="scheduleSortIcon-day">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('time')" class="flex items-center gap-1">ساعت <span id="scheduleSortIcon-time">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('student')" class="flex items-center gap-1">هنرجو <span id="scheduleSortIcon-student">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('teacher')" class="flex items-center gap-1">استاد <span id="scheduleSortIcon-teacher">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('instrument')" class="flex items-center gap-1">ساز <span id="scheduleSortIcon-instrument">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('classroom')" class="flex items-center gap-1">کلاس <span id="scheduleSortIcon-classroom">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('type')" class="flex items-center gap-1">نوع <span id="scheduleSortIcon-type">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortSchedulesBy('status')" class="flex items-center gap-1">وضعیت <span id="scheduleSortIcon-status">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="schedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۲۰۰ برنامه</span>
            <div class="flex items-center gap-2" id="schedulesPaginationButtons"></div>
        </div>
    </div>
</div>
