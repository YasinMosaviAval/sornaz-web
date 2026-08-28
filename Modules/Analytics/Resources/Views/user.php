<div id="page-user-profile" class="site-page">
    <div class="max-w-7xl mx-auto px-4 py-8 md:py-12">

        <a href="/users" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
            <div><i class="fas fa-arrow-right"></i> &nbsp; بازگشت به کاربران</div>
        </a>

        <!-- هدر پروفایل -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div id="upCover" class="relative aspect-[3/1] min-h-[150px] w-full overflow-hidden bg-gradient-to-l from-violet-600 via-indigo-600 to-indigo-800 bg-cover bg-center">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 80% 40%, white 0%, transparent 45%);"></div>
            </div>

            <div class="border-t border-gray-100 bg-white px-6 py-6 md:px-10">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center mb-6">
                    <div id="upAvatar" class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center text-3xl font-bold text-indigo-600 shrink-0">
                        ?
                    </div>
                    <div class="flex-1 min-w-0 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-center sm:text-start">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 id="upName" class="text-2xl md:text-3xl font-bold"></h1>
                            <span id="upRoleBadge" class="px-3 py-1 rounded-full text-xs font-medium"></span>
                        </div>
                        <p id="upHeadline" class="text-gray-500 mt-1 text-sm"></p>
                        <p id="upLocation" class="text-sm text-gray-400 mt-1"></p>
                    </div>
                    <div class="flex flex-wrap gap-2 shrink-0">
                        <a href="/analytics/contact-us" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-medium text-center block">ارسال پیام</a>
                    </div>
                </div>

                <!-- آمار -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">امتیاز</div>
                        <div class="text-xl font-bold mt-1" id="upRating">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">سازها</div>
                        <div class="text-xl font-bold mt-1" id="upInstrumentsCount">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">تجربه (سال)</div>
                        <div class="text-xl font-bold mt-1" id="upYears">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">نشان‌ها</div>
                        <div class="text-xl font-bold mt-1" id="upBadgesCount">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- اصلی -->
            <div class="lg:col-span-2 space-y-6">
                <!-- درباره -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">درباره</h2>
                    <p id="upBio" class="text-gray-600 leading-relaxed whitespace-pre-wrap"></p>
                </section>

                <section id="upIntroSection" class="hidden bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">ویدیوی معرفی</h2>
                    <video id="upIntroVideo" class="w-full aspect-video rounded-2xl bg-black" controls preload="metadata"></video>
                </section>

                <section id="upGallerySection" class="hidden bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">گالری تصاویر</h2>
                    <div id="upGallery" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
                </section>

                <!-- سازها / ابزارها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">سازها و تخصص‌ها</h2>
                    <div id="upInstruments" class="flex flex-wrap gap-2"></div>
                </section>

                <!-- درس‌ها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">درس‌ها</h2>
                    <div id="upLessons" class="space-y-2"></div>
                </section>

                <!-- تحصیلات / تجربه‌ها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">سوابق و تجربه‌ها</h2>
                    <div id="upExperiences" class="space-y-4"></div>
                </section>

                <!-- تألیف‌ها / جوایز (خلاصه) -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">افتخارات و آثار</h2>
                    <div id="upAchievements" class="space-y-3"></div>
                </section>
            </div>

            <!-- سایدبار -->
            <div class="space-y-6">
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">اطلاعات</h2>
                    <div id="upInfo" class="space-y-3 text-sm"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">برنامه هفتگی حضور</h2>
                    <div id="upAvailability" class="space-y-4"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">تعطیلات، مرخصی‌ها و تغییرات برنامه</h2>
                    <div id="upAvailabilityExceptions" class="space-y-3"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">نشانی‌ها</h2>
                    <div id="upAddresses" class="space-y-3 text-sm"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">راه‌های ارتباطی</h2>
                    <div id="upContacts" class="space-y-3 text-sm"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">نشان‌ها</h2>
                    <div id="upBadges" class="flex flex-wrap gap-2"></div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">آموزشگاه‌ها</h2>
                    <div id="upAcademies" class="space-y-2"></div>
                </section>
            </div>
        </div>
    </div>
</div>
<?php if(isset($selectedUserId)): ?><script>window.siteUsersData=<?= json_encode($users??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;document.addEventListener('DOMContentLoaded',()=>window.openSiteUserProfile?.(<?= (int)$selectedUserId ?>));</script><?php endif; ?>

<div id="userGalleryDialog" class="hidden fixed inset-0 z-[100] bg-black/90 p-4 items-center justify-center" role="dialog" aria-modal="true">
    <button type="button" onclick="closeUserGalleryDialog()" class="absolute top-5 left-5 w-11 h-11 rounded-full bg-white/15 text-white text-xl" aria-label="بستن"><i class="fas fa-times"></i></button>
    <button type="button" onclick="moveUserGallery(-1)" class="absolute right-4 md:right-8 w-12 h-12 rounded-full bg-white/15 text-white" aria-label="قبلی"><i class="fas fa-chevron-right"></i></button>
    <img id="userGalleryDialogImage" class="max-w-[90vw] max-h-[85vh] object-contain rounded-2xl" alt="تصویر گالری کاربر">
    <button type="button" onclick="moveUserGallery(1)" class="absolute left-4 md:left-8 w-12 h-12 rounded-full bg-white/15 text-white" aria-label="بعدی"><i class="fas fa-chevron-left"></i></button>
    <span id="userGalleryDialogCounter" class="absolute bottom-5 text-white/80 text-sm"></span>
</div>
