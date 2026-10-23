<div id="posts" class="section hidden">
    <?php component('posts-page-tabs', ['active'=>'posts']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <button data-panel-page-action="posts" onclick="openPostEditor()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن نوشته
        </button>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex flex-wrap gap-2" id="postsStatusTabs">
            <?php foreach (['all'=>'همه','published'=>'منتشرشده','draft'=>'پیش‌نویس','pending'=>'در انتظار','private'=>'خصوصی','trash'=>'زباله‌دان','future'=>'زمان‌بندی‌شده'] as $key=>$label): ?>
                <button data-status="<?= $key ?>" onclick="filterPostsByStatus('<?= $key ?>')" class="post-status-tab px-4 py-2 rounded-xl text-sm font-medium <?= $key==='all'?'bg-indigo-600 text-white':'border border-gray-200 hover:bg-gray-50' ?>"><?= $label ?> <span class="opacity-60" data-post-status-count="<?= $key ?>">0</span></button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- فیلتر سریع -->
    <div class="bg-white rounded-3xl p-4 mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="postSearch" placeholder="جستجو عنوان / اسلاگ..."
                   onkeyup="filterPosts()"
                   class="w-full border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
            <select id="postTypeFilter" onchange="filterPosts()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه انواع</option>
                <option value="post">نوشته</option>
                <option value="product">محصول</option>
                <option value="music_theory">تئوری موسیقی</option>
            </select>
            <select id="postVisibilityFilter" onchange="filterPosts()" class="w-full border border-gray-300 rounded-2xl py-3 px-4">
                <option value="">همه سطوح دسترسی</option>
                <option value="public">عمومی</option>
                <option value="private">خصوصی</option>
                <option value="followers">دنبال‌کنندگان</option>
                <option value="premium">ویژه</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <?php component('panel-table-template', [
                'attributes' => ' class="w-full min-w-[1200px]" id="postsTable"',
                'headAttributes' => ' class="bg-gray-50 border-b"',
                'headerRowAttributes' => '',
                'bodyAttributes' => ' class="divide-y text-sm"',
                'columns' => [
                    ['html' => 'عنوان', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'نویسنده', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'دسته‌ها', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'نوع', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'وضعیت', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'بازدید', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => 'تاریخ', 'attributes' => ' class="text-right py-5 px-5 font-medium"'],
                    ['html' => '', 'attributes' => ' class="w-36 py-5 px-5"'],
                ],
                'rows' => [],
            ]); ?>
        </div>
        <?php component('panel-table-pagination', ['id'=>'postsPagination']); ?>
    </div>
</div>
