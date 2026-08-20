<div id="chart-gallery" class="section hidden">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold">گالری نمودارها</h1>
            <p class="mt-2 text-gray-500">نمونه‌های تعاملی Apache ECharts برای انتخاب سریع نمودار مناسب</p>
        </div>
        <div class="rounded-2xl bg-indigo-50 px-4 py-3 text-sm text-indigo-700"><b id="chartGalleryVisibleCount">۰</b> نوع نمودار قابل نمایش</div>
    </div>
    <div class="mb-6 grid gap-3 rounded-3xl bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-4">
        <input id="chartGallerySearch" type="search" oninput="filterChartGallery()" class="rounded-xl border px-4 py-3" placeholder="جستجوی نام یا کاربرد...">
        <select id="chartGalleryFamily" onchange="filterChartGallery()" class="rounded-xl border px-4 py-3">
            <option value="all">همه خانواده‌ها</option>
            <option value="comparison">مقایسه و روند</option>
            <option value="part">سهم و ترکیب</option>
            <option value="relation">ارتباط و سلسله‌مراتب</option>
            <option value="distribution">توزیع و آماری</option>
            <option value="special">تخصصی و پیشرفته</option>
        </select>
        <select id="chartGalleryType" onchange="filterChartGallery()" class="rounded-xl border px-4 py-3">
            <option value="all">همه انواع نمودار</option>
        </select>
        <select id="chartGalleryVariant" onchange="filterChartGallery()" class="rounded-xl border px-4 py-3">
            <option value="all">نمایش هر دو پیاده‌سازی</option>
            <option value="basic">پیاده‌سازی پایه</option>
            <option value="advanced">پیاده‌سازی پیشرفته</option>
        </select>
    </div>
    <div id="chartGalleryGrid" class="space-y-6"></div>
</div>
