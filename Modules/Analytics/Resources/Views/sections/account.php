<div id="account" class="section hidden">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">حساب کاربری آموزشگاه</h1>
        <p class="text-gray-500 mt-1">مدیریت پروفایل، امنیت، حریم خصوصی و پشتیبان‌گیری</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- کارت پروفایل -->
        <div class="bg-white rounded-3xl p-6 shadow">
            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    <div id="accountAvatarPreview" class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border-4 border-white shadow">
                        <i class="fas fa-music text-4xl text-indigo-600" id="accountAvatarIcon"></i>
                        <img id="accountAvatarImg" src="" alt="" class="w-full h-full object-cover hidden">
                    </div>
                    <label class="absolute bottom-0 left-0 w-9 h-9 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-indigo-700 shadow">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="accountAvatarInput" accept="image/*" class="hidden" onchange="onAccountAvatarChange(event)">
                    </label>
                </div>
                <h2 class="text-xl font-bold" id="academyName">موزیک آکادمی</h2>
                <p class="text-gray-500 text-sm mt-1" id="academyTypeLabel">آموزشگاه موسیقی</p>
                <p class="text-sm text-gray-600 mt-3 line-clamp-2 px-2" id="academyShortIntro">—</p>
                <button onclick="openEditProfileModal()" class="mt-5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">
                    ویرایش پروفایل
                </button>
            </div>
        </div>

        <!-- اطلاعات اصلی -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow">
            <h3 class="font-bold text-lg mb-5">اطلاعات اصلی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm" id="accountInfo"></div>
        </div>
    </div>

    <!-- معرفی و بیوگرافی -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-lg">معرفی و بیوگرافی</h3>
            <button onclick="openEditBioModal()" class="text-indigo-600 text-sm hover:underline">ویرایش</button>
        </div>
        <div class="space-y-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">معرفی کوتاه</p>
                <p class="text-sm text-gray-700" id="accountShortIntroText">—</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">بیوگرافی کامل</p>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" id="accountBioText">—</p>
            </div>
        </div>
    </div>

    <!-- اسناد آموزشگاه -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
            <h3 class="font-bold text-lg">اسناد آموزشگاه</h3>
            <label class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2.5 rounded-xl text-sm cursor-pointer hover:bg-indigo-100">
                <i class="fas fa-upload"></i> آپلود سند
                <input type="file" id="accountDocInput" class="hidden" multiple onchange="onAccountDocumentUpload(event)">
            </label>
        </div>
        <div id="accountDocumentsList" class="space-y-3"></div>
    </div>

    <!-- دستگاه‌های فعال -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <h3 class="font-bold text-lg mb-5">دستگاه‌های متصل</h3>
        <div id="accountDevicesList" class="space-y-3"></div>
    </div>

    <!-- تاریخچه ورود و هشدارها -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <div class="bg-white rounded-3xl p-6 shadow">
            <h3 class="font-bold text-lg mb-5">تاریخچه ورود و خروج</h3>
            <div id="accountLoginHistory" class="space-y-3 max-h-80 overflow-y-auto"></div>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow">
            <h3 class="font-bold text-lg mb-5">هشدارهای امنیتی</h3>
            <div id="accountSecurityAlerts" class="space-y-3 max-h-80 overflow-y-auto"></div>
        </div>
    </div>

    <!-- حریم خصوصی -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <h3 class="font-bold text-lg mb-5">حریم خصوصی و نمایش عمومی</h3>
        <div class="space-y-4" id="accountPrivacyOptions"></div>
        <div class="mt-6">
            <button onclick="savePrivacySettings()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm">
                ذخیره تنظیمات حریم خصوصی
            </button>
        </div>
    </div>

    <!-- تنظیمات امنیتی حساب -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8">
        <h3 class="font-bold text-lg mb-5">تنظیمات امنیتی حساب</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">ایمیل مدیریت</label>
                <input type="email" id="accountEmail" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">شماره تماس اصلی</label>
                <input type="text" id="accountPhone" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">رمز عبور جدید</label>
                <input type="password" id="accountPassword" placeholder="در صورت نیاز به تغییر" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">تکرار رمز عبور</label>
                <input type="password" id="accountPasswordConfirm" placeholder="تکرار رمز جدید" class="w-full border border-gray-300 rounded-2xl py-3 px-5">
            </div>
        </div>
        <div class="mt-6">
            <button onclick="saveAccountSettings()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl">
                ذخیره تغییرات
            </button>
        </div>
    </div>

    <!-- پشتیبان‌گیری -->
    <div class="bg-white rounded-3xl p-6 shadow mt-8 mb-4">
        <h3 class="font-bold text-lg mb-2">پشتیبان‌گیری کامل</h3>
        <p class="text-sm text-gray-500 mb-5">از تمام اطلاعات آموزشگاه (کاربران، شعبه‌ها، گزارش‌ها و تنظیمات) نسخه پشتیبان بگیرید.</p>
        <div class="flex flex-wrap gap-3">
            <button onclick="createFullBackup()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm flex items-center gap-2">
                <i class="fas fa-database"></i> ایجاد پشتیبان کامل
            </button>
            <button onclick="downloadLastBackup()" class="border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-2xl text-sm flex items-center gap-2">
                <i class="fas fa-download"></i> دانلود آخرین پشتیبان
            </button>
        </div>
        <div id="accountBackupStatus" class="mt-4 text-sm text-gray-500"></div>
    </div>
</div>
