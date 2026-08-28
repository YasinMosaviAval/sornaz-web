<?

$home_array = getFilteredList(setIndexforDataArray($home, 'variable_name'), 'home_main_title');
$header_array = setIndexforDataArray($header, 'variable_name');
$footer_array = setIndexforDataArray($footer, 'variable_name');
$isEnglish = locale() === 'en';
$formatNumber = static function (int $number) use ($isEnglish): string {
    $value = (string) $number;
    if ($isEnglish) return $value;

    return strtr($value, [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ]);
};
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
            <div class="max-w-4xl text-center <?= $isEnglish ? 'lg:text-left' : 'lg:text-right' ?> pointer-events-auto">
                    <span class="inline-flex px-5 py-2.5 rounded-full bg-white/95 text-indigo-600 text-sm shadow-sm mb-7">بزرگ‌ترین مرجع آموزش موسیقی ایران</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.35] text-white drop-shadow-lg mb-5">بهترین آموزشگاه موسیقی را<br class="hidden sm:block"> پیدا کنید</h1>
                    <p class="text-white/90 md:text-lg leading-8 mb-8 drop-shadow">آموزشگاه‌ها، اساتید، کلاس‌ها و دوره‌های موسیقی سراسر ایران را جستجو و مقایسه کنید.</p>

                    <form action="/academy/academies" method="GET" class="grid grid-cols-1 sm:grid-cols-[1fr_100px_1fr_108px] gap-3 bg-white/95 backdrop-blur rounded-3xl p-4 shadow-2xl" dir="<?= e(direction()) ?>">
                        <input name="q" type="search" placeholder="نام آموزشگاه" class="bg-gray-50 rounded-2xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400 <?= $isEnglish ? 'text-left' : 'text-right' ?>">
                        <select name="instrument" aria-label="ساز" class="bg-gray-50 rounded-2xl px-3 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400 <?= $isEnglish ? 'text-left' : 'text-right' ?>">
                            <option value=""><?= htmlspecialchars($homeSearchSelectLabels['instrument'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php foreach (($academySearchOptions['instruments'] ?? []) as $instrument): ?>
                                <option value="<?= (int)$instrument['id'] ?>"><?= htmlspecialchars($instrument['title'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="city" aria-label="شهر" class="bg-gray-50 rounded-2xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-indigo-400 <?= $isEnglish ? 'text-left' : 'text-right' ?>">
                            <option value=""><?= htmlspecialchars($homeSearchSelectLabels['city'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php foreach (($academySearchOptions['cities'] ?? []) as $city): ?>
                                <option value="<?= (int)$city['id'] ?>"><?= htmlspecialchars($city['title'], ENT_QUOTES, 'UTF-8') ?></option>
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
                <div class="text-3xl font-bold text-indigo-600" id="homeStatArticles"><?= $formatNumber((int)($homeStatistics['articles'] ?? 0)) ?></div>
                <div class="text-sm text-gray-500 mt-1">مقاله آموزشی</div>
            </div>
            <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-indigo-600" id="homeStatTeachers"><?= $formatNumber((int)($homeStatistics['teachers'] ?? 0)) ?></div>
                <div class="text-sm text-gray-500 mt-1">استاد مجرب</div>
            </div>
            <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-indigo-600" id="homeStatStudents"><?= $formatNumber((int)($homeStatistics['students'] ?? 0)) ?></div>
                <div class="text-sm text-gray-500 mt-1">هنرجو</div>
            </div>
            <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-indigo-600" id="homeStatCourses"><?= $formatNumber((int)($homeStatistics['courses'] ?? 0)) ?></div>
                <div class="text-sm text-gray-500 mt-1">دوره فعال</div>
            </div>
        </section>

        <!-- ========== نمای کلی فعالیت‌ها ========== -->
        <section class="bg-white rounded-3xl p-8 md:p-12 shadow-sm mb-12">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center">نمای کلی فعالیت‌های ما</h2>
            <div class="home-activity-overview prose prose-lg max-w-none text-gray-600 leading-relaxed space-y-4 text-justify" data-dynamic-content>
                <?php if ($activityOverviewHtml !== ''): ?>
                    <?= $activityOverviewHtml ?>
                <?php elseif ($isEnglish): ?>
                <p style="text-align: justify;">The Sornaz Music Program is an online music school dedicated to teaching both Western and Iranian music theory, while exploring the similarities and differences between musical traditions from around the world.</p>
                <p style="text-align: justify;">With this in mind, we aim to support music students by providing useful <a href="https://sornaz.com/%d9%85%d9%82%d8%a7%d9%84%d9%87-%d9%87%d8%a7/">educational articles</a> about the music of various nations — especially the rich musical heritage of our homeland, Iran. We are committed to continuously updating the articles on the site, correcting any possible errors, and presenting in-depth analyses by prominent music theorists on different aspects of music as both a science and an art. In doing so, we hope to take a small but meaningful step toward the advancement and elevation of this beautiful art form.</p>
                <p style="text-align: justify;">We will begin by publishing articles on the fundamental principles of <a href="https://sornaz.com/%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">music theory</a>, such as an introduction to the three main elements of music — <a href="https://sornaz.com/%d8%b1%db%8c%d8%aa%d9%85-%d8%af%d8%b1-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a8%d9%87-%da%86%d9%87-%d9%85%d8%b9%d9%86%d8%a7%d8%b3%d8%aa%d8%9f/">rhythm</a>, <a href="https://sornaz.com/%d9%85%d9%84%d9%88%d8%af%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">melody</a>, and <a href="https://sornaz.com/%d9%87%d8%a7%d8%b1%d9%85%d9%88%d9%86%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">harmony</a> — along with concepts like <a href="https://sornaz.com/%d9%81%d8%a7%d8%b5%d9%84%d9%87-%d9%85%d9%88%d8%b3%db%8c%d9%82%d8%a7%db%8c%db%8c-%d8%a8%d9%87-%da%86%d9%87-%d9%85%d8%b9%d9%86%d8%a7%d8%b3%d8%aa%d8%9f/">musical intervals</a>, <a href="https://sornaz.com/%d9%85%d9%81%d9%87%d9%88%d9%85-%da%af%d8%a7%d9%85-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d9%88-%d8%aa%d9%88%d9%86%d8%a7%d9%84%db%8c%d8%aa%d9%87-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">scales in Western music</a>, the <a href="https://sornaz.com/4-%d9%88%db%8c%da%98%da%af%db%8c-%d8%a7%d8%b5%d9%84%db%8c-%d8%b5%d8%af%d8%a7%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%d8%a7%db%8c%db%8c-%d9%88-%d8%b5%d9%88%d8%aa/">characteristics of musical sound</a>, <a href="https://sornaz.com/%d9%85%d8%af%d9%87%d8%a7%db%8c-%da%a9%d9%84%db%8c%d8%b3%d8%a7%db%8c%db%8c-%d8%af%d8%b1-%d9%82%d8%b1%d9%88%d9%86-%d9%88%d8%b3%d8%b7%db%8c/">church modes</a>, <a href="https://sornaz.com/%d8%a2%da%a9%d9%88%d8%b1%d8%af-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">chords</a>, and more. Our aim is to familiarize readers with the most basic and essential topics in the science and art of <a href="https://sornaz.com/%d9%85%d9%88%d8%b2%db%8c%da%a9-%db%8c%d8%a7-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/">music</a>. Through continuous updates and more detailed explanations, we will help you gain a deeper and clearer understanding of these concepts.</p>
                <p style="text-align: justify;">After building this foundation, we will examine <a href="https://sornaz.com/%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a7%db%8c%d8%b1%d8%a7%d9%86%db%8c%d8%8c-%d8%aa%d9%82%d8%b3%db%8c%d9%85%d8%a7%d8%aa-%d9%88-%d9%81%d8%b1%d9%85-%d9%87%d8%a7%db%8c-%d9%85%d8%ae%d8%aa%d9%84%d9%81/">Iranian music</a>, the <a href="https://sornaz.com/%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a7%db%8c%d8%b1%d8%a7%d9%86%db%8c%d8%8c-%d8%a8%d8%b1%d8%b1%d8%b3%db%8c-%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d8%af%d8%a7%d9%86%da%af/">theory of dangs in Iranian music</a>, the theories of great Iranian music scholars, the structure of the seven dastgahs and five avaz in the Iranian radif, as well as the different opinions and debates surrounding these topics.</p>
                <p style="text-align: justify;">Following our exploration of Iranian music, we will introduce various <a href="https://sornaz.com/category/%d9%81%d8%b1%d9%85-%d9%87%d8%a7%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c/">musical forms</a> from both around the world and Iran, and familiarize you with great musicians from Iran and other countries. In this journey, the Sornaz Music Program will do its utmost to create a rich and welcoming space for deeper acquaintance and understanding of the science and art of music.</p>
                <p style="text-align: justify;">On the other hand, we will help you with <a href="https://sornaz.com/category/%d8%ae%d8%b1%db%8c%d8%af-%d8%b3%d8%a7%d8%b2/">selecting and purchasing instruments</a> by introducing various musical instruments from Iran and around the world. We will provide practical tips and important considerations for buying different instruments — whether you are just beginning to play or aiming to enter the professional world of music.</p>
                <p style="text-align: justify;">To help you make a better choice of your favorite musical style — which ultimately leads to a more informed decision when choosing an instrument — we will also explore the musical styles and traditions of different regions of the world and their associated instruments.</p>
                <?php else: ?>
                <p>برنامه موسیقی سُرناز یک آموزشگاه آنلاین موسیقی است که جهت آموزش تئوری موسیقی جهانی و ایرانی و بررسی شباهت‌ها و تفاوت‌های موسیقی در نقاط مختلف جهان ایجاد شده است.</p>
                <p>از این رو در نظر داریم با ارائه <a href="https://sornaz.com/%d9%85%d9%82%d8%a7%d9%84%d9%87-%d9%87%d8%a7/" class="text-indigo-600 font-medium hover:underline">مقاله‌های آموزشی</a> مفید در رابطه با موسیقی ملل مختلف و به خصوص موسیقی سرزمین‌مان ایران و به‌روزرسانی‌های مستمر مقالات سایت و رفع خطاهای احتمالی و ارائه بررسی‌های مختلف توسط تئوریسین‌های بزرگ در رابطه با بخش‌های گوناگون علم و هنر موسیقی کمک‌حال هنرجویان موسیقی باشیم و قدم کوچکی در راه اعتلای این هنر برداریم.</p>
                <p>ابتدا با ارائه مقالاتی در رابطه با مباحث اولیه <a href="https://sornaz.com/%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">علم تئوری موسیقی</a> نظیر آشنایی با سه عنصر اصلی موسیقی یعنی <a href="https://sornaz.com/%d8%b1%db%8c%d8%aa%d9%85-%d8%af%d8%b1-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a8%d9%87-%da%86%d9%87-%d9%85%d8%b9%d9%86%d8%a7%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">ریتم</a>، <a href="https://sornaz.com/%d9%85%d9%84%d9%88%d8%af%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">ملودی</a> و <a href="https://sornaz.com/%d9%87%d8%a7%d8%b1%d9%85%d9%88%d9%86%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">هارمونی</a>، ارائه مفاهیم <a href="https://sornaz.com/%d9%81%d8%a7%d8%b5%d9%84%d9%87-%d9%85%d9%88%d8%b3%db%8c%d9%82%d8%a7%db%8c%db%8c-%d8%a8%d9%87-%da%86%d9%87-%d9%85%d8%b9%d9%86%d8%a7%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">فاصله‌ها</a> و <a href="https://sornaz.com/%d9%85%d9%81%d9%87%d9%88%d9%85-%da%af%d8%a7%d9%85-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d9%88-%d8%aa%d9%88%d9%86%d8%a7%d9%84%db%8c%d8%aa%d9%87-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">گام‌های موسیقی جهانی</a>، <a href="https://sornaz.com/4-%d9%88%db%8c%da%98%da%af%db%8c-%d8%a7%d8%b5%d9%84%db%8c-%d8%b5%d8%af%d8%a7%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%d8%a7%db%8c%db%8c-%d9%88-%d8%b5%d9%88%d8%aa/" class="text-indigo-600 font-medium hover:underline">ویژگی‌های صوت موسیقایی</a>، <a href="https://sornaz.com/%d9%85%d8%af%d9%87%d8%a7%db%8c-%da%a9%d9%84%db%8c%d8%b3%d8%a7%db%8c%db%8c-%d8%af%d8%b1-%d9%82%d8%b1%d9%88%d9%86-%d9%88%d8%b3%d8%b7%db%8c/" class="text-indigo-600 font-medium hover:underline">مُدهای کلیسایی</a>، <a href="https://sornaz.com/%d8%a2%da%a9%d9%88%d8%b1%d8%af-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">آکوردها</a> و … سعی می‌کنیم تا حدودی با پایه‌ای‌ترین و مهم‌ترین موارد در علم و هنر <a href="https://sornaz.com/%d9%85%d9%88%d8%b2%db%8c%da%a9-%db%8c%d8%a7-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%da%86%db%8c%d8%b3%d8%aa%d8%9f/" class="text-indigo-600 font-medium hover:underline">موسیقی</a> آشنا شویم و پس از آن با به‌روزرسانی‌های مستمر این مطالب و ارائه جزئیات بیشتر شناخت بهتر و بیشتری از آن‌ها پیدا می‌کنیم.</p>
                <p>پس از آن به بررسی <a href="https://sornaz.com/%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a7%db%8c%d8%b1%d8%a7%d9%86%db%8c%d8%8c-%d8%aa%d9%82%d8%b3%db%8c%d9%85%d8%a7%d8%aa-%d9%88-%d9%81%d8%b1%d9%85-%d9%87%d8%a7%db%8c-%d9%85%d8%ae%d8%aa%d9%84%d9%81/" class="text-indigo-600 font-medium hover:underline">موسیقی ایرانی</a>، <a href="https://sornaz.com/%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c-%d8%a7%db%8c%d8%b1%d8%a7%d9%86%db%8c%d8%8c-%d8%a8%d8%b1%d8%b1%d8%b3%db%8c-%d8%aa%d8%a6%d9%88%d8%b1%db%8c-%d8%af%d8%a7%d9%86%da%af/" class="text-indigo-600 font-medium hover:underline">تئوری دانگ‌ها در موسیقی ایرانی</a>، نظریه‌های بزرگان موسیقی ایران، ساختار هفت دستگاه و پنج آواز در ردیف موسیقی ایران و اختلاف‌نظرها در این موارد می‌پردازیم.</p>
                <p>پس از بررسی موسیقی ایران به معرفی <a href="https://sornaz.com/category/%d9%81%d8%b1%d9%85-%d9%87%d8%a7%db%8c-%d9%85%d9%88%d8%b3%db%8c%d9%82%db%8c/" class="text-indigo-600 font-medium hover:underline">فرم‌های گوناگون موسیقی</a> جهانی و ایرانی، آشنایی با موسیقیدان‌های بزرگ ایران و جهان خواهیم پرداخت و در این راه در آموزشگاه موسیقی سُرناز تمام تلاش خود را به کار می‌گیریم تا فضایی را برای آشنایی و شناخت هرچه بیشتر علم و هنر موسیقی ایجاد کنیم.</p>
                <p>از طرفی دیگر سعی داریم با معرفی سازها و آلات مختلف موسیقی ایران و جهان شما را در <a href="https://sornaz.com/category/%d8%ae%d8%b1%db%8c%d8%af-%d8%b3%d8%a7%d8%b2/" class="text-indigo-600 font-medium hover:underline">تهیه و خرید ساز</a> یاری کنیم و نکات و موارد لازم را در جهت خرید سازهای مختلف به منظور شروع نوازندگی و ورود به دنیای حرفه‌ای موسیقی ارائه می‌کنیم.</p>
                <p>از این رو جهت انتخاب بهتر سبک موسیقی مورد علاقه شما که به انتخاب آگاهانه ساز می‌انجامد گریزی به سبک‌شناسی موسیقی مناطق و سازهای مختلف جهان می‌زنیم.</p>
                <?php endif; ?>
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
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-10"><?= htmlspecialchars($homeLearningPath['heading'], ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="/analytics/articles?category=5" class="block bg-white rounded-3xl p-6 shadow-sm relative card-hover">
                    <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۱</span>
                    <h3 class="font-bold mt-2 mb-2"><?= htmlspecialchars($homeLearningPath['basic_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($homeLearningPath['basic_description'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
                <a href="/analytics/articles?category=1" class="block bg-white rounded-3xl p-6 shadow-sm relative card-hover">
                    <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۲</span>
                    <h3 class="font-bold mt-2 mb-2"><?= htmlspecialchars($homeLearningPath['iranian_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($homeLearningPath['iranian_description'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
                <a href="/analytics/articles?category=6" class="block bg-white rounded-3xl p-6 shadow-sm relative card-hover">
                    <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۳</span>
                    <h3 class="font-bold mt-2 mb-2"><?= htmlspecialchars($homeLearningPath['forms_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($homeLearningPath['forms_description'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
                <a href="/analytics/articles?category=4" class="block bg-white rounded-3xl p-6 shadow-sm relative card-hover">
                    <span class="absolute -top-3 -right-1 w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">۴</span>
                    <h3 class="font-bold mt-2 mb-2"><?= htmlspecialchars($homeLearningPath['instruments_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($homeLearningPath['instruments_description'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            </div>
        </section>

        <!-- ========== آخرین مقالات ========== -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold"><?= $isEnglish ? 'Latest Educational Articles' : 'آخرین مقاله‌های آموزشی' ?></h2>
                <a href="/analytics/articles" class="text-indigo-600 text-sm font-medium hover:underline"><?= $isEnglish ? 'View All →' : 'مشاهده همه ←' ?></a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="homeLatestArticles" data-dynamic-content>
                <?php foreach (($latestArticles ?? []) as $article): ?>
                    <article class="bg-white rounded-3xl p-6 shadow-sm card-hover border border-gray-50 flex flex-col">
                        <?php if (!empty($article['thumbnail'])): ?>
                            <a href="/analytics/article-details?id=<?= (int)$article['id'] ?>" class="block mb-4">
                                <img src="<?= htmlspecialchars($article['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full aspect-video object-cover rounded-2xl" loading="lazy">
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($article['categories'])): ?>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <?php foreach (array_slice($article['categories'], 0, 2) as $category): ?>
                                    <span class="px-2.5 py-1 rounded-lg text-xs bg-indigo-50 text-indigo-700"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="/analytics/article-details?id=<?= (int)$article['id'] ?>" class="hover:text-indigo-600">
                            <h3 class="font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        </a>
                        <?php $articleSummary = $article['summary'] ?: $article['description']; ?>
                        <?php if ($articleSummary): ?><p class="text-sm text-gray-500 line-clamp-2 mb-4"><?= htmlspecialchars($articleSummary, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                        <div class="text-xs text-gray-400 mt-auto" dir="ltr"><?= htmlspecialchars((string)($article['published_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ========== CTA پایین ========== -->
        <section class="rounded-3xl bg-gradient-to-l from-violet-600 to-indigo-600 text-white p-10 md:p-14 text-center mb-6">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">آماده شروع یادگیری هستید؟</h2>
            <p class="text-indigo-100 mb-8 max-w-xl mx-auto">در کلاس‌ها ثبت‌نام کنید یا آموزشگاه خود را به جمع ما اضافه کنید.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <?php if(auth()->check()): ?><a href="/academy/academies"><button class="bg-white text-indigo-700 hover:bg-indigo-50 px-8 py-3.5 rounded-2xl font-bold transition"><?= locale()==='en'?'Register for a class':'ثبت‌نام در کلاس' ?></button></a><?php endif; ?>
                <a href="/academy/send-academy-request"><button class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">ثبت آموزشگاه</button></a>
                <a href="/page/contact-us"><button class="border-2 border-white/50 hover:bg-white/10 px-8 py-3.5 rounded-2xl font-medium transition">تماس با ما</button></a>
            </div>
        </section>
    </div>
</div>
