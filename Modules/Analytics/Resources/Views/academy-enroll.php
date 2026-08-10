<div id="page-academy-enroll" class="">
    <div class="max-w-xl mx-auto px-4 py-12 md:py-16">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">ثبت‌نام در کلاس</h1>
            <p class="text-gray-500 leading-relaxed">
                پس از ثبت‌نام، با شما تماس گرفته می‌شود تا زمان کلاس نهایی شود.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-5">
            <!-- آی دی آموزشگاه مورد نظر را از آدرس سایت بگیر -->
            <!-- <div>
                <label class="block text-sm font-medium mb-2">آموزشگاه *</label>
                <select id="siteEnrollAcademy"
                        class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                    <option value="">انتخاب آموزشگاه</option>
                </select>
            </div> -->
            <div>
                <label class="block text-sm font-medium mb-2">دوره / کلاس مورد نظر *</label>
                <select id="siteEnrollCourse"
                        class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                    <option value="">انتخاب کنید</option>
                    <option value="piano-beginner">پیانو مبتدی</option>
                    <option value="piano-advanced">پیانو پیشرفته</option>
                    <option value="guitar">گیتار کلاسیک</option>
                    <option value="violin">ویولن</option>
                    <option value="santur">سنتور</option>
                    <option value="theory">تئوری موسیقی</option>
                    <option value="vocal">آواز</option>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">نام و نام خانوادگی *</label>
                    <input id="siteEnrollName" type="text"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شماره موبایل *</label>
                    <input id="siteEnrollPhone" type="tel" placeholder="09123456789" dir="ltr"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 text-left focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">ایمیل</label>
                <input id="siteEnrollEmail" type="email"
                       class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">سطح فعلی</label>
                <select id="siteEnrollLevel"
                        class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                    <option value="beginner">مبتدی</option>
                    <option value="intermediate">متوسط</option>
                    <option value="advanced">پیشرفته</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">توضیحات / درخواست خاص</label>
                <textarea id="siteEnrollNote" rows="3"
                          placeholder="زمان ترجیحی، اهداف یادگیری و ..."
                          class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <button type="button" onclick="submitSiteEnroll()"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">
                ثبت‌نام در کلاس
            </button>
        </div>

        <p class="text-center text-sm text-gray-400 mt-8">
            آموزشگاه دارید؟
            <a href="/academy/academy" class="text-indigo-600 hover:underline">بازگشت به آموزشگاه</a>
        </p>
    </div>
</div>
