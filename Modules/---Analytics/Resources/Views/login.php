<!-- <div id="login" class="section hidden min-h-[80vh] flex items-center justify-center py-10"> -->
<div id="login" class="min-h-[80vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md">
        <!-- لوگو / عنوان -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-2xl mb-4 shadow-lg">
                <i class="fas fa-music"></i>
            </div>
            <h1 class="text-3xl font-bold">ورود به حساب</h1>
            <p class="text-gray-500 mt-2">به پنل آموزشگاه خوش آمدید</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">ایمیل یا نام کاربری</label>
                    <input id="loginUser" type="text" autocomplete="username" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">رمز عبور</label>
                    <div class="relative">
                        <input id="loginPassword" type="password" autocomplete="current-password" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('loginPassword', this)" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="loginRemember" class="rounded border-gray-300 text-indigo-600">
                        <span class="text-gray-600">مرا به خاطر بسپار</span>
                    </label>
                    <a href="/analytics/forgot-password" class="text-indigo-600 hover:underline">فراموشی رمز عبور</a>
                </div>
                <button onclick="handleLogin()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition">ورود</button>
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-gray-400">یا</span></div>
            </div>

            <a href="/analytics/register" class="w-full border border-gray-300 hover:bg-gray-50 py-3.5 rounded-2xl font-medium transition text-center block">ثبت نام</a>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            <a href="/analytics/home" class="hover:text-indigo-600">بازگشت به صفحه اصلی</a>
        </p>
    </div>
</div>