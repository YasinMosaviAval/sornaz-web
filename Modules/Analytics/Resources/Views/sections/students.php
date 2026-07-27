<div id="students" class="section hidden">
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
            <button onclick="exportToExcel()" 
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i>
                خروجی اکسل
            </button>
        </div>
    </div>
    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" id="studentSearch" placeholder="جستجو نام / موبایل..." 
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                    onkeyup="filterStudents()">
            </div>
            <div>
                <select id="filterInstrument" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه سازها</option>
                    <option value="پیانو">پیانو</option>
                    <option value="گیتار">گیتار</option>
                    <option value="ویولن">ویولن</option>
                    <option value="آواز">آواز</option>
                </select>
            </div>
            <div>
                <select id="filterLevel" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه سطوح</option>
                    <option value="مبتدی">مبتدی</option>
                    <option value="متوسط">متوسط</option>
                    <option value="پیشرفته">پیشرفته</option>
                </select>
            </div>
            <div>
                <select id="filterStatus" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="فعال">فعال</option>
                    <option value="بدهکار">بدهکار</option>
                    <option value="غیرفعال">غیرفعال</option>
                </select>
            </div>
            <div>
                <select id="filterTeacher" onchange="filterStudents()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه اساتید</option>
                    <option value="موسوی">استاد موسوی</option>
                    <option value="رضایی">استاد رضایی</option>
                </select>
            </div>
        </div>
    </div>
    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]" id="studentsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">نام هنرجو</th>
                        <th class="text-right py-5 px-5 font-medium">ساز</th>
                        <th class="text-right py-5 px-5 font-medium">سطح</th>
                        <th class="text-right py-5 px-5 font-medium">استاد</th>
                        <th class="text-right py-5 px-5 font-medium">جلسات باقی‌مانده</th>
                        <th class="text-right py-5 px-5 font-medium">وضعیت مالی</th>
                        <th class="text-right py-5 px-5 font-medium">حضور</th>
                        <th class="w-40 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="paginationInfo">نمایش ۱ تا ۱۰ از ۷۶ هنرجو</span>
            <div class="flex items-center gap-2" id="paginationButtons">
                <!-- دکمه‌ها توسط JS ساخته می‌شوند -->
            </div>
        </div>
    </div>
</div>
