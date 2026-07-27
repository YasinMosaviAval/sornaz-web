<div id="page-academies" class="">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">آموزشگاه‌ها</h1>
                <p class="text-gray-500">لیست آموزشگاه‌های فعال روی پلتفرم</p>
            </div>
            <a href="/analytics/send-academy-request" class="border border-indigo-300 text-indigo-600 hover:bg-indigo-50 px-5 py-3 rounded-2xl text-sm">درخواست ثبت آموزشگاه</a>
        </div>

        <div class="mb-6">
            <input type="text" id="siteAcademySearch" placeholder="جستجو نام یا شهر..." onkeyup="renderSiteAcademies()" class="w-full md:w-80 border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="siteAcademiesGrid"></div>
    </div>
</div>

<!-- جزئیات آموزشگاه -->
<!-- <div id="page-academy-detail" class="site-page">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <button onclick="showSitePage('academies')" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-right"></i> بازگشت به لیست
        </button>
        <div id="siteAcademyDetail"></div>
    </div>
</div> -->