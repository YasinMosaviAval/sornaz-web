<div id="post-categories" class="section hidden">
    <?php component('panel-page-tabs', [
        'active'=>'post-categories', 'label'=>locale()==='en' ? 'Posts' : 'نوشته‌ها',
        'tabs'=>[
            'posts'=>['labels'=>['نوشته‌ها','Posts']],
            'post-categories'=>['labels'=>['دسته‌بندی نوشته‌ها','Post categories']],
        ],
    ]); ?>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div><h1 class="text-3xl font-bold">دسته‌بندی نوشته‌ها</h1><p class="mt-1 text-gray-500">مدیریت دسته‌بندی‌های قابل استفاده برای نوشته‌ها و مقاله‌های آموزشی</p></div>
        <button type="button" onclick="openPostCategoryForm()" class="flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700"><i class="fas fa-plus"></i> افزودن دسته‌بندی</button>
    </div>
    <div class="mb-6 rounded-3xl bg-white p-4 shadow-sm">
        <input id="postCategorySearch" type="search" oninput="filterPostCategories()" placeholder="جستجو در عنوان، نامک یا گروه..." class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none">
    </div>
    <div class="overflow-hidden rounded-3xl bg-white shadow">
        <div class="overflow-x-auto"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' id="postCategoriesTable" class="w-full min-w-[850px]"';
$panelTable0['headAttributes'] = ' class="border-b bg-gray-50"';
$panelTable0['headerRowAttributes'] = '';
$panelTable0['columns'][] = ['html' => 'عنوان فارسی', 'attributes' => ' class="px-5 py-5 text-right font-medium"'];
$panelTable0['columns'][] = ['html' => 'عنوان انگلیسی', 'attributes' => ' class="px-5 py-5 text-right font-medium"'];
$panelTable0['columns'][] = ['html' => 'نامک', 'attributes' => ' class="px-5 py-5 text-right font-medium"'];
$panelTable0['columns'][] = ['html' => 'گروه', 'attributes' => ' class="px-5 py-5 text-right font-medium"'];
$panelTable0['columns'][] = ['html' => 'تعداد نوشته‌ها', 'attributes' => ' class="px-5 py-5 text-right font-medium"'];
$panelTable0['columns'][] = ['html' => '', 'attributes' => ' class="w-40 px-5 py-5"'];
$panelTable0['bodyAttributes'] = ' class="divide-y text-sm"';
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = '';
$panelTable0CellAttributes = ' colspan="6" class="px-5 py-12 text-center text-gray-400"';
$panelTable0Row['cells'][] = ['html' => 'در حال دریافت دسته‌بندی‌ها...', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div>
        <div id="postCategoriesCount" class="border-t p-4 text-sm text-gray-500">۰ دسته‌بندی</div>
    </div>
</div>
