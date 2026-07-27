<div id="register" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-2xl mb-4 shadow-lg">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="text-3xl font-bold">ثبت نام</h1>
            <p class="text-gray-500 mt-2">حساب کاربری جدید بسازید</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">ایمیل *</label>
                    <input id="regEmail" type="email" autocomplete="email" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام کاربری *</label>
                    <input id="regUsername" type="text" autocomplete="username" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="username">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نام نمایشی</label>
                    <input id="regDisplayName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="نام و نام خانوادگی">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">رمز عبور *</label>
                    <div class="relative">
                        <input id="regPassword" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="حداقل ۸ کاراکتر">
                        <button type="button" onclick="togglePassword('regPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تکرار رمز عبور *</label>
                    <div class="relative">
                        <input id="regPassword2" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="تکرار رمز">
                        <button type="button" onclick="togglePassword('regPassword2', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <label class="flex items-start gap-2 text-sm text-gray-600 cursor-pointer">
                    <!-- <input type="checkbox" id="regTerms" class="mt-1 rounded border-gray-300 text-indigo-600"> -->
                    <input type="checkbox" id="regTerms" class="mt-1 rounded border-gray-300 text-indigo-600">
                        <span>
                            <button type="button" onclick="openTermsModal()" class="text-indigo-600 hover:underline font-medium">
                                قوانین و شرایط استفاده
                            </button>
                            را می‌پذیرم
                        </span>
                    </label>
                    <!-- <span>قوانین و شرایط استفاده را می‌پذیرم</span> -->
                </label>
                <label class="flex items-start gap-2 text-sm text-gray-600">
                
                <button onclick="handleRegister()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">ثبت نام</button>
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">یا</span></div>
            </div>
            <a href="/analytics/login" class="w-full border border-gray-300 hover:bg-gray-50 py-3.5 rounded-2xl font-medium transition text-center block">ورود</a>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/analytics/home" class="hover:text-indigo-600">بازگشت به صفحه اصلی</a>
            <!-- &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -->
            <!-- <button onclick="showSection('academy-requests')" class="hover:text-indigo-600">درخواست ثبت آموزشگاه</button> -->
        </p>
    </div>
</div>