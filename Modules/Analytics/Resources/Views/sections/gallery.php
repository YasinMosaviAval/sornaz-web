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
<?php if ((int) auth()->id() !== 1): ?>
<div id="gallery" class="section hidden">
    <div class="mb-6 flex flex-wrap items-center justify-end gap-4">
        <button type="button" onclick="openAddGalleryModal()" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700"><i class="fas fa-plus ml-2"></i>افزودن رسانه</button>
    </div>
    <div class="mb-6 overflow-x-auto rounded-3xl bg-white p-3 shadow-sm"><div id="galleryBranchTabs" class="flex min-w-max gap-2"><button type="button" data-value="all" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white">همه</button></div></div>
    <?php foreach (['cover' => 'کاور', 'logo' => 'لوگو', 'intro_video' => 'ویدیو معرفی', 'gallery' => 'گالری'] as $galleryKind => $galleryLabel): ?>
    <section class="mb-8 border-b pb-8 last:border-b-0" style="border-color:color-mix(in srgb,var(--theme-500) 24%,transparent)" data-gallery-overview-category="<?= e($galleryKind) ?>">
        <h2 class="mb-5 text-xl font-bold"><?= e($galleryLabel) ?></h2>
        <div class="gallery-grid grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" data-gallery-category="<?= e($galleryKind) ?>"></div>
    </section>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<div id="gallery-cover" class="section hidden" data-gallery-category="cover">
    <?php component('gallery-page-tabs', ['active' => 'gallery-cover']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

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
    <?php component('gallery-page-tabs', ['active' => 'gallery-logo']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

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
    <?php component('gallery-page-tabs', ['active' => 'gallery-intro-video']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

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
    <?php component('gallery-page-tabs', ['active' => 'gallery-collection']); ?>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <button onclick="openAddGalleryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-plus"></i> افزودن آیتم جدید
        </button>
    </div>
    <div class="bg-white rounded-3xl p-3 mb-6 shadow-sm overflow-x-auto">
        <div class="flex gap-2 min-w-max gallery-owner-tabs" data-gallery-section="gallery-collection"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gallery-grid" data-gallery-section="gallery-collection" data-gallery-category="gallery"></div>
</div>
