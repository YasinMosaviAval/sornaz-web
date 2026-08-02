<div id="account" class="section hidden">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">حساب کاربری آموزشگاه</h1>
        <p class="text-gray-500 mt-1">مدیریت پروفایل، امنیت، حریم خصوصی و پشتیبان‌گیری</p>
    </div>

    <!-- کاور پروفایل (افقی مانند یوتیوب) -->
    <div class="bg-white rounded-3xl shadow overflow-hidden mb-8">
        <div class="relative group">
            <div id="accountCoverPreview" class="w-full aspect-[21/6] min-h-[140px] max-h-[280px] bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 flex items-center justify-center overflow-hidden">
                <div id="accountCoverPlaceholder" class="text-center text-white/90 px-4">
                    <i class="fas fa-image text-4xl mb-2 opacity-80"></i>
                    <p class="text-sm">کاور پروفایل آموزشگاه</p>
                    <p class="text-xs opacity-75 mt-1">نسبت پیشنهادی حدود ۲۱:۶ (افقی عریض)</p>
                </div>
                <img id="accountCoverImg" src="" alt="کاور" class="absolute inset-0 w-full h-full object-cover hidden">
            </div>
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition flex items-end justify-end p-4 gap-2">
                <label class="opacity-0 group-hover:opacity-100 transition bg-white text-gray-800 px-4 py-2.5 rounded-xl text-sm cursor-pointer shadow flex items-center gap-2 hover:bg-gray-50">
                    <i class="fas fa-camera"></i> تغییر کاور
                    <input type="file" id="accountCoverInput" accept="image/*" class="hidden" onchange="onAccountCoverChange(event)">
                </label>
                <button type="button" id="accountCoverRemoveBtn" onclick="removeAccountCover()"
                        class="hidden opacity-0 group-hover:opacity-100 transition bg-red-500 text-white px-4 py-2.5 rounded-xl text-sm shadow hover:bg-red-600">
                    <i class="fas fa-trash-alt"></i> حذف
                </button>
            </div>
        </div>
        <!-- نوار زیر کاور: آواتار + نام (پیش‌نمایش سبک پروفایل عمومی) -->
        <div class="px-6 pb-5 pt-0 relative">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 sm:-mt-12">
                <div class="relative shrink-0 self-center sm:self-auto">
                    <div id="accountAvatarPreview" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border-4 border-white shadow-lg">
                        <i class="fas fa-music text-3xl sm:text-4xl text-indigo-600" id="accountAvatarIcon"></i>
                        <img id="accountAvatarImg" src="" alt="" class="w-full h-full object-cover hidden">
                    </div>
                    <label class="absolute bottom-1 left-1 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-indigo-700 shadow">
                        <i class="fas fa-camera text-xs"></i>
                        <input type="file" id="accountAvatarInput" accept="image/*" class="hidden" onchange="onAccountAvatarChange(event)">
                    </label>
                </div>
                <div class="flex-1 text-center sm:text-right pt-2 sm:pb-1">
                    <h2 class="text-xl sm:text-2xl font-bold" id="academyName">موزیک آکادمی</h2>
                    <p class="text-gray-500 text-sm mt-0.5" id="academyTypeLabel">آموزشگاه موسیقی</p>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2" id="academyShortIntro">—</p>
                </div>
                <div class="shrink-0 self-center sm:self-end pb-1">
                    <button onclick="openEditProfileModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm">
                        ویرایش پروفایل
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
        <!-- اطلاعات اصلی -->
        <div class="bg-white rounded-3xl p-6 shadow">
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
