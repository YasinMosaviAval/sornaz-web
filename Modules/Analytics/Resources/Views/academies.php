<div id="page-academies" class="">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">آموزشگاه‌ها</h1>
                <p class="text-gray-500">لیست آموزشگاه‌های فعال روی پلتفرم</p>
            </div>
            <a href="/academy/send-academy-request" class="border border-indigo-300 text-indigo-600 hover:bg-indigo-50 px-5 py-3 rounded-2xl text-sm">ثبت آموزشگاه</a>
        </div>

        <form action="/academy/academies" method="GET" class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-4">
            <input type="text" name="q" value="<?= htmlspecialchars($academyFilters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="نام یا معرفی آموزشگاه" class="border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
            <select name="instrument" class="border border-gray-300 rounded-2xl py-3 px-4 bg-white focus:outline-none focus:border-indigo-500"><option value="">همه سازها</option><?php foreach (($academySearchOptions['instruments'] ?? []) as $instrument): ?><option value="<?= (int)$instrument['id'] ?>" <?= (int)($academyFilters['instrument']??0)===(int)$instrument['id']?'selected':'' ?>><?= htmlspecialchars($instrument['title'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
            <select name="city" dir="<?= e(direction()) ?>" class="border border-gray-300 rounded-2xl py-3 px-4 bg-white focus:outline-none focus:border-indigo-500 <?= locale()==='en'?'text-left':'text-right' ?>"><option value="">همه شهرها</option><?php foreach (($academySearchOptions['cities'] ?? []) as $city): ?><option value="<?= (int)$city['id'] ?>" <?= (int)($academyFilters['city']??0)===(int)$city['id']?'selected':'' ?>><?= htmlspecialchars($city['title'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
            <button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">جستجو</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="siteAcademiesGrid"></div>
    </div>
</div>

<script>
window.siteAcademiesData = <?= json_encode($academies ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<!-- جزئیات آموزشگاه -->
<!-- <div id="page-academy-detail" class="site-page">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <button onclick="showSitePage('academies')" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-right"></i> بازگشت به لیست
        </button>
        <div id="siteAcademyDetail"></div>
    </div>
</div> -->
