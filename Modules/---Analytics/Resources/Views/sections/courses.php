<div id="courses" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت دوره‌ها</h1>
            <p class="text-gray-500 mt-1">دوره‌های فعال، غیرفعال و در انتظار تکمیل</p>
        </div>
        <button onclick="openAddCourseModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن دوره جدید
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="coursesBranchTabs">
            <button onclick="filterCoursesByBranch('all')" class="course-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- جدول دوره‌ها -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="coursesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">نام دوره</th>
                        <th class="text-right py-5 px-5 font-medium">شعبه</th>
                        <th class="text-right py-5 px-5 font-medium">ساز / تخصص</th>
                        <th class="text-right py-5 px-5 font-medium">ظرفیت</th>
                        <th class="text-right py-5 px-5 font-medium">ثبت‌نام‌شده</th>
                        <th class="text-right py-5 px-5 font-medium">وضعیت</th>
                        <th class="w-36 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
    </div>
</div>
