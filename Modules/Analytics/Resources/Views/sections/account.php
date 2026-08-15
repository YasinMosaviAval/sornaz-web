<div id="account" class="section hidden">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">حساب کاربری آموزشگاه</h1>
        <p class="text-gray-500 mt-1">مدیریت پروفایل، امنیت، حریم خصوصی و پشتیبان‌گیری</p>
    </div>

    <!-- کاور پروفایل ۱۶:۹ (معادل ۱۹۲۰×۱۰۸۰) -->
    <div class="bg-white rounded-3xl shadow overflow-hidden mb-8">
        <div class="relative">
            <div id="accountCoverPreview" class="w-full aspect-video max-h-[640px] bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 flex items-center justify-center overflow-hidden">
                <div id="accountCoverPlaceholder" class="text-center text-white/90 px-4 pointer-events-none">
                    <i class="fas fa-image text-4xl mb-2 opacity-80"></i>
                    <p class="text-sm">کاور پروفایل آموزشگاه</p>
                    <p class="text-xs opacity-75 mt-1">قالب پیشنهادی ۱۹۲۰×۱۰۸۰ (۱۶:۹ افقی)</p>
                </div>
                <img id="accountCoverImg" src="" alt="کاور" class="absolute inset-0 w-full h-full object-cover hidden pointer-events-none">
            </div>
            <!-- دکمه‌ها همیشه قابل کلیک (بدون hover محو) -->
            <div class="absolute top-3 left-3 z-20 flex flex-wrap items-center gap-2">
                <label class="bg-white/95 hover:bg-white text-gray-800 px-4 py-2.5 rounded-xl text-sm cursor-pointer shadow-md flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-camera"></i> تغییر کاور
                    <input type="file" id="accountCoverInput" accept="image/*" class="hidden" onchange="onAccountCoverChange(event)">
                </label>
                <button type="button" id="accountCoverRemoveBtn" onclick="removeAccountCover()"
                        class="hidden bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-xl text-sm shadow-md">
                    <i class="fas fa-trash-alt"></i> حذف کاور
                </button>
            </div>
        </div>
        <!-- نام و آواتار روی پس‌زمینه سفید جدا از کاور -->
        <div class="bg-white border-t border-gray-100 px-6 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="relative shrink-0 self-center sm:self-auto">
                    <div id="accountAvatarPreview" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border-4 border-white shadow-lg ring-1 ring-gray-100">
                        <i class="fas fa-music text-3xl sm:text-4xl text-indigo-600" id="accountAvatarIcon"></i>
                        <img id="accountAvatarImg" src="" alt="" class="w-full h-full object-cover hidden">
                    </div>
                    <label class="absolute bottom-1 left-1 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-indigo-700 shadow z-10">
                        <i class="fas fa-camera text-xs"></i>
                        <input type="file" id="accountAvatarInput" accept="image/*" class="hidden" onchange="onAccountAvatarChange(event)">
                    </label>
                </div>
                <div class="flex-1 min-w-0 text-center sm:text-right rounded-2xl bg-slate-50 px-4 py-3 border border-slate-100">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900" id="academyName">موزیک آکادمی</h2>
                    <p class="text-gray-500 text-sm mt-0.5" id="academyTypeLabel">آموزشگاه موسیقی</p>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2" id="academyShortIntro">—</p>
                </div>
                <div class="shrink-0 self-center">
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

    <div class="bg-white rounded-3xl p-6 shadow mt-8" id="accountInviteCard">
        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div><h3 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-user-plus text-indigo-600"></i> لینک دعوت من</h3><p class="text-sm text-gray-500 mt-2">این لینک اختصاصی را برای معرفی سرناز ارسال کنید. امتیازها و تخفیف‌های دعوت در آینده بر اساس همین کد محاسبه می‌شوند.</p></div>
            <span id="accountInviteCount" class="rounded-2xl bg-indigo-50 px-4 py-2 text-sm text-indigo-700">۰ کاربر دعوت‌شده</span>
        </div>
        <div class="mt-5 flex flex-col gap-3 sm:flex-row"><input id="accountInviteUrl" readonly dir="ltr" class="min-w-0 flex-1 rounded-2xl border bg-gray-50 px-5 py-3 text-left font-mono" value="در حال دریافت..."><button type="button" onclick="copyAccountInviteLink()" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white"><i class="fas fa-copy ml-2"></i>کپی لینک</button><button type="button" onclick="shareAccountInviteLink()" class="rounded-2xl border px-6 py-3"><i class="fas fa-share-alt ml-2"></i>اشتراک‌گذاری</button></div>
        <p class="mt-3 text-xs text-gray-400">کد اختصاصی: <b id="accountInviteCode" dir="ltr">—</b></p>
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
