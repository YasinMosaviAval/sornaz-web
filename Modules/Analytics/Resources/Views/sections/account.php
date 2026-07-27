<div id="account" class="section">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">حساب کاربری آموزشگاه</h1>
        <p class="text-gray-500 mt-1">مدیریت اطلاعات اصلی و تنظیمات حساب</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- کارت پروفایل -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex flex-col items-center text-center">
                <div class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                    <i class="fas fa-music text-4xl text-indigo-600"></i>
                </div>
                <h2 class="text-xl font-bold" id="academyName">موزیک آکادمی</h2>
                <p class="text-gray-500 text-sm mt-1">آموزشگاه موسیقی</p>
                <button onclick="openEditProfileModal()" class="mt-5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">
                    ویرایش پروفایل
                </button>
            </div>
        </div>

        <!-- اطلاعات اصلی -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow">
            <h3 class="font-bold text-lg mb-5">اطلاعات اصلی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm" id="accountInfo">
                <!-- توسط JS پر می‌شود -->
            </div>
        </div>
    </div>

    <!-- تنظیمات امنیتی -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <h3 class="font-bold text-lg mb-5">تنظیمات امنیتی</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">ایمیل مدیریت</label>
                <input type="email" id="accountEmail" value="admin@musicacademy.ir" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">شماره تماس اصلی</label>
                <input type="text" id="accountPhone" value="۰۲۱-۸۸۷۷۶۶۵۵" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">رمز عبور جدید</label>
                <input type="password" placeholder="در صورت نیاز به تغییر" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">تکرار رمز عبور</label>
                <input type="password" placeholder="تکرار رمز جدید" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
        </div>
        <div class="mt-6">
            <button onclick="saveAccountSettings()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl">
                ذخیره تغییرات
            </button>
        </div>
    </div>
</div>