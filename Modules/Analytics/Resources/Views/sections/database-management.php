<?php $databaseLabel = static fn (string $fa, string $en): string => locale() === 'en' ? $en : $fa; ?>
<section id="database-management" class="section hidden" data-admin-ui-native>
    <div class="panel-page-toolbar" data-panel-page-toolbar>
        <div class="panel-page-toolbar-tabs">
            <?php component('panel-page-tabs', [
                'active' => 'tables', 'onSelect' => 'showDatabaseTab', 'label' => $databaseLabel('مدیریت دیتابیس', 'Database management'),
                'tabs' => [
                    'tables' => ['labels' => ['جدول‌ها', 'Tables'], 'attributes' => 'data-database-tab="tables"'],
                    'rows' => ['labels' => ['سطرهای جدول', 'Table rows'], 'attributes' => 'data-database-tab="rows" disabled'],
                ],
            ]); ?>
        </div>
        <div class="panel-page-toolbar-actions" data-panel-page-actions>
            <button type="button" data-database-action="download-all-schema" data-panel-tooltip="<?= e($databaseLabel('دانلود ساختار همه جدول‌ها', 'Download the schema of all tables')) ?>" class="flex items-center gap-2 rounded-2xl border bg-white px-4 py-3"><i class="fas fa-file-code" aria-hidden="true"></i><?= e($databaseLabel('ساختار کل دیتابیس', 'Database schema')) ?></button>
            <button type="button" data-database-action="download-all-data" data-panel-tooltip="<?= e($databaseLabel('دانلود ساختار و اطلاعات همه جدول‌ها', 'Download the schema and data of all tables')) ?>" class="flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-3 text-white"><i class="fas fa-download" aria-hidden="true"></i><?= e($databaseLabel('ساختار و اطلاعات کل دیتابیس', 'Database schema and data')) ?></button>
        </div>
    </div>
    <p data-database-status role="status" aria-live="polite" class="mb-4 text-sm"></p>
    <div data-database-panel="tables">
        <div class="mb-5 flex items-center gap-3 rounded-3xl bg-white p-4 shadow-sm">
            <input id="databaseTableSearch" type="search" autocomplete="off" placeholder="<?= e($databaseLabel('جستجوی نام جدول', 'Search table names')) ?>" aria-label="<?= e($databaseLabel('جستجوی نام جدول', 'Search table names')) ?>" class="min-w-0 flex-1 rounded-2xl border px-4 py-3">
            <button type="button" data-database-action="refresh-tables" class="rounded-2xl border px-4 py-3" aria-label="<?= e($databaseLabel('تازه‌سازی جدول‌ها', 'Refresh tables')) ?>" data-panel-tooltip="<?= e($databaseLabel('تازه‌سازی جدول‌ها', 'Refresh tables')) ?>"><i class="fas fa-sync-alt" aria-hidden="true"></i></button>
        </div>
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <?php component('panel-table-template', [
                    'attributes' => 'id="databaseTables" class="w-full min-w-[760px]" data-no-identity-column',
                    'columns' => array_map(static fn ($label) => ['value' => $label, 'attributes' => 'class="px-5 py-4 text-start font-medium"'], locale() === 'en' ? ['Table', 'Type', 'Estimated rows', 'Size', 'Actions'] : ['جدول', 'نوع', 'تعداد تقریبی سطرها', 'حجم', 'عملیات']),
                    'headAttributes' => 'class="border-b bg-gray-50"', 'bodyAttributes' => 'class="divide-y text-sm"', 'rows' => [],
                ]); ?>
            </div>
            <div id="databaseTablesPagination" hidden class="hidden flex flex-wrap items-center justify-between gap-3 border-t px-5 py-4"></div>
        </div>
    </div>
    <div data-database-panel="rows" class="hidden">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-3xl bg-white p-4 shadow-sm">
            <strong id="databaseSelectedTable" dir="ltr" class="break-all font-mono"></strong>
            <div class="flex flex-wrap gap-3">
                <button type="button" data-database-action="refresh-rows" class="rounded-2xl border px-4 py-3" aria-label="<?= e($databaseLabel('تازه‌سازی سطرها', 'Refresh rows')) ?>" data-panel-tooltip="<?= e($databaseLabel('تازه‌سازی سطرها', 'Refresh rows')) ?>"><i class="fas fa-sync-alt" aria-hidden="true"></i></button>
                <button type="button" data-database-action="download-table-schema" class="flex items-center gap-2 rounded-2xl border px-4 py-3"><i class="fas fa-file-code" aria-hidden="true"></i><?= e($databaseLabel('دانلود ساختار جدول', 'Download table schema')) ?></button>
                <button type="button" data-database-action="download-table-data" class="flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-3 text-white"><i class="fas fa-download" aria-hidden="true"></i><?= e($databaseLabel('دانلود ساختار و اطلاعات جدول', 'Download table schema and data')) ?></button>
            </div>
        </div>
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <?php component('panel-table-template', [
                    'attributes' => 'id="databaseRows" class="w-full" data-no-identity-column',
                    'columns' => [['value' => $databaseLabel('سطرهای جدول', 'Table rows'), 'attributes' => 'class="px-5 py-4 text-start"']],
                    'headAttributes' => 'class="border-b bg-gray-50"', 'bodyAttributes' => 'class="divide-y text-sm"', 'rows' => [],
                ]); ?>
            </div>
            <div id="databaseRowsPagination" hidden class="hidden flex flex-wrap items-center justify-between gap-3 border-t px-5 py-4"></div>
        </div>
    </div>
</section>
