<div id="schedules" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">برنامه زمانی کلاس‌ها</h1>
            <p class="text-gray-500 mt-1">جلسات حضوری و آنلاین ترم‌های آموزشگاه</p>
        </div>
        <div class="flex flex-wrap gap-3">
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
                <option value="5">شنبه</option><option value="6">یکشنبه</option><option value="0">دوشنبه</option><option value="1">سه‌شنبه</option><option value="2">چهارشنبه</option><option value="3">پنجشنبه</option><option value="4">جمعه</option>
            </select>
            <select id="filterScheduleType" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="in_person">حضوری</option><option value="online">آنلاین</option>
            </select>
            <select id="filterScheduleStatus" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="pending">در انتظار تأیید</option><option value="approved">تأیید شده</option><option value="rejected">رد شده</option><option value="completed">پایان یافته</option><option value="canceled">لغو شده</option><option value="rescheduled">زمان‌بندی مجدد</option><option value="held">برگزار شده</option><option value="postponed">به تعویق افتاده</option>
            </select>
            <select id="filterScheduleInstrument" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه درس‌ها</option>
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
                            <button onclick="sortSchedulesBy('lesson')" class="flex items-center gap-1">درس <span id="scheduleSortIcon-lesson">↕</span></button>
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
        <div class="px-6 py-4 border-t flex flex-col lg:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="schedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۲۰۰ برنامه</span>
            <label class="flex items-center gap-2 whitespace-nowrap">
                تعداد ردیف در صفحه
                <select id="schedulesPerPage" onchange="changeSchedulesPerPage(this.value)" class="border border-gray-300 rounded-xl py-2 px-3 text-gray-700">
                    <option value="10">۱۰</option><option value="20">۲۰</option><option value="30">۳۰</option><option value="50">۵۰</option><option value="100">۱۰۰</option>
                </select>
            </label>
            <div class="flex items-center gap-2" id="schedulesPaginationButtons"></div>
        </div>
    </div>
</div>
