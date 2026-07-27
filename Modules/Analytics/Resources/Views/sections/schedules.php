<div id="schedules" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">برنامه زمانی کلاس‌ها</h1>
            <p class="text-gray-500 mt-1">زمان‌بندی جلسات خصوصی و گروهی در شعبه‌ها</p>
        </div>


        <button onclick="showSection('scheduling-rules')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-sliders-h w-5"></i> قوانین زمان‌بندی
        </button>
        <button onclick="showSection('member-schedules')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-user-clock w-5"></i> زمان‌بندی اعضا
        </button>
        <button onclick="showSection('availabilities')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-clock w-5"></i> زمان‌های در دسترس
        </button>
        <button onclick="showSection('availability-exceptions')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-ban w-5"></i> زمان‌های خارج از دسترس
        </button>


        <button onclick="openAddScheduleModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن برنامه
        </button>
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

    <!-- فیلترهای اضافی -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="scheduleSearch" placeholder="جستجو هنرجو / استاد..."  onkeyup="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
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
            </div>
            <div>
                <select id="filterScheduleType" onchange="filterSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع</option>
                    <option value="خصوصی">خصوصی</option>
                    <option value="گروهی">گروهی</option>
                    <option value="آنلاین">آنلاین</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول برنامه زمانی -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]" id="schedulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">روز</th>
                        <th class="text-right py-5 px-5 font-medium">ساعت</th>
                        <th class="text-right py-5 px-5 font-medium">هنرجو</th>
                        <th class="text-right py-5 px-5 font-medium">استاد</th>
                        <th class="text-right py-5 px-5 font-medium">ساز</th>
                        <th class="text-right py-5 px-5 font-medium">کلاس</th>
                        <th class="text-right py-5 px-5 font-medium">شعبه</th>
                        <th class="text-right py-5 px-5 font-medium">نوع</th>
                        <th class="w-28 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="schedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۲۰ برنامه</span>
            <div class="flex items-center gap-2" id="schedulesPaginationButtons"></div>
        </div>
    </div>
</div>