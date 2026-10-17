<div id="teachers" class="section hidden">
    <style>
        .staff-inline-expand { animation: slideDown 220ms ease-out forwards; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <h1 class="hidden">کارمندها</h1>
    <?php component('staff-page-tabs', ['active' => 'teachers']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <p class="text-gray-500 mt-1">لیست کارمندها، قراردادها و اطلاعات مرتبط با هر قرارداد</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button onclick="openAddStaffModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-user-plus"></i>
                افزودن کارمند جدید
            </button>
            <button onclick="exportStaffToExcel()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i>
                خروجی اکسل
            </button>
            <button onclick="exportStaffToPDF()"
                    class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i>
                خروجی PDF
            </button>
        </div>
    </div>

    <?php component('panel-page-guide', ['page' => 'teachers']); ?>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="staffBranchTabs">
            <button data-staff-organization="all" onclick="filterStaffByBranch('all')" class="staff-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه</button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" id="staffSearch" placeholder="جستجو نام / موبایل / عنوان قرارداد..."
                       class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                       onkeyup="filterStaff()">
            </div>
            <div>
                <select id="filterStaffType" onchange="filterStaff()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه نوع قرارداد</option>
                    <option value="teacher">استاد</option>
                    <option value="receptionist">پذیرش</option>
                    <option value="manager">مدیر</option>
                    <option value="other">سایر</option>
                </select>
            </div>
            <div>
                <select id="filterStaffStatus" onchange="filterStaff()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="در انتظار تأیید">در انتظار تأیید</option>
                    <option value="فعال">فعال</option>
                    <option value="غیرفعال">غیرفعال</option>
                    <option value="حذف شده">حذف شده</option>
                    <option value="رد شده">رد شده</option>
                </select>
            </div>
            <div>
                <select id="filterStaffCurrency" onchange="filterStaff()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع پول</option>
                    <option value="تومان">تومان</option>
                    <option value="دلار">دلار</option>
                    <option value="یورو">یورو</option>
                </select>
            </div>
            <div>
                <select id="filterStaffLesson" onchange="filterStaff()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه درس‌های شعبه‌ها</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1400px]" id="staffTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => '<button onclick="sortStaffBy(\'name\')" class="flex items-center gap-1"> نام <span id="sortIcon-name">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'typeLabel\')" class="flex items-center gap-1"> نوع قرارداد <span id="sortIcon-typeLabel">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'contractTitle\')" class="flex items-center gap-1"> عنوان قرارداد <span id="sortIcon-contractTitle">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'branch\')" class="flex items-center gap-1"> سازمان <span id="sortIcon-branch">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'lessonName\')" class="flex items-center gap-1"> درس <span id="sortIcon-lessonName">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'startDate\')" class="flex items-center gap-1"> تاریخ شروع <span id="sortIcon-startDate">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'endDate\')" class="flex items-center gap-1"> تاریخ خاتمه <span id="sortIcon-endDate">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'price\')" class="flex items-center gap-1"> مبلغ قرارداد <span id="sortIcon-price">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortStaffBy(\'status\')" class="flex items-center gap-1"> وضعیت <span id="sortIcon-status">↕</span> </button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-40 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="staffPaginationInfo">نمایش ۱ تا ۱۰ از ۳۰ کارمند</span>
            <div class="flex items-center gap-2" id="staffPaginationButtons"></div>
        </div>
    </div>
</div>
