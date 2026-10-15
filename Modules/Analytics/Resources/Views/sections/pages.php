<div id="pages" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"><div><h1 class="text-3xl font-bold">برگه‌ها</h1><p class="mt-1 text-gray-500">مدیریت محتوای صفحات واقعی سایت؛ مشابه فهرست برگه‌های وردپرس</p></div><span id="sitePagesCount" class="rounded-xl bg-white px-4 py-2 text-sm text-gray-500 shadow-sm">۰ برگه</span></div>
    <div class="mb-6 rounded-3xl bg-white p-4 shadow-sm"><input id="sitePageSearch" onkeyup="filterSitePages()" placeholder="جستجو در عنوان یا مسیر برگه..." class="w-full rounded-2xl border px-4 py-3 md:w-96"></div>
    <div class="overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' id="adminPagesTable" class="w-full min-w-[950px]"';
$panelTable0['headAttributes'] = ' class="border-b bg-gray-50"';
$panelTable0['headerRowAttributes'] = '';
$panelTable0['columns'][] = ['html' => 'عنوان', 'attributes' => ' class="p-5 text-right"'];
$panelTable0['columns'][] = ['html' => 'متن‌های ویرایش‌شده', 'attributes' => ' class="p-5 text-right"'];
$panelTable0['columns'][] = ['html' => 'تصاویر', 'attributes' => ' class="p-5 text-right"'];
$panelTable0['columns'][] = ['html' => 'آیکن‌ها', 'attributes' => ' class="p-5 text-right"'];
$panelTable0['columns'][] = ['html' => 'وضعیت', 'attributes' => ' class="p-5 text-right"'];
$panelTable0['columns'][] = ['html' => '', 'attributes' => ' class="p-5"'];
$panelTable0['bodyAttributes'] = ' class="divide-y text-sm"';
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = '';
$panelTable0CellAttributes = ' colspan="6" class="p-12 text-center text-gray-400"';
$panelTable0Row['cells'][] = ['html' => 'در حال دریافت برگه‌ها...', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div></div>
    <?php component('panel-page-guide', [
        'title' => locale() === 'en' ? 'How to edit' : 'روش ویرایش:',
        'paragraphs' => [locale() === 'en' ? 'Select Edit content to open the actual page in a new tab. Click any text, image or icon and save its new value. Changes are saved directly to the database and shown to visitors.' : 'روی «ویرایش محتوا» بزنید؛ صفحه واقعی در تب جدید باز می‌شود. سپس روی هر متن، تصویر یا آیکن کلیک کنید و مقدار جدید را ثبت کنید. تغییرات مستقیماً در دیتابیس ذخیره و برای بازدیدکنندگان اعمال می‌شوند.'],
    ]); ?>
</div>
