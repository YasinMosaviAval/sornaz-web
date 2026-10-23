<div id="chart-gallery" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

        <div class="rounded-2xl bg-indigo-50 px-4 py-3 text-sm text-indigo-700"><b id="chartGalleryVisibleCount">۰</b> نوع نمودار قابل نمایش</div>
    </div>
    <div class="mb-6 rounded-3xl bg-white p-4 shadow-sm"><div data-panel-filter-row class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input id="chartGallerySearch" type="search" oninput="filterChartGallery()" class="min-w-0 w-full border border-gray-300 rounded-2xl py-3 px-4" placeholder="جستجوی نام یا کاربرد...">
        <select id="chartGalleryFamily" onchange="filterChartGallery()" class="min-w-0 w-full border border-gray-300 rounded-2xl py-3 px-4">
            <option value="all">همه خانواده‌ها</option>
            <option value="comparison">مقایسه و روند</option>
            <option value="part">سهم و ترکیب</option>
            <option value="relation">ارتباط و سلسله‌مراتب</option>
            <option value="distribution">توزیع و آماری</option>
            <option value="special">تخصصی و پیشرفته</option>
        </select>
        <select id="chartGalleryType" onchange="filterChartGallery()" class="min-w-0 w-full border border-gray-300 rounded-2xl py-3 px-4">
            <option value="all">همه انواع نمودار</option>
        </select>
        <select id="chartGalleryVariant" onchange="filterChartGallery()" class="min-w-0 w-full border border-gray-300 rounded-2xl py-3 px-4">
            <option value="all">نمایش هر دو پیاده‌سازی</option>
            <option value="basic">پیاده‌سازی پایه</option>
            <option value="advanced">پیاده‌سازی پیشرفته</option>
        </select>
    </div>
    </div>
    <div id="chartGalleryGrid" class="space-y-6"></div>
</div>
