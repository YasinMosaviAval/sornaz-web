<div id="articles" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مقاله‌های آموزشی</h1>
            <p class="text-gray-500 mt-1">تئوری موسیقی ایرانی و جهانی، ردیف، دستگاه‌ها و زندگینامه موسیقیدانان</p>
        </div>
        <button onclick="openAddArticleModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن مقاله
        </button>
    </div>

    <!-- فیلتر دسته (شبیه dansim) -->
    <div class="bg-white rounded-3xl p-4 mb-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 mb-3">فیلتر بر اساس دسته‌بندی</p>
        <div class="flex flex-wrap gap-2" id="articleCategoryTabs">
            <button onclick="filterArticlesByCategory('all')" class="article-cat-tab px-4 py-2 rounded-xl text-sm bg-indigo-600 text-white">همه</button>
        </div>
    </div>

    <div class="mb-4">
        <input type="text" id="articleSearch" placeholder="جستجو در عنوان و متن..."
               onkeyup="filterArticles()"
               class="w-full md:w-96 border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
    </div>

    <!-- کارت‌های مقاله -->
    <div class="space-y-4" id="articlesList">
        <!-- توسط JS -->
    </div>
</div>