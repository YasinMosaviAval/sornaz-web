<?php $pointSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows(auth()->user()); ?>
<div id="points" class="section hidden">
    <h1 class="text-3xl font-bold mb-6"><?= e(locale() === 'en' ? 'My points' : 'امتیازهای من') ?></h1>
    <style>
        .point-inline-expand { animation: pointSlideDown 220ms ease-out forwards; }
        @keyframes pointSlideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <?php component('panel-page-tabs', [
        'active' => 'personal', 'onSelect' => 'showPointTab',
        'label' => locale() === 'en' ? 'Points pages' : 'صفحه‌های امتیاز',
        'tabs' => [
            'personal' => ['labels' => ['امتیازهای من', 'My points'], 'attributes' => 'data-point-tab="personal"'],
            'rules' => ['labels' => ['قوانین امتیازها', 'Point rules'], 'attributes' => 'data-point-tab="rules"'],
        ],
    ]); ?>

    <div data-point-panel="personal">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-3xl bg-gradient-to-l from-amber-400 to-yellow-500 p-5 text-white shadow"><p class="text-sm opacity-90">امتیاز عمومی من</p><strong id="pointGeneralBalance" class="block mt-2 text-3xl">۰</strong></div>
        <div class="rounded-3xl bg-gradient-to-l from-violet-600 to-indigo-600 p-5 text-white shadow"><p class="text-sm opacity-90">امتیاز حرفه‌ای من</p><strong id="pointProfessionalBalance" class="block mt-2 text-3xl">۰</strong></div>
        <div class="rounded-3xl bg-gradient-to-l from-teal-600 to-cyan-600 p-5 text-white shadow"><p class="text-sm opacity-90"><?= e(locale() === 'en' ? 'My specialized points' : 'امتیاز تخصصی من') ?></p><strong id="pointSpecializedBalance" class="block mt-2 text-3xl">۰</strong></div>

    </div>

    <?php component('my-points'); ?>
    <section class="mt-6 rounded-3xl bg-white p-5 shadow-sm dark:bg-slate-900" aria-labelledby="personalPointsChartTitle">
        <h2 id="personalPointsChartTitle" class="mb-3 text-lg font-bold"><?= e(locale() === 'en' ? 'My points by type' : 'امتیازهای من به تفکیک نوع') ?></h2>
        <p class="mb-3 text-sm text-gray-500"><?= e(locale() === 'en' ? 'Shares of your confirmed point balances: general, professional and specialized.' : 'سهم امتیازهای عمومی، حرفه‌ای و تخصصی از موجودی تأییدشدهٔ حساب شما.') ?></p>
        <p id="personalPointsChartSummary" class="mb-3 text-sm" aria-live="polite"></p>
        <div id="personalPointsChart" class="w-full" style="height:360px" role="img" aria-label="<?= e(locale() === 'en' ? 'Pie chart of my general, professional and specialized points' : 'نمودار دایره‌ای امتیازهای عمومی، حرفه‌ای و تخصصی من') ?>"></div>
    </section>
    </div>
    <div data-point-panel="rules" class="hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-end gap-4 mb-6">
        <?php if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())): ?>
        <button onclick="openAddPointModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن قانون امتیاز
        </button>
        <?php endif; ?>
    </div>

    <div class="rounded-3xl bg-white p-5 shadow mb-6"><p class="text-sm text-gray-500">قوانین فعال</p><strong id="pointActiveRules" class="block mt-2 text-3xl text-gray-800">۰</strong><p class="mt-1 text-xs text-gray-400">اعمال خودکار با سقف و کنترل تکرار</p></div>
    <!-- فیلترها -->
    <div class="bg-white rounded-3xl p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="text" id="pointSearch" placeholder="جستجو عنوان / عملیات / خلاصه..."
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500"
                   onkeyup="filterPoints()">
            <select id="filterPointType" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="general">عمومی</option>
                <option value="professional">حرفه‌ای</option>
            </select>
            <select id="filterPointCategory" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه دسته‌ها</option>
                <option value="attendance">حضور</option>
                <option value="academic">آموزشی</option>
                <option value="event">رویداد</option>
                <option value="social">اجتماعی</option>
                <option value="financial">مالی</option>
                <option value="profile">پروفایل</option>
                <option value="achievement">دستاورد</option>
                <option value="engagement">تعامل</option>
                <option value="management">مدیریت</option>
            </select>
            <?php if ($pointSiteAdmin): ?>
            <select id="filterPointStatus" onchange="filterPoints()"
                    class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه وضعیت‌ها</option>
                <option value="فعال">فعال</option>
                <option value="غیرفعال">غیرفعال</option>
            </select>
            <?php endif; ?>
        </div>
    </div>

    <!-- جدول -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1200px]" id="pointsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => '<button onclick="sortPointsBy(\'title\')" class="flex items-center gap-1">عنوان <span id="pointSortIcon-title">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortPointsBy(\'type\')" class="flex items-center gap-1">نوع <span id="pointSortIcon-type">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortPointsBy(\'category\')" class="flex items-center gap-1">دسته <span id="pointSortIcon-category">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '<button onclick="sortPointsBy(\'points\')" class="flex items-center gap-1">امتیاز <span id="pointSortIcon-points">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ...(\Modules\System\Services\SiteAdminAccess::allows(auth()->user()) ? [['html' => '<button onclick="sortPointsBy(\'source\')" class="flex items-center gap-1">منبع / عملیات <span id="pointSortIcon-source">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"']] : []),
                    ...(\Modules\System\Services\SiteAdminAccess::allows(auth()->user()) ? [['html' => '<button onclick="sortPointsBy(\'branchName\')" class="flex items-center gap-1">شعبه <span id="pointSortIcon-branchName">↕</span></button>', 'attributes' => ' class="text-right py-5 px-5 font-medium"']] : []),
                    ['html' => e(locale() === 'en' ? 'Repeat control' : 'کنترل تکرار'), 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ...($pointSiteAdmin ? [['html' => e(locale() === 'en' ? 'Status' : 'وضعیت'), 'attributes' => ' class="text-right py-5 px-5 font-medium"']] : []),
                    ['html' => '', 'attributes' => ' class="w-40 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
        <div class="px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
            <span id="pointsPaginationInfo">نمایش ۱ تا ۱۰ از ۰ قانون</span>
            <div class="flex items-center gap-2" id="pointsPaginationButtons"></div>
        </div>
    </div>

    </div>
</div>
