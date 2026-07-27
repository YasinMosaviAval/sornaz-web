<div id="page-academy-profile" class="">
    <div class="max-w-5xl mx-auto px-4 py-8 md:py-12">

        <!-- بازگشت -->
        <a href="/analytics/academies" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
            <div><i class="fas fa-arrow-right"></i> &nbsp; بازگشت به لیست آموزشگاه‌ها</div>
        </a>

        <!-- هدر پروفایل -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <!-- کاور -->
            <div id="apCover" class="h-36 md:h-48 bg-gradient-to-l from-indigo-600 via-violet-600 to-indigo-800 relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 50%, white 0%, transparent 50%);"></div>
            </div>

            <div class="px-6 md:px-10 pb-8">
                <!-- آواتار + نام -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 md:-mt-14 mb-6">
                    <div id="apAvatar" class="w-24 h-24 md:w-28 md:h-28 rounded-3xl bg-white border-4 border-white shadow-lg flex items-center justify-center text-3xl font-bold text-indigo-600 shrink-0">
                        آ
                    </div>
                    <div class="flex-1 pb-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 id="apName" class="text-2xl md:text-3xl font-bold"></h1>
                            <span id="apTypeBadge" class="px-3 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700 hidden"></span>
                        </div>
                        <p id="apSlogan" class="text-indigo-600 mt-1 font-medium hidden"></p>
                        <p id="apLocation" class="text-sm text-gray-500 mt-1"></p>
                    </div>
                    <div class="flex flex-wrap gap-2 shrink-0">
                        <a href="/analytics/academy-enroll" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-medium text-center block">ثبت‌نام در کلاس</a>
                        <a href="#" class="border border-gray-300 hover:bg-gray-50 px-5 py-3 rounded-2xl text-sm">تماس</a>
                    </div>
                </div>

                <!-- آمار -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="apStats">
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">امتیاز</div>
                        <div class="text-xl font-bold mt-1" id="apRating">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">کلاس‌ها</div>
                        <div class="text-xl font-bold mt-1" id="apClasses">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">هنرجوها</div>
                        <div class="text-xl font-bold mt-1" id="apStudents">—</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl py-4 text-center">
                        <div class="text-xs text-gray-400">اساتید</div>
                        <div class="text-xl font-bold mt-1" id="apTeachers">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- ستون اصلی -->
            <div class="lg:col-span-2 space-y-6">
                <!-- درباره -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">درباره آموزشگاه</h2>
                    <p id="apSummary" class="text-indigo-600 font-medium mb-3 hidden"></p>
                    <div id="apBio" class="text-gray-600 leading-relaxed text-justify whitespace-pre-wrap"></div>
                </section>

                <!-- دوره‌ها / کلاس‌ها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">دوره‌ها و کلاس‌ها</h2>
                    </div>
                    <div id="apCourses" class="space-y-3">
                        <p class="text-sm text-gray-400 text-center py-6">دوره‌ای ثبت نشده است</p>
                    </div>
                </section>

                <!-- اساتید -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-4">اساتید</h2>
                    <div id="apTeachersList" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <p class="text-sm text-gray-400 col-span-full text-center py-6">استادی نمایش داده نشده</p>
                    </div>
                </section>
            </div>

            <!-- سایدبار -->
            <div class="space-y-6">
                <!-- تماس و لینک‌ها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">راه‌های ارتباطی</h2>
                    <div id="apContacts" class="space-y-3 text-sm"></div>
                </section>

                <!-- آدرس‌ها -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-4">آدرس‌ها</h2>
                    <div id="apAddresses" class="space-y-3 text-sm"></div>
                </section>

                <!-- وضعیت -->
                <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold mb-3">وضعیت</h2>
                    <div id="apStatus" class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-700">فعال</div>
                </section>
            </div>
        </div>
    </div>
</div>