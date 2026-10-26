<div id="classrooms" class="section hidden">
    <style>
        .classroom-inline-expand { animation: classroomSlideDown 220ms ease-out forwards; }
        @keyframes classroomSlideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <h1 class="hidden"><?= e(locale() === 'en' ? 'Class management' : 'مدیریت کلاس') ?></h1>
    <?php component('classroom-page-tabs', ['active' => 'classrooms']); ?>
    <p data-panel-description class="mb-6 text-gray-500">لیست کلاس‌ها، نوع، تجهیزات و وضعیت هر شعبه</p>
    <div class="mb-6 flex flex-wrap justify-end gap-3">
            <button onclick="openAddClassroomModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition">
                <i class="fas fa-plus"></i> افزودن کلاس جدید
            </button>
            <button onclick="exportClassroomsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportClassroomsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
    </div>
    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="classroomBranchTabs">
            <button onclick="filterClassroomsByBranch('all')" class="branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <input type="text" id="classroomSearch" placeholder="جستجو نام کلاس..." class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500" onkeyup="filterClassrooms()">
            </div>
            <div>
                <select id="filterClassroomStatus" onchange="filterClassrooms()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="در انتظار تأیید">در انتظار تأیید</option>
                    <option value="در دسترس">در دسترس</option>
                    <option value="خارج از دسترس">خارج از دسترس</option>
                </select>
            </div>
            <div>
                <select id="filterClassroomType" onchange="filterClassrooms()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه انواع</option>
                </select>
            </div>
            <div>
                <select id="filterClassroomCapacity" onchange="filterClassrooms()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه ظرفیت‌ها</option>
                </select>
            </div>
            <div>
                <select id="filterClassroomEquipment" onchange="filterClassrooms()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                    <option value="">همه تجهیزات</option>
                </select>
            </div>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1100px]" id="classroomsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => '<button onclick="sortClassroomsBy(\'name\')" class="flex items-center gap-1">نام کلاس <span id="classroomSortIcon-name">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortClassroomsBy(\'typeLabel\')" class="flex items-center gap-1">نوع کلاس <span id="classroomSortIcon-typeLabel">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortClassroomsBy(\'branchName\')" class="flex items-center gap-1">سازمان <span id="classroomSortIcon-branchName">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortClassroomsBy(\'capacity\')" class="flex items-center gap-1">ظرفیت <span id="classroomSortIcon-capacity">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortClassroomsBy(\'equipment\')" class="flex items-center gap-1">تجهیزات <span id="classroomSortIcon-equipment">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortClassroomsBy(\'status\')" class="flex items-center gap-1">وضعیت <span id="classroomSortIcon-status">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-40 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>

        <!-- صفحه‌بندی -->
        <div id="classroomPagination" class="hidden px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="classroomPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ کلاس</span>
            <div class="flex items-center gap-2" id="classroomPaginationButtons"></div>
        </div>
    </div>
    <section class="mt-6 rounded-3xl bg-white p-5 shadow-sm dark:bg-slate-900" aria-labelledby="classroomHeatmapTitle">
        <h2 id="classroomHeatmapTitle" class="mb-3 text-lg font-bold"><?= e(locale() === 'en' ? 'Classroom occupancy heatmap' : 'نقشهٔ حرارتی پر و خالی کلاس‌ها') ?></h2>
        <p class="mb-3 text-sm text-gray-500"><?= e(locale() === 'en' ? 'Counts available classrooms with bookings overlapping each hour in the selected week. Zero means no booking, not guaranteed availability; closures and working hours are not included. Unavailable and pending classrooms are excluded from free counts. All classroom filters apply.' : 'تعداد کلاس‌های در دسترس با رزرو هم‌پوشان در هر ساعت هفتهٔ انتخاب‌شده. صفر یعنی بدون رزرو، نه تضمین امکان رزرو؛ تعطیلی و ساعات کاری لحاظ نمی‌شوند. کلاس خارج از دسترس و در انتظار در شمارش خالی نیست. تمام فیلترهای کلاس‌ها اعمال می‌شوند.') ?></p>
        <div class="mb-3 flex flex-wrap items-center gap-3">
            <button type="button" onclick="changeClassroomHeatmapWeek(-7)" class="rounded-xl border px-4 py-2"><?= e(locale() === 'en' ? 'Previous week' : 'هفته قبل') ?></button>
            <label><?= e(locale() === 'en' ? 'Week containing date' : 'هفتهٔ شامل تاریخ') ?><input id="classroomHeatmapWeek" type="date" onchange="loadClassroomHeatmap(this.value)" class="mx-2 rounded-xl border p-2 dark:bg-slate-800"></label>
            <button type="button" onclick="changeClassroomHeatmapWeek(7)" class="rounded-xl border px-4 py-2"><?= e(locale() === 'en' ? 'Next week' : 'هفته بعد') ?></button>
            <label><?= e(locale() === 'en' ? 'Classroom' : 'کلاس') ?><select id="classroomHeatmapRoom" onchange="selectClassroomHeatmap(this.value)" class="mx-2 rounded-xl border p-2 dark:bg-slate-800"></select></label>
            <button type="button" onclick="downloadClassroomHeatmap()" class="rounded-xl border px-4 py-2"><?= e(locale() === 'en' ? 'Download PNG' : 'دریافت تصویر نمودار') ?></button>
        </div>
        <p id="classroomHeatmapSummary" class="mb-3 text-sm" aria-live="polite"></p>
        <div id="classroomHeatmap" style="height:540px" class="w-full" role="img" aria-label="<?= e(locale() === 'en' ? 'Occupied classrooms by weekday and hour' : 'کلاس‌های پر به تفکیک روز هفته و ساعت') ?>"></div>
    </section>
</div>
