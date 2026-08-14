<div id="posts" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">نوشته‌ها</h1>
            <p class="text-gray-500 mt-1">مدیریت پست‌ها، محصولات و مطالب تئوری موسیقی</p>
        </div>
        <button onclick="openPostEditor()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن نوشته
        </button>
    </div>

    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex flex-wrap gap-2" id="postsStatusTabs">
            <?php foreach (['all'=>'همه','published'=>'منتشرشده','draft'=>'پیش‌نویس','pending'=>'در انتظار','private'=>'خصوصی','trash'=>'زباله‌دان','future'=>'زمان‌بندی‌شده'] as $key=>$label): ?>
                <button data-status="<?= $key ?>" onclick="filterPostsByStatus('<?= $key ?>')" class="post-status-tab px-4 py-2 rounded-xl text-sm font-medium <?= $key==='all'?'bg-gray-900 text-white':'border border-gray-200 hover:bg-gray-50' ?>"><?= $label ?> <span class="opacity-60" data-post-status-count="<?= $key ?>">0</span></button>
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
            <table class="w-full min-w-[1200px]" id="postsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right py-5 px-5 font-medium">عنوان</th>
                        <th class="text-right py-5 px-5 font-medium">نویسنده</th>
                        <th class="text-right py-5 px-5 font-medium">دسته‌ها</th>
                        <th class="text-right py-5 px-5 font-medium">نوع</th>
                        <th class="text-right py-5 px-5 font-medium">وضعیت</th>
                        <th class="text-right py-5 px-5 font-medium">بازدید</th>
                        <th class="text-right py-5 px-5 font-medium">تاریخ</th>
                        <th class="w-36 py-5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm"></tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t p-4 text-sm text-gray-500 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3"><span id="postsPaginationInfo">نمایش ۰ نوشته</span><select id="postsPerPage" onchange="changePostsPerPage(this.value)" class="rounded-xl border px-3 py-2"><option>10</option><option selected>20</option><option>30</option><option>50</option><option>100</option></select></div>
            <div id="postsPaginationButtons" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
</div>
