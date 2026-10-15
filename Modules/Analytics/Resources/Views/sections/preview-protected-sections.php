<?php // Static design fixtures only. This component must never query account or tenant data. ?>
<section id="branch-types" class="section hidden">
    <div class="mb-7 flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-3xl font-bold">انواع آموزشی</h1><p class="mt-2 text-gray-500">مدیریت عنوان، خلاصه و شرح انواع آموزشی شعبه‌ها</p></div><button type="button" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-white"><i class="fas fa-plus ml-2"></i>نوع آموزشی جدید</button></div>
    <div class="overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-sm"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' class="w-full min-w-[850px]"';
$panelTable0['headAttributes'] = ' class="bg-gray-50 text-right text-sm text-gray-500"';
$panelTable0['headerRowAttributes'] = '';
$panelTable0['columns'][] = ['html' => 'عنوان', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'خلاصه', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'شرح', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'عملیات', 'attributes' => ' class="p-4"'];
$panelTable0['bodyAttributes'] = '';
foreach (['موسیقی', 'هنرهای نمایشی', 'آموزش گروهی'] as $item):
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = '';
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($item), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => 'نمونهٔ معرفی', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => 'شرح نمونه برای طراحی', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4 text-indigo-600"';
$panelTable0Row['cells'][] = ['html' => 'ویرایش', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
endforeach;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div>
</section>
<section id="classroom-types" class="section hidden">
    <div class="mb-7 flex justify-between"><div><h1 class="text-3xl font-bold">انواع کلاس</h1><p class="mt-2 text-gray-500">مدیریت انواع کلاس‌های فیزیکی</p></div><button type="button" class="rounded-xl bg-indigo-600 px-5 py-2 text-white">+ نوع جدید</button></div>
    <div class="mb-5 grid grid-cols-1 gap-3 rounded-2xl bg-white p-4 shadow-sm md:grid-cols-3">
        <label><span class="mb-1 block text-xs text-gray-500">جست‌وجو</span><input type="search" placeholder="عنوان یا نام ایجادکننده" class="w-full rounded-xl border border-gray-200 px-4 py-2.5"></label>
        <label><span class="mb-1 block text-xs text-gray-500">دسته</span><select class="w-full rounded-xl border border-gray-200 px-4 py-2.5"><option>همه دسته‌ها</option><option>کلاس آموزشی</option></select></label>
        <label><span class="mb-1 block text-xs text-gray-500">وضعیت</span><select class="w-full rounded-xl border border-gray-200 px-4 py-2.5"><option>همه وضعیت‌ها</option><option>تأیید شده</option><option>در انتظار تأیید</option><option>رد شده</option></select></label>
    </div>
    <div class="overflow-x-auto rounded-3xl bg-white shadow"><?php $panelTable1 = ['columns' => [], 'rows' => []];
$panelTable1['attributes'] = ' class="w-full"';
$panelTable1['headAttributes'] = ' class="bg-gray-50"';
$panelTable1['headerRowAttributes'] = '';
$panelTable1['columns'][] = ['html' => 'ایجادکننده ↕', 'attributes' => ' class="p-4 text-right"'];
$panelTable1['columns'][] = ['html' => 'عنوان ↕', 'attributes' => ' class="p-4 text-right"'];
$panelTable1['columns'][] = ['html' => 'خلاصه ↕', 'attributes' => ' class="p-4 text-right"'];
$panelTable1['columns'][] = ['html' => 'دسته ↕', 'attributes' => ' class="p-4 text-right"'];
$panelTable1['columns'][] = ['html' => 'وضعیت ↕', 'attributes' => ' class="p-4 text-right"'];
$panelTable1['columns'][] = ['html' => '', 'attributes' => ' class="p-4"'];
$panelTable1['bodyAttributes'] = '';
$panelTable1Row = ['cells' => []];
$panelTable1Row['attributes'] = '';
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => 'آموزشگاه نمونه', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => 'کلاس تمرین', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => 'فضای تمرین موسیقی', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => 'آموزشی', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => 'تأیید شده', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4 text-indigo-600"';
$panelTable1Row['cells'][] = ['html' => 'ویرایش', 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1['rows'][] = $panelTable1Row;
component('panel-table-template', $panelTable1); unset($panelTable1); ?></div>
</section>
<section id="national-holidays" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"><div><h1 class="text-3xl font-bold">تعطیلات رسمی کشور</h1><p class="mt-1 text-gray-500">تاریخ‌هایی که به‌صورت پیش‌فرض از برنامه‌ریزی خودکار جلسات حذف می‌شوند</p></div><button type="button" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white"><i class="fas fa-plus ml-2"></i>افزودن تعطیل رسمی</button></div>
    <div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php $panelTable2 = ['columns' => [], 'rows' => []];
$panelTable2['attributes'] = ' class="w-full min-w-[760px]"';
$panelTable2['headAttributes'] = ' class="border-b bg-gray-50"';
$panelTable2['headerRowAttributes'] = '';
$panelTable2['columns'][] = ['html' => 'عنوان', 'attributes' => ' class="p-5 text-right"'];
$panelTable2['columns'][] = ['html' => 'تاریخ', 'attributes' => ' class="p-5 text-right"'];
$panelTable2['columns'][] = ['html' => 'وضعیت', 'attributes' => ' class="p-5 text-right"'];
$panelTable2['columns'][] = ['html' => '', 'attributes' => ' class="p-5"'];
$panelTable2['bodyAttributes'] = '';
$panelTable2Row = ['cells' => []];
$panelTable2Row['attributes'] = '';
$panelTable2CellAttributes = ' class="p-5"';
$panelTable2Row['cells'][] = ['html' => 'تعطیل نمونه', 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-5"';
$panelTable2Row['cells'][] = ['html' => '۱۴۰۵/۰۷/۱۷', 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-5"';
$panelTable2Row['cells'][] = ['html' => 'فعال', 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-5 text-indigo-600"';
$panelTable2Row['cells'][] = ['html' => 'ویرایش', 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2['rows'][] = $panelTable2Row;
component('panel-table-template', $panelTable2); unset($panelTable2); ?></div></div>
</section>
