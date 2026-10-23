<div id="media" class="section hidden">
    <?php component('posts-page-tabs', ['active'=>'media']); ?>
 <div class="mb-6 flex flex-wrap items-center justify-between gap-4"><button data-panel-page-action="media" onclick="openMediaUpload()" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><i class="fas fa-plus ml-2"></i>افزودن رسانه</button></div>
 <?php
 $viewModeTableId='mediaListButton'; $viewModeCardsId='mediaGridButton';
 $viewModeTableAttributes='onclick="setMediaView(\'list\')"'; $viewModeCardsAttributes='onclick="setMediaView(\'grid\')"';
 require __DIR__.'/view-mode-toggle.php'; ?>
 <div class="bg-white rounded-3xl p-4 mb-6 shadow-sm"><div data-panel-filter-row class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <input id="mediaSearch" oninput="searchAdminMedia()" class="w-full border border-gray-300 rounded-2xl px-4 py-3" placeholder="<?= e(locale()==='en' ? 'Search images...' : 'جستجوی تصاویر...') ?>">
 <select id="mediaMonth" onchange="loadAdminMedia(1)" class="w-full border border-gray-300 rounded-2xl px-4 py-3"><option value=""><?= e(locale()==='en' ? 'All dates' : 'همه تاریخ‌ها') ?></option></select>
 </div></div>
 <div id="mediaGrid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6"></div>
 <div id="mediaList" class="hidden overflow-hidden rounded-3xl bg-white shadow"><div class="overflow-x-auto"><?php component('panel-table-template', [
                'attributes' => ' id="mediaTable" class="w-full min-w-[850px]"',
                'headAttributes' => ' class="bg-gray-50"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' id="mediaListBody" class="divide-y"',
                'columns' => [
                    ['html' => 'فایل', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'نوع', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'حجم', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => 'تاریخ', 'attributes' => ' class="p-4 text-right"'],
                    ['html' => '', 'attributes' => ' class="p-4"'],
                ],
                'rows' => [],
            ]); ?></div></div>
 <?php component('panel-table-pagination', ['id'=>'mediaPagination']); ?>
 <input id="mediaFileInput" type="file" multiple hidden accept="image/png,image/jpeg,image/webp,image/gif" onchange="uploadMediaFiles(this.files)">
</div>
