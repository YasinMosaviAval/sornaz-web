<div id="lessons" class="section hidden">
    <h1 class="hidden">درس‌ها</h1>
    <?php component('lesson-page-tabs', ['active' => 'lessons']); ?>
    <div class="flex flex-col md:flex-row md:items-center justify-end gap-4 mb-6">
        <div class="flex flex-wrap justify-end gap-3">
            <button id="addLessonButton" onclick="openAddLessonModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن درس
            </button>
            <button onclick="exportLessonsToExcel()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-excel text-green-600"></i> خروجی اکسل
            </button>
            <button onclick="exportLessonsToPDF()" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-600"></i> خروجی PDF
            </button>
        </div>
    </div>

    <?php component('panel-page-guide', [
        'title' => locale() === 'en' ? 'The first step in managing your academy and branches' : 'اولین مرحله مدیریت امور آموزشگاه و شعبه ها',
        'paragraphs' => array_map(static fn (array $note): string => $note[locale() === 'en' ? 1 : 0], [
                ['پیش از شروع این مرحله، اگر آموزشگاه شما چند شعبه دارد، باید تمام شعبه های اصلی و فرعی آموزشگاه را ثبت کنید.', 'Before starting, register all main and subsidiary branches if your academy has more than one branch.'],
                ['پس از ثبت آموزشگاه و شعبه های اصلی و فرعی آن، قبل از هر کار دیگر، فهرست تمام درس های ارائه شده در آموزشگاه و شعبه ها را به تفکیک وارد کنید.', 'After registering the academy and its branches, enter the complete lesson list for each organization before continuing.'],
                ['اگر درسی را برای آموزشگاه انتخاب کنید، نیازی نیست آن درس را برای شعبه ها ثبت کنید و به صورت خودکار امکان انتخاب درس های آموزشگاه برای شعبه های اصلی و فرعی آموزشگاه وجود دارد.', 'Academy lessons are automatically available to its branches; you do not need to register duplicate lessons for each branch.'],
                ['درس هایی که برای شعبه ها تعریف می کنید، در بخش های دیگر فقط برای شعبه مورد نظر قابل انتخاب خواهند بود.', 'Lessons defined for a branch are available only to that branch in other sections.'],
                ['درس ذخیره‌شده باید در فهرست و فرم‌های وابسته قابل انتخاب باشد. در صورت مشاهده‌نشدن، فیلتر شعبه و وضعیت "فعال" بودن یا نبودن درس را بررسی کنید.', 'Saved lessons should appear in the list and related forms. If a lesson is missing, check the branch filter and whether the lesson is active.'],
                ['ویرایش درسی که در دوره یا قرارداد یکی از اساتید استفاده شده فقط برای مدیر یا کاربر آموزشگاه و شعبهٔ مجاز امکان‌پذیر است.', 'Only the authorized academy or branch account or manager can edit a lesson used in a course or teacher contract.'],
                ['حذف درسی که در دوره یا قرارداد یکی از اساتید استفاده شده مجاز نیست.', 'A lesson used in any course or teacher contract cannot be deleted.'],
            ]),
    ]); ?>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max" id="lessonsBranchTabs">
            <button onclick="filterLessonsByBranch('all')" class="lesn-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">همه شعبه‌ها</button>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="lessonSearch" placeholder="جستجو نام درس..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterLessons()">
            <select id="filterLessonStatus" onchange="filterLessons()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="active">فعال</option>
                <option value="inactive">غیرفعال</option>
                <option value="pending">در انتظار تأیید</option>
            </select>
            <select id="filterLessonLevel" onchange="filterLessons()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه سطوح</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1100px]" id="lessonsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => '<button onclick="sortLessonsBy(\'title\')" class="flex items-center gap-1">عنوان / درس <span id="lesnSortIcon-title">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortLessonsBy(\'levelTitle\')" class="flex items-center gap-1">سطح <span id="lesnSortIcon-levelTitle">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortLessonsBy(\'start_date\')" class="flex w-full items-center justify-center gap-1">زمان شروع <span id="lesnSortIcon-start_date">↕</span></button>', 'attributes' => ' class="text-center py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortLessonsBy(\'is_primary\')" class="flex items-center gap-1">اصلی <span id="lesnSortIcon-is_primary">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortLessonsBy(\'branchName\')" class="flex items-center gap-1">سازمان <span id="lesnSortIcon-branchName">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortLessonsBy(\'status\')" class="flex items-center gap-1">وضعیت <span id="lesnSortIcon-status">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-40 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
        <div id="lessonsPagination" class="hidden px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="lessonsPaginationInfo">نمایش ۱ تا ۱۰ از ۴۰ مورد</span>
            <div class="flex items-center gap-2" id="lessonsPaginationButtons"></div>
        </div>
    </div>
</div>
