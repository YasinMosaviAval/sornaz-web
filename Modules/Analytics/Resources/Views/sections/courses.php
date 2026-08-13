<div id="courses" class="section hidden">
    <style>
        .course-inline-expand { animation: courseSlideDown 220ms ease-out forwards; }
        @keyframes courseSlideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مدیریت دوره‌ها</h1>
            <p class="text-gray-500 mt-1">دوره‌های فعال، غیرفعال و در انتظار تکمیل</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="button" data-course-action="add" data-no-inline-edit
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-plus"></i> افزودن دوره جدید
            </button>
            <button onclick="exportCoursesToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportCoursesToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="coursesBranchTabs">
            <button onclick="filterCoursesByBranch('all')"
                    class="course-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="courseSearch" placeholder="جستجوی نام دوره..."
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                       onkeyup="filterCourses()">
            </div>
            <div>
                <select id="filterCourseStatus" onchange="filterCourses()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="در انتظار">در انتظار</option>
                    <option value="باز">باز</option>
                    <option value="در حال برگزاری">در حال برگزاری</option>
                    <option value="پایان‌یافته">پایان‌یافته</option>
                </select>
            </div>
            <div>
                <select id="filterCourseInstrument" onchange="filterCourses()"
                        class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه سازها</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]" id="coursesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('name')" class="flex items-center gap-1">
                                نام دوره <span id="courseSortIcon-name">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('level')" class="flex items-center gap-1">
                                سطح <span id="courseSortIcon-level">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('branchName')" class="flex items-center gap-1">
                                شعبه <span id="courseSortIcon-branchName">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('instrument')" class="flex items-center gap-1">
                                ساز / تخصص <span id="courseSortIcon-instrument">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('student_capacity')" class="flex items-center gap-1">
                                ظرفیت هنرجوها <span id="courseSortIcon-student_capacity">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('teacher_capacity')" class="flex items-center gap-1">
                                ظرفیت اساتید <span id="courseSortIcon-teacher_capacity">↕</span>
                            </button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortCoursesBy('status')" class="flex items-center gap-1">
                                وضعیت <span id="courseSortIcon-status">↕</span>
                            </button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>

        <!-- صفحه‌بندی -->
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="coursesPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ دوره</span>
            <div class="flex items-center gap-2" id="coursesPaginationButtons"></div>
        </div>
    </div>
</div>
