<div id="home" class="section hidden">
    <!-- ========== HERO ========== -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-bl from-indigo-700 via-indigo-600 to-violet-700 text-white mb-10">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-64 h-64 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 left-20 w-80 h-80 rounded-full bg-violet-300 blur-3xl"></div>
        </div>
        <div class="relative px-8 py-16 md:px-16 md:py-24 max-w-4xl">
            <p class="text-indigo-200 text-sm font-medium mb-4 tracking-wide">برنامه و آموزشگاه موسیقی</p>
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-6">
                به ساده‌ترین شکل ممکن<br>
                با مفاهیم سخت و پیچیده دنیای موسیقی آشنا می‌شوید
            </h1>
            <p class="text-indigo-100 text-lg md:text-xl leading-relaxed mb-8 max-w-2xl">
                آموزش تئوری موسیقی ایرانی و جهانی، ردیف، دستگاه‌ها، گوشه‌ها و مقایسه موسیقی ملل مختلف
            </p>
            <div class="flex flex-wrap gap-4">
                <button onclick="showSection('articles')"
                        class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-4 rounded-2xl font-bold text-lg shadow-lg transition">
                    شروع آموزش
                </button>
                <button onclick="showSection('academies')"
                        class="border-2 border-white/40 hover:bg-white/10 px-8 py-4 rounded-2xl font-medium text-lg transition">
                    آموزشگاه‌ها
                </button>
            </div>
        </div>
    </section>

    <!-- ========== آمار سریع ========== -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
            <div class="text-3xl font-bold text-indigo-600" id="homeStatArticles">۵۰+</div>
            <div class="text-sm text-gray-500 mt-1">مقاله آموزشی</div>
        </div>
        <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
            <div class="text-3xl font-bold text-indigo-600" id="homeStatTeachers">۳۰+</div>
            <div class="text-sm text-gray-500 mt-1">استاد مجرب</div>
        </div>
        <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
            <div class="text-3xl font-bold text-indigo-600" id="homeStatStudents">۵۰۰+</div>
            <div class="text-sm text-gray-500 mt-1">هنرجو</div>
        </div>
        <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
            <div class="text-3xl font-bold text-indigo-600" id="homeStatCourses">۲۰+</div>
            <div class="text-sm text-gray-500 mt-1">دوره فعال</div>
        </div>
    </section>

    <!-- ========== نمای کلی فعالیت‌ها ========== -->
    <section class="bg-white rounded-3xl p-8 md:p-12 shadow-sm mb-12">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center">نمای کلی فعالیت‌های ما</h2>
        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed space-y-4 text-justify">
            <p>
                این مجموعه یک بستر آموزشی موسیقی است که برای آموزش تئوری موسیقی جهانی و ایرانی
                و بررسی شباهت‌ها و تفاوت‌های موسیقی در نقاط مختلف جهان ایجاد شده است.
            </p>
            <p>
                با ارائه <button onclick="showSection('articles')" class="text-indigo-600 font-medium hover:underline">مقاله‌های آموزشی</button>
                مفید درباره موسیقی ملل مختلف — به‌ویژه موسیقی ایران — و به‌روزرسانی مستمر مطالب،
                تلاش می‌کنیم کمک‌حال هنرجویان باشیم و قدمی در اعتلای این هنر برداریم.
            </p>
            <p>
                از مباحث پایه تئوری مانند <strong>ریتم</strong>، <strong>ملودی</strong> و <strong>هارمونی</strong>،
                فاصله‌ها، گام‌ها، ویژگی‌های صوت، مُدهای کلیسایی و آکوردها آغاز می‌کنیم؛
                سپس به موسیقی ایرانی، دانگ‌ها، هفت دستگاه و پنج آواز ردیف، فرم‌های گوناگون
                و معرفی موسیقیدانان بزرگ ایران و جهان می‌پردازیم.
            </p>
            <p>
                همچنین با معرفی سازها و نکات خرید ساز، و سبک‌شناسی مناطق مختلف،
                مسیر انتخاب آگاهانه ساز و شروع نوازندگی را هموار می‌کنیم.
            </p>
        </div>
    </section>

    <!-- ========== چرا ما؟ ========== -->
    <section class="mb-12">
        <div class="text-center mb-10">
            <p class="text-indigo-600 font-medium text-sm mb-2">آموزش موسیقی حرفه‌ای</p>
            <h2 class="text-2xl md:text-3xl font-bold">چرا این آموزشگاه؟</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl p-8 shadow-sm text-center card-hover border border-gray-50">
                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">اساتید مجرب</h3>
                <p class="text-gray-500 leading-relaxed">تدریس توسط اساتید با سابقه و مدرک معتبر در موسیقی ایرانی و جهانی</p>
            </div>
            <div class="bg-white rounded-3xl p-8 shadow-sm text-center card-hover border border-gray-50">
                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">گواهینامه رسمی</h3>
                <p class="text-gray-500 leading-relaxed">دریافت مدرک معتبر پس از پایان دوره و ارزیابی نهایی</p>
            </div>
            <div class="bg-white rounded-3xl p-8 shadow-sm text-center card-hover border border-gray-50">
                <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                    <i class="fas fa-laptop-house"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">آموزش آنلاین و حضوری</h3>
                <p class="text-gray-500 leading-relaxed">امکان شرکت در کلاس‌های حضوری شعب و جلسات آنلاین</p>
            </div>
        </div>
    </section>

    <!-- ========== مسیر یادگیری ========== -->
    <section class="mb-12">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-10">مسیر یادگیری</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl p-6 shadow-sm relative">
                <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۱</span>
                <h3 class="font-bold mt-2 mb-2">تئوری پایه</h3>
                <p class="text-sm text-gray-500">ریتم، ملودی، هارمونی، فاصله و گام</p>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-sm relative">
                <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۲</span>
                <h3 class="font-bold mt-2 mb-2">موسیقی ایرانی</h3>
                <p class="text-sm text-gray-500">دانگ‌ها، دستگاه‌ها، آوازها و ردیف</p>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-sm relative">
                <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۳</span>
                <h3 class="font-bold mt-2 mb-2">فرم و سبک</h3>
                <p class="text-sm text-gray-500">فرم‌های ایرانی و جهانی، سبک‌شناسی</p>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-sm relative">
                <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۴</span>
                <h3 class="font-bold mt-2 mb-2">ساز و نوازندگی</h3>
                <p class="text-sm text-gray-500">انتخاب ساز، تمرین و ورود حرفه‌ای</p>
            </div>
        </div>
    </section>

    <!-- ========== آخرین مقالات ========== -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">آخرین مقاله‌های آموزشی</h2>
            <button onclick="showSection('articles')" class="text-indigo-600 text-sm font-medium hover:underline">
                مشاهده همه ←
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="homeLatestArticles">
            <!-- توسط JS -->
        </div>
    </section>

    <!-- ========== CTA پایین ========== -->
    <section class="rounded-3xl bg-gradient-to-l from-violet-600 to-indigo-600 text-white p-10 md:p-14 text-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">آماده شروع یادگیری هستید؟</h2>
        <p class="text-indigo-100 mb-8 max-w-xl mx-auto">در کلاس‌ها ثبت‌نام کنید یا آموزشگاه خود را به جمع ما اضافه کنید.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <button onclick="showSection('academy-enroll')"
                    class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-3.5 rounded-2xl font-bold transition">
                ثبت‌نام در کلاس
            </button>
            <button onclick="showSection('academy-requests')"
                    class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">
                ثبت آموزشگاه
            </button>
            <button onclick="showSection('contact-us')"
                    class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">
                تماس با ما
            </button>
        </div>
    </section>
</div>