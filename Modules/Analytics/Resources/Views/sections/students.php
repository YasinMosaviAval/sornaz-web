<div id="students" class="section hidden">
    <style>
        .student-inline-expand { animation: studentSlideDown 220ms ease-out forwards; }
        @keyframes studentSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .student-parent-fields.hidden { display: none; }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">مدیریت هنرجویان</h1>
            <p class="text-gray-500 mt-1">لیست کامل هنرجویان و وضعیت آموزشی آن‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddStudentModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-user-plus"></i>
                افزودن هنرجو جدید
            </button>
            <button onclick="exportStudentsToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i>
                خروجی اکسل
            </button>
            <button onclick="exportStudentsToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i>
                خروجی PDF
            </button>
        </div>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="studentBranchTabs">
            <button type="button" onclick="filterStudentsByBranch('all')"
                    class="student-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white"
                    data-value="all">
                همه شعبه‌ها
            </button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div>
                <input type="text" id="studentSearch" placeholder="جستجو نام هنرجو / استاد..."
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                       onkeyup="filterStudents()">
            </div>
            <div>
                <select id="filterStudentAgeGroup" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه رده‌های سنی</option>
                    <option value="child">زیر ۱۸ سال</option>
                    <option value="adult">۱۸ سال و بالاتر</option>
                </select>
            </div>
            <div>
                <select id="filterStudentLevel" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه سطوح</option>
                    <option value="مبتدی">مبتدی</option>
                    <option value="متوسط">متوسط</option>
                    <option value="پیشرفته">پیشرفته</option>
                </select>
            </div>
            <div>
                <select id="filterStudentInstrument" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه سازها</option>
                    <option value="پیانو">پیانو</option>
                    <option value="گیتار">گیتار</option>
                    <option value="ویولن">ویولن</option>
                    <option value="آواز">آواز</option>
                    <option value="درام">درام</option>
                    <option value="سنتور">سنتور</option>
                    <option value="کمانچه">کمانچه</option>
                </select>
            </div>
            <div>
                <select id="filterStudentFinancial" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌های مالی</option>
                    <option value="تسویه">تسویه</option>
                    <option value="بدهکار">بدهکار</option>
                </select>
            </div>
            <div>
                <select id="filterStudentRemaining" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه جلسات باقی‌مانده</option>
                    <option value="0">۰ جلسه</option>
                    <option value="1-2">۱ تا ۲ جلسه</option>
                    <option value="3-5">۳ تا ۵ جلسه</option>
                    <option value="6+">۶ جلسه و بیشتر</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1300px]" id="studentsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('name')" class="flex items-center gap-1">نام هنرجو <span id="stuSortIcon-name">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('instrument')" class="flex items-center gap-1">ساز <span id="stuSortIcon-instrument">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('level')" class="flex items-center gap-1">سطح <span id="stuSortIcon-level">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('teacher')" class="flex items-center gap-1">استاد <span id="stuSortIcon-teacher">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('branch')" class="flex items-center gap-1">شعبه <span id="stuSortIcon-branch">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('remaining')" class="flex items-center gap-1">جلسات باقی‌مانده <span id="stuSortIcon-remaining">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('financial')" class="flex items-center gap-1">وضعیت مالی <span id="stuSortIcon-financial">↕</span></button>
                        </th>
                        <th class="text-right py-5 px-5 font-medium">
                            <button onclick="sortStudentsBy('attendance')" class="flex items-center gap-1">حضور <span id="stuSortIcon-attendance">↕</span></button>
                        </th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="studentsPaginationInfo">نمایش ۱ تا ۱۰ از ۲۵۰ هنرجو</span>
            <div class="flex items-center gap-2" id="studentsPaginationButtons"></div>
        </div>
    </div>
</div>
