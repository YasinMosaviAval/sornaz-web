<div id="page-academy-request" class="">
    <div class="max-w-xl mx-auto px-4 py-12 md:py-16">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">فرم درخواست ثبت آموزشگاه</h1>
            <p class="text-gray-500 leading-relaxed">
                پس از بررسی، نتیجه از طریق ایمیل اعلام می‌شود.
            </p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">ایمیل *</label>
                    <input id="siteReqEmail" type="email"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام کاربری *</label>
                    <input id="siteReqUsername" type="text"
                           class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">رمز عبور *</label>
                    <div class="relative">
                        <input id="siteReqPassword" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="حداقل ۸ کاراکتر">
                        <button type="button" onclick="togglePassword('siteReqPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تکرار رمز عبور *</label>
                    
                    <div class="relative">
                        <input id="siteReqPassword2" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="********">
                        <button type="button" onclick="togglePassword('siteReqPassword2', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">نام آموزشگاه *</label>
                <input id="siteReqAcademyName" type="text"
                       class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">توضیح کوتاه</label>
                <input id="siteReqShortDesc" type="text" placeholder="یک جمله درباره آموزشگاه"
                       class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">بیوگرافی</label>
                <textarea id="siteReqBio" rows="4" placeholder="تاریخچه، سبک تدریس، شعب و ..."
                          class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="submitSiteAcademyRequest()"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">
                    ثبت آموزشگاه
                </button>
                <!-- <button type="button" onclick="showSitePage('academies')"
                        class="flex-1 border border-gray-300 py-4 rounded-2xl hover:bg-gray-50 transition">
                    لغو
                </button> -->
            </div>
        </div>

        <p class="text-center text-sm text-gray-400 mt-8">
            قبلاً آموزشگاه خود را ثبت کرده‌اید؟
            <a href="/analytics/academies" class="text-indigo-600 hover:underline">بازگشت به لیست</a>
        </p>
    </div>
</div>