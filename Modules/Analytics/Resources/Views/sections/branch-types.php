<?php if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())): ?>
<section id="branch-types" class="section hidden" data-csrf="<?= e(csrf_token()) ?>">
    <div class="mb-7 flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-3xl font-bold">انواع آموزشی</h1><p class="mt-2 text-gray-500">مدیریت عنوان، خلاصه و شرح انواع آموزشی شعبه‌ها</p></div><button type="button" onclick="openBranchTypeAdminModal()" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-white"><i class="fas fa-plus ml-2"></i>نوع آموزشی جدید</button></div>
    <div class="overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-sm"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' class="w-full min-w-[850px]"';
$panelTable0['headAttributes'] = ' class="bg-gray-50 text-right text-sm text-gray-500"';
$panelTable0['headerRowAttributes'] = '';
$panelTable0['columns'][] = ['html' => 'عنوان', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'خلاصه', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'شرح', 'attributes' => ' class="p-4"'];
$panelTable0['columns'][] = ['html' => 'عملیات', 'attributes' => ' class="p-4"'];
$panelTable0['bodyAttributes'] = ' id="branchTypesAdminBody"';
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = '';
$panelTable0CellAttributes = ' colspan="4" class="p-12 text-center text-gray-400"';
$panelTable0Row['cells'][] = ['html' => 'در حال بارگذاری...', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div>
</section>
<?php endif; ?>
