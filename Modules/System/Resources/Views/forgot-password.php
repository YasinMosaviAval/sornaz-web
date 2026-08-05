<div id="forgot-password" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-2xl mb-4 shadow-lg">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="text-3xl font-bold">بازیابی رمز عبور</h1>
            <p class="text-gray-500 mt-2">کد تأیید را از طریق ایمیل یا پیامک دریافت کنید</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">

            <!-- مرحله ۱: انتخاب روش + ورود شناسه -->
            <div id="fpStep1" class="space-y-5">
                <p class="text-sm text-gray-500 text-center mb-2">روش دریافت کد را انتخاب کنید</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="fpMethodEmail" onclick="setFpMethod('email')" class="fp-method border-2 border-indigo-600 bg-indigo-50 text-indigo-700 py-3 rounded-2xl font-medium text-sm">
                        <i class="fas fa-envelope mb-1 block text-lg"></i>
                        ایمیل
                    </button>
                    <button type="button" id="fpMethodPhone" onclick="setFpMethod('phone')" class="fp-method border-2 border-gray-200 hover:border-gray-300 py-3 rounded-2xl font-medium text-sm text-gray-600">
                        <i class="fas fa-mobile-alt mb-1 block text-lg"></i>
                        پیامک (OTP)
                    </button>
                </div>

                <div id="fpEmailBox">
                    <label class="block text-sm font-medium mb-2">ایمیل ثبت‌شده</label>
                    <input id="fpEmail" type="email" placeholder="email@example.com" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500">
                </div>
                <div id="fpPhoneBox" class="hidden">
                    <label class="block text-sm font-medium mb-2">شماره موبایل ثبت‌شده</label>
                    <input id="fpPhone" type="tel" placeholder="09123456789" dir="ltr" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500 text-left">
                </div>

                <button onclick="sendFpOtp()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">
                    ارسال کد تأیید
                </button>
            </div>

            <!-- مرحله ۲: وارد کردن OTP -->
            <div id="fpStep2" class="hidden space-y-5">
                <p class="text-sm text-gray-500 text-center">
                    کد ۶ رقمی به
                    <strong id="fpSentTo" class="text-gray-800"></strong>
                    ارسال شد
                </p>
                <div class="flex justify-center gap-2 dir-ltr" id="fpOtpInputs">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                    <input maxlength="1" class="fp-otp w-12 h-14 text-center text-xl font-bold border border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none" inputmode="numeric">
                </div>
                <p class="text-center text-sm text-gray-400">
                    <span id="fpTimer">۰۲:۰۰</span>
                    ·
                    <button type="button" id="fpResendBtn" onclick="sendFpOtp()" disabled class="text-indigo-600 disabled:text-gray-300 disabled:cursor-not-allowed">
                        ارسال مجدد
                    </button>
                </p>
                <button onclick="verifyFpOtp()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">
                    تأیید کد
                </button>
                <button onclick="fpGoStep(1)" class="w-full text-sm text-gray-500 hover:text-indigo-600">
                    تغییر ایمیل / شماره
                </button>
            </div>

            <!-- مرحله ۳: رمز جدید -->
            <div id="fpStep3" class="hidden space-y-5">
                <p class="text-sm text-gray-500 text-center">رمز عبور جدید خود را وارد کنید</p>
                <div>
                    <label class="block text-sm font-medium mb-2">رمز عبور جدید</label>
                    <div class="relative">
                        <input id="fpNewPass" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 pr-12 focus:outline-none focus:border-indigo-500" placeholder="حداقل ۸ کاراکتر">
                        <button type="button" onclick="togglePassword('fpNewPass', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تکرار رمز عبور</label>
                    <input id="fpNewPass2" type="password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="تکرار رمز">
                </div>
                <button onclick="resetPassword()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium">ذخیره رمز جدید</button>
            </div>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/system/login" class="hover:text-indigo-600">بازگشت به صفحه ورود</ش>
        </p>
    </div>
</div>