<div id="gallery" class="section">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">گالری شعبه‌ها</h1>
            <p class="text-gray-500 mt-1">تصاویر و ویدیوهای آموزشگاه با توضیحات</p>
        </div>
        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>

    <!-- تاپ‌بار شعبه‌ها -->
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
    <div class="flex gap-2 min-w-max" id="galleryBranchTabs">
        <button onclick="filterGalleryByBranch('all')" class="gallery-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium bg-indigo-600 text-white">
            همه شعبه‌ها
        </button>
    </div>
</div>

    <!-- گرید گالری (شبیه اینستاگرام / یوتیوب) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="galleryGrid">
        <!-- توسط JS پر می‌شود -->
    </div>
</div>