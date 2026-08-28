<?

$home_array = getFilteredList(setIndexforDataArray($home, 'variable_name'), 'home_main_title');
$header_array = setIndexforDataArray($header, 'variable_name');
$footer_array = setIndexforDataArray($footer, 'variable_name');
// dump($home_array);
// dump($header_array);
// dump($footer_array);

?>

<div id="home" class="">
    <!-- ========== HERO ========== -->
    <section class="home-hero relative min-h-[620px] md:min-h-[680px] overflow-hidden mb-10 flex items-center" aria-label="معرفی برنامه آموزشی سرناز">
        <div id="homeHeroSlider" class="home-hero-slider absolute inset-0" aria-roledescription="carousel">
            <?php foreach ([1, 2, 3] as $index): ?>
                <div class="home-hero-slide absolute inset-0 <?= $index === 1 ? 'is-active' : '' ?>" data-slide="<?= $index - 1 ?>">
                    <img src="/assets/images/banner/slider_<?= $index ?>-copyright.webp" alt="<?= ['کودک در حال گوش دادن به موسیقی', 'نوازنده ساز بادی', 'ساز سنتور ایرانی'][$index - 1] ?>" class="w-full h-full object-cover" <?= $index === 1 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                </div>
            <?php endforeach; ?>
            <div class="absolute inset-0 bg-gradient-to-l from-black/75 via-black/45 to-black/20 pointer-events-none"></div>
            <button type="button" class="hero-slider-prev absolute z-20 right-4 md:right-8 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/85 hover:bg-white text-gray-800 shadow transition" aria-label="اسلاید قبلی"><i class="fas fa-chevron-right"></i></button>
            <button type="button" class="hero-slider-next absolute z-20 left-4 md:left-8 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/85 hover:bg-white text-gray-800 shadow transition" aria-label="اسلاید بعدی"><i class="fas fa-chevron-left"></i></button>
            <div class="hero-slider-dots absolute z-20 bottom-6 inset-x-0 flex justify-center gap-2" aria-label="انتخاب اسلاید"></div>
        </div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-6 md:px-16 py-16 pointer-events-none">
            <div class="max-w-4xl text-center lg:text-right pointer-events-auto">
                    <span class="inline-flex px-5 py-2.5 rounded-full bg-white/95 text-indigo-600 text-sm shadow-sm mb-7">بزرگ‌ترین مرجع آموزش موسیقی ایران</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.35] text-white drop-shadow-lg mb-5">بهترین آموزشگاه موسیقی را<br class="hidden sm:block"> پیدا کنید</h1>
                    <p class="text-white/90 md:text-lg leading-8 mb-8 drop-shadow">آموزشگاه‌ها، اساتید، کلاس‌ها و دوره‌های موسیقی سراسر ایران را جستجو و مقایسه کنید.</p>

                    <form action="/academy/academies" method="GET" class="grid grid-cols-1 sm:grid-cols-[1fr_100px_1fr_108px] gap-3 bg-white/95 backdrop-blur rounded-3xl p-4 shadow-2xl">
                        <input name="q" type="search" placeholder="نام آموزشگاه" class="bg-gray-50 rounded-2xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400">
                        <select name="instrument" aria-label="ساز" class="bg-gray-50 rounded-2xl px-3 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">ساز</option>
                            <?php foreach (($academySearchOptions['instruments'] ?? []) as $instrument): ?>
                                <option value="<?= (int)$instrument['id'] ?>"><?= htmlspecialchars($instrument['title'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="city" aria-label="شهر" class="bg-gray-50 rounded-2xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">شهر</option>
                            <?php foreach (($academySearchOptions['cities'] ?? []) as $city): ?>
                                <option value="<?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-5 py-3.5 transition">جستجو</button>
                    </form>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4">
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
                    با ارائه <a href="/analytics/articles" class="text-indigo-600 font-medium hover:underline">مقاله‌های آموزشی</a>
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
                <a href="/analytics/articles"><button class="text-indigo-600 text-sm font-medium hover:underline">مشاهده همه ←</button></a>
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
                <a href="/academy/academy-enroll"><button class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-3.5 rounded-2xl font-bold transition">ثبت‌نام در کلاس</button></a>
                <a href="/academy/send-academy-request"><button class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">ثبت آموزشگاه</button></a>
                <a href="/page/contact-us"><button class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">تماس با ما</button></a>
            </div>
        </section>
    </div>
</div>
