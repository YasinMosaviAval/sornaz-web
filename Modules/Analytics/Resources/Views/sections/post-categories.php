<div id="post-categories" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div><h1 class="text-3xl font-bold">دسته‌بندی نوشته‌ها</h1><p class="mt-1 text-gray-500">مدیریت دسته‌بندی‌های قابل استفاده برای نوشته‌ها و مقاله‌های آموزشی</p></div>
        <button type="button" onclick="openPostCategoryForm()" class="flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700"><i class="fas fa-plus"></i> افزودن دسته‌بندی</button>
    </div>
    <div class="mb-6 rounded-3xl bg-white p-4 shadow-sm">
        <input id="postCategorySearch" type="search" oninput="filterPostCategories()" placeholder="جستجو در عنوان، نامک یا گروه..." class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none">
    </div>
    <div class="overflow-hidden rounded-3xl bg-white shadow">
        <div class="overflow-x-auto"><table id="postCategoriesTable" class="w-full min-w-[850px]"><thead class="border-b bg-gray-50"><tr><th class="px-5 py-5 text-right font-medium">عنوان فارسی</th><th class="px-5 py-5 text-right font-medium">عنوان انگلیسی</th><th class="px-5 py-5 text-right font-medium">نامک</th><th class="px-5 py-5 text-right font-medium">گروه</th><th class="px-5 py-5 text-right font-medium">تعداد نوشته‌ها</th><th class="w-40 px-5 py-5"></th></tr></thead><tbody class="divide-y text-sm"><tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">در حال دریافت دسته‌بندی‌ها...</td></tr></tbody></table></div>
        <div id="postCategoriesCount" class="border-t p-4 text-sm text-gray-500">۰ دسته‌بندی</div>
    </div>
</div>
