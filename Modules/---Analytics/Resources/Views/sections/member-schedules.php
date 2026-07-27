<div id="member-schedules" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">زمان‌بندی اعضا</h1>
            <p class="text-gray-500 mt-1">برنامه زمانی اساتید، منشی‌ها و پرسنل</p>
        </div>

        <button onclick="showSection('schedules')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-calendar-alt w-5"></i> برنامه زمانی
        </button>
        <button onclick="showSection('scheduling-rules')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-sliders-h w-5"></i> قوانین زمان‌بندی
        </button>
        <button onclick="showSection('availabilities')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-clock w-5"></i> زمان‌های در دسترس
        </button>
        <button onclick="showSection('availability-exceptions')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-ban w-5"></i> زمان‌های خارج از دسترس
        </button>

        <button onclick="openAddMemberScheduleModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن زمان‌بندی
        </button>
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

    <!-- فیلترهای اضافی -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="memberScheduleSearch" placeholder="جستجو نام عضو..." 
                       onkeyup="filterMemberSchedules()"
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select id="filterMemberRole" onchange="filterMemberSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه نقش‌ها</option>
                    <option value="استاد">استاد</option>
                    <option value="منشی">منشی</option>
                    <option value="مدیر">مدیر</option>
                    <option value="پرسنل">پرسنل</option>
                </select>
            </div>
            <div>
                <select id="filterMemberDay" onchange="filterMemberSchedules()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
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
        </div>
    </div>

    <!-- جدول زمان‌بندی اعضا -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="memberSchedulesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">نام عضو</th>
                        <th class="text-right py-5 px-5 font-medium">نقش</th>
                        <th class="text-right py-5 px-5 font-medium">روز</th>
                        <th class="text-right py-5 px-5 font-medium">ساعت</th>
                        <th class="text-right py-5 px-5 font-medium">شعبه</th>
                        <th class="text-right py-5 px-5 font-medium">وضعیت</th>
                        <th class="w-32 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="memberSchedulesPaginationInfo">نمایش ۱ تا ۱۰ از ۱۵ زمان‌بندی</span>
            <div class="flex items-center gap-2" id="memberSchedulesPaginationButtons"></div>
        </div>
    </div>
</div>