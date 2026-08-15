<div id="page-articles" class="">
    <script>window.siteArticlesData = <?= json_encode($articles ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">مقاله‌های آموزشی</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                در این بخش به بررسی تئوری موسیقی ایرانی و جهانی، ردیف، دستگاه‌ها، فرم‌ها و بیوگرافی موسیقیدانان می‌پردازیم.
            </p>
        </div>

        <!-- فیلتر دسته -->
        <div class="bg-white rounded-3xl p-4 mb-6 shadow-sm">
            <p class="text-sm font-medium text-gray-500 mb-3">فیلتر بر اساس دسته‌بندی</p>
            <div class="flex flex-wrap gap-2" id="siteArticleCats">
                <button onclick="filterSiteArticles('all')" class="site-art-cat px-4 py-2 rounded-xl text-sm bg-indigo-600 text-white">همه</button>
            </div>
        </div>

        <div class="mb-6">
            <input type="text" id="siteArticleSearch" placeholder="جستجو در مقالات..." onkeyup="filterSiteArticles()" class="w-full md:w-96 border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
        </div>

        <div class="space-y-5" id="siteArticlesList" data-dynamic-content></div>
    </div>
</div>

<style>[data-dynamic-content][data-page-content-key], [data-dynamic-content] [data-page-content-key] { outline: none !important; cursor: default !important; }</style>
<script>
document.addEventListener('click', function (event) {
    if (new URLSearchParams(location.search).get('cms') === '1' && event.target.closest('[data-dynamic-content]')) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);
</script>

<!-- جزئیات مقاله (صفحه جدا) -->
<!-- <div id="page-article-detail" class="site-page">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <button onclick="showSitePage('articles')" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-right"></i> بازگشت به لیست مقالات
        </button>
        <div id="siteArticleDetail"></div>
    </div>
</div> -->
