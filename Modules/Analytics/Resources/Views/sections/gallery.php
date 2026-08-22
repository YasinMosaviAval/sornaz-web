<style>
    .gallery-grid > .gallery-card {
        height: auto;
        min-width: 0;
        border-radius: .75rem;
    }
    .gallery-card-body > div:last-child > div > button:first-child { display: none; }
    #galleryViewerModal > div { border-radius: .75rem; }
    .gallery-card-media {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        flex: 0 0 auto;
        overflow: hidden;
        background: #e5e7eb;
    }
    .gallery-card-media > img,
    .gallery-card-media > video {
        position: absolute;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }
    .gallery-card-body { height: 13rem; min-height: 13rem; }
    .gallery-card.gallery-card-compact .gallery-card-body { height: 8.5rem; min-height: 8.5rem; }
    .gallery-card-title {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .gallery-card-description {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
    }
</style>
<div id="gallery-cover" class="section hidden" data-gallery-category="cover">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">کاور</h1>
            <p class="text-gray-500 mt-1">تصاویر کاور آموزشگاه و شعبه‌ها — کراپ ۱۶×۹</p>
        </div>
        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max gallery-owner-tabs" data-gallery-section="gallery-cover"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gallery-grid" data-gallery-section="gallery-cover" data-gallery-category="cover"></div>
</div>

<div id="gallery-logo" class="section hidden" data-gallery-category="logo">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">لوگو</h1>
            <p class="text-gray-500 mt-1">لوگو و هویت بصری — کراپ ۱×۱ دایره‌ای</p>
        </div>
        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max gallery-owner-tabs" data-gallery-section="gallery-logo"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gallery-grid" data-gallery-section="gallery-logo" data-gallery-category="logo"></div>
</div>

<div id="gallery-intro-video" class="section hidden" data-gallery-category="intro_video">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">ویدیو معرفی</h1>
            <p class="text-gray-500 mt-1">ویدیوهای معرفی آموزشگاه و شعبه‌ها</p>
        </div>
        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max gallery-owner-tabs" data-gallery-section="gallery-intro-video"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gallery-grid" data-gallery-section="gallery-intro-video" data-gallery-category="intro_video"></div>
</div>

<div id="gallery-collection" class="section hidden" data-gallery-category="gallery">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">مجموعه عکس‌ها و ویدیوها</h1>
            <p class="text-gray-500 mt-1">گالری تصاویر و ویدیوها — کراپ برای تصاویر</p>
        </div>
        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max gallery-owner-tabs" data-gallery-section="gallery-collection"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gallery-grid" data-gallery-section="gallery-collection" data-gallery-category="gallery"></div>
</div>
