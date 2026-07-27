<div id="about-us" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">درباره ما</h1>
            <p class="text-gray-500 mt-1">مدیریت محتوای صفحه معرفی آموزشگاه</p>
        </div>
        <button onclick="saveAboutUs()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-save"></i> ذخیره تغییرات
        </button>
    </div>

    <!-- معرفی کوتاه -->
    <div class="bg-white rounded-3xl p-6 shadow-sm mb-6">
        <h2 class="text-xl font-bold mb-4 text-indigo-700">معرفی کلی</h2>
        <textarea id="aboutIntro" rows="4" class="w-full border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-400"
            placeholder="متن معرفی آموزشگاه..."></textarea>
    </div>

    <!-- آکاردئون‌های بخش‌ها (شبیه سرناز) -->
    <div class="space-y-4" id="aboutSections">
        <!-- با JS پر می‌شود -->
    </div>

    <!-- راه‌های ارتباطی -->
    <div class="bg-white rounded-3xl p-6 shadow-sm mt-6">
        <h2 class="text-xl font-bold mb-4 text-indigo-700">راه‌های ارتباطی</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input id="aboutEmail" type="email" placeholder="ایمیل" class="w-full border border-gray-200 rounded-2xl py-3 px-4">
            <input id="aboutWebsite" type="text" placeholder="وب‌سایت" class="w-full border border-gray-200 rounded-2xl py-3 px-4">
            <input id="aboutInstagram" type="text" placeholder="اینستاگرام" class="w-full border border-gray-200 rounded-2xl py-3 px-4">
            <input id="aboutYoutube" type="text" placeholder="یوتیوب" class="w-full border border-gray-200 rounded-2xl py-3 px-4">
        </div>
    </div>
</div>