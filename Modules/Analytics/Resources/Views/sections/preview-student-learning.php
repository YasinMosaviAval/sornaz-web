<?php // Static personal dashboard fixtures for the public design preview. ?>
<section id="dashboard" class="section hidden space-y-6" data-dashboard-kind="student">
    <?php component('my-learning-tabs', ['active' => 'dashboard']); ?>
    <div><h2 class="mb-4 text-xl font-bold">درخواست‌های ثبت‌نام من</h2><div class="grid gap-4 lg:grid-cols-2"><article class="rounded-2xl border bg-white p-5 shadow-sm"><div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-bold">ترم نمونهٔ پیانو</h3><p class="mt-1 text-sm text-gray-500">دورهٔ مقدماتی · درخواست #۱</p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700">در انتظار بررسی آموزشگاه</span></div></article></div></div>
    <div class="grid gap-5 md:grid-cols-3"><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">ترم‌های من</p><strong class="mt-2 block text-2xl text-indigo-700">۲</strong></article><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">کلاس‌های من</p><strong class="mt-2 block text-2xl text-indigo-700">۳</strong></article><article class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">امتیاز من</p><strong class="mt-2 block text-2xl text-indigo-700">۱۲۵</strong></article></div>
</section>
<?php foreach (['my-terms' => ['ترم‌های من', 'ترم نمونهٔ پیانو', '۱۴۰۵/۰۷/۱۷', 'فعال'], 'my-classrooms' => ['کلاس‌های من', 'کلاس پیانو', 'شنبه ۱۰:۰۰', 'فعال'], 'my-courses' => ['دوره‌های من', 'دورهٔ مقدماتی پیانو', 'مقدماتی', 'در حال برگزاری']] as $previewKey => $previewValues): ?>
<section id="<?= e($previewKey) ?>" class="section hidden space-y-6">
    <?php component('my-learning-tabs', ['active' => $previewKey]); ?>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' class="w-full min-w-[600px] text-sm"';
$panelTable0['headAttributes'] = ' class="border-b bg-gray-50"';
$panelTable0['headerRowAttributes'] = '';
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($previewValues[0]), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0['columns'][] = ['html' => 'زمان یا سطح', 'attributes' => ' class="p-4 text-right"'];
$panelTable0['columns'][] = ['html' => 'وضعیت', 'attributes' => ' class="p-4 text-right"'];
$panelTable0['columns'][] = ['html' => 'عملیات', 'attributes' => ' class="p-4 text-right"'];
$panelTable0['bodyAttributes'] = '';
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = '';
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($previewValues[1]), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($previewValues[2]), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($previewValues[3]), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4 text-indigo-600"';
$panelTable0Row['cells'][] = ['html' => 'مشاهده', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div>
</section>
<?php endforeach; ?>
