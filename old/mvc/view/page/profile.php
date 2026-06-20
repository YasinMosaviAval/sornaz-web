<style>
    .profile-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 7rem 2rem 5rem; text-align: center; }
    .avatar { width: 200px; height: 200px; border-radius: 50%; border: 1px solid var(--primary); object-fit: cover; box-shadow: 0 15px 40px rgba(0,0,0,0.3); margin-bottom: 2rem; }
    .name { font-size: 3.8rem; font-weight: 900; margin-bottom: 0.8rem; }
    .title { font-size: 2rem; opacity: 0.95; margin-bottom: 2rem; }
    .main-content { max-width: 1200px; margin: -4rem auto 0; padding: 0 2rem; }
    .bio { font-size: 1.75rem; color: #444; line-height: 2; text-align: justify; }
    .education-list { list-style: none; }
    .education-item { padding: 1.8rem 0; border-bottom: 1px solid #eee; }
    .education-item:last-child { border-bottom: none; }
    .degree { font-size: 1.8rem; font-weight: 700; color: var(--primary); }
    .books-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem; margin-top: 2rem; }
    .book-card { background: var(--light); border-radius: 16px; padding: 2rem; text-align: center; }
    .book-title { font-size: 1.7rem; font-weight: 700; margin: 1rem 0 0.5rem; }
    .awards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem; margin-top: 2rem; }
    .award-item { background: var(--light); border-radius: 16px; padding: 2rem; text-align: center; }
    .award-icon { font-size: 3.5rem; margin-bottom: 1rem; }
    .social-links { display: flex; justify-content: center; gap: 2rem; margin-top: 2rem; }
    .social-links a { font-size: 2.8rem; color: var(--primary); transition: all 0.3s; }
    .social-links a:hover { color: var(--primary-dark); transform: scale(1.2); }
    .contact-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 2rem; }
    .contact-item { display: flex; align-items: center; gap: 1.5rem; font-size: 1.7rem; }
    .contact-icon { font-size: 2.8rem; color: var(--primary); width: 50px; }
    .action-buttons { text-align: center; margin: 4rem 0; }
    .btn { display: inline-block; padding: 1.2rem 3rem; margin: 0 1rem; font-size: 1.7rem; border-radius: 10px; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-3px); }
    .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); }
    .btn-outline:hover { background: var(--primary); color: white; }
    .skills { display: flex; flex-wrap: wrap; gap: 1.2rem; margin-top: 2rem; }
    .skill { background: var(--light); color: var(--primary); padding: 0.8rem 2rem; border-radius: 30px; font-size: 1.55rem; font-weight: 500; }
    @media (max-width: 768px) {
        .avatar { width: 160px; height: 160px; }
        .name { font-size: 3rem; }
        .profile-header { padding: 5rem 1rem 4rem; }
        .books-grid, .awards-grid, .contact-info { grid-template-columns: 1fr; }
    }
</style>

    <header class="profile-header">
        <img src="<?=baseUrl() . '/pictures/profile/Yasin_Mosavi_Aval.jpg' ?>" alt="یاسین موسوی اول" class="avatar">
        <h1 class="name">یاسین موسوی اول</h1>
        <p class="title">مدیر و بنیان‌گذار آموزشگاه موسیقی سُرناز</p>
        <p class="title">مدرس ویولن - ۱۲ سال سابقه تدریس حرفه‌ای</p>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">۱۲+</div>
                <div class="stat-label">سال تجربه</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">۸۵۰+</div>
                <div class="stat-label">هنرجوی آموزش‌دیده</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">۵۸</div>
                <div class="stat-label">کل مقاله ها</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">۴۸</div>
                <div class="stat-label">مقاله منتشرشده</div>
            </div>

        <div class="stat-item">
            <div class="stat-number">۲۱۲</div>
            <div class="stat-label">نظرات مدیریت‌شده</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">۱۸۰</div>
            <div class="stat-label">کاربر فعال</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">۹۸%</div>
            <div class="stat-label">رضایت هنرجویان</div>
        </div>
        </div>
    </header>

    <main class="main-content">

        <!-- بیوگرافی -->
    <section class="profile-card">
        <h2 class="section-title">بیوگرافی</h2>
        <p class="bio-text">
            من یاسین موسوی اول، نوازنده، پژوهشگر و مدرس موسیقی ایرانی هستم. بیش از ۱۲ سال تجربه در آموزش تئوری موسیقی، ردیف دستگاه‌ها و تحلیل موسیقی ایرانی دارم. 
            هدفم از تأسیس آموزشگاه سُرناز، ارائه آموزش‌های عمیق، ساده و کاربردی از موسیقی اصیل ایرانی به علاقه‌مندان است.
        </p>
        <p class="bio-text">
            آقای یاسین موسوی اول از سال ۱۳۹۲ به صورت حرفه‌ای مشغول تدریس ویولن هستند. ایشان فارغ‌التحصیل کارشناسی ارشد موسیقی از دانشگاه تهران بوده و نزد اساتید برجسته‌ای همچون استاد پرویز یاحقی و استاد علی‌اکبر شکارچی آموزش دیده‌اند.
        </p>
        <p class="bio-text">
            سبک تدریس ایشان ترکیبی از تکنیک کلاسیک و بیان احساسی موسیقی ایرانی است. بیش از ۴۵۰ هنرجو در سطوح مختلف زیر نظر ایشان آموزش دیده‌اند و بسیاری از شاگردانشان اکنون در ارکسترها و گروه‌های حرفه‌ای فعالیت می‌کنند.
        </p>

        <div class="skills-list">
            <span class="skill-tag">ویولن کلاسیک</span>
            <span class="skill-tag">ویولن ایرانی</span>
            <span class="skill-tag">سُلفژ و تئوری موسیقی</span>
            <span class="skill-tag">آموزش آنلاین و حضوری</span>
            <span class="skill-tag">آماده‌سازی کنکور موسیقی</span>
        </div>
    </section>

    <!-- گالری عکس‌های مدرس -->
    <section class="profile-card">
        <h2 class="section-title">گالری عکس‌ها</h2>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-3.jpg' ?>" 
                    alt="استاد احمدی در حال نواختن ویولن" 
                    loading="lazy">
                <div class="gallery-caption">اجرای زنده - جشنواره موسیقی فجر</div>
            </div>

            <div class="gallery-item">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-2.jpg' ?>" 
                    alt="کلاس ویولن" 
                    loading="lazy">
                <div class="gallery-caption">کلاس خصوصی ویولن</div>
            </div>

            <div class="gallery-item">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-1.jpg' ?>" 
                    alt="استاد در استودیو" 
                    loading="lazy">
                <div class="gallery-caption">ضبط آلبوم جدید</div>
            </div>

            <div class="gallery-item">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-3.jpg' ?>" 
                    alt="کنسرت گروهی" 
                    loading="lazy">
                <div class="gallery-caption">کنسرت با ارکستر ملی</div>
            </div>

            <div class="gallery-item">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-2.jpg' ?>" 
                    alt="جشن فارغ‌التحصیلی شاگردان" 
                    loading="lazy">
                <div class="gallery-caption">جشن فارغ‌التحصیلی هنرجویان</div>
            </div>
        </div>
    </section>



    <!-- کلاس‌های تدریس -->
    <section class="classes-section profile-card">
        <h2 class="section-title">کلاس‌های تدریس</h2>
        <div class="class-grid">
            <div class="class-card">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-3.jpg' ?>" alt="ویولن مقدماتی" class="class-img">
                <div class="class-body">
                    <h3 class="class-title">ویولن مقدماتی</h3>
                    <p class="class-time">شنبه و دوشنبه - ۱۸:۰۰ تا ۱۹:۳۰</p>
                    <p>شهریه: ۱,۲۰۰,۰۰۰ تومان (۱۲ جلسه)</p>
                </div>
            </div>

            <div class="class-card">
                <img src="<?=baseUrl() . '/pictures/profile/gallery-2.jpg' ?>" alt="ویولن پیشرفته" class="class-img">
                <div class="class-body">
                    <h3 class="class-title">ویولن پیشرفته و ردیف</h3>
                    <p class="class-time">یکشنبه و چهارشنبه - ۱۷:۰۰ تا ۱۸:۴۵</p>
                    <p>شهریه: ۱,۸۰۰,۰۰۰ تومان (۱۲ جلسه)</p>
                </div>
            </div>
        </div>
    </section>

    <!-- نظرات هنرجویان -->
    <section class="reviews-section profile-card">
        <h2 class="section-title">نظرات هنرجویان</h2>
        <div class="review-grid">
            <div class="review-card">
                <div class="review-header">
                    <img src="<?=baseUrl() . '/pictures/profile/gallery-1.jpg' ?>" alt="هنرجو" class="review-avatar">
                    <div>
                        <div class="review-name">سارا محمدی</div>
                        <div class="review-rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-text">
                    استاد احمدی واقعاً با حوصله و دقیق تدریس می‌کنن. تونستم تو کمتر از یک سال پیشرفت چشمگیری داشته باشم.
                </p>
            </div>

            <div class="review-card">
                <div class="review-header">
                    <img src="<?=baseUrl() . '/pictures/profile/gallery-2.jpg' ?>" alt="هنرجو" class="review-avatar">
                    <div>
                        <div class="review-name">امیرحسین کریمی</div>
                        <div class="review-rating">★★★★☆</div>
                    </div>
                </div>
                <p class="review-text">
                    تکنیک‌های خوبی یاد گرفتم. فقط کاش کلاس‌ها تعداد جلسات بیشتری داشته باشه.
                </p>
            </div>

            <!-- نظرات بیشتر -->
        </div>
    </section>

        <!-- سوابق تحصیلی -->
        <div class="profile-card">
            <h2 class="section-title">سوابق تحصیلی</h2>
            <ul class="education-list">
                <li class="education-item">
                    <span class="degree">کارشناسی ارشد موسیقی</span><br>
                    دانشگاه تهران - گرایش موسیقی ایرانی (۱۳۹۸)
                </li>
                <li class="education-item">
                    <span class="degree">کارشناسی موسیقی</span><br>
                    دانشگاه هنر تهران (۱۳۹۵)
                </li>
                <li class="education-item">
                    <span class="degree">دوره‌های تخصصی ردیف</span><br>
                    نزد اساتید برجسته: پرویز یاحقی، علی‌اکبر شکارچی و محمدعلی کیانی‌نژاد
                </li>
            </ul>
        </div>

        <!-- کتاب‌ها -->
        <div class="profile-card">
            <h2 class="section-title">کتاب‌ها و تألیفات</h2>
            <div class="books-grid">
                <div class="book-card">
                    <h3 class="book-title">ردیف موسیقی ایرانی به زبان ساده</h3>
                    <p>انتشارات سُرناز - ۱۴۰۲</p>
                </div>
                <div class="book-card">
                    <h3 class="book-title">مقایسه دستگاه‌های ایرانی با مقام‌های عربی</h3>
                    <p>انتشارات سُرناز - ۱۴۰۳</p>
                </div>
                <div class="book-card">
                    <h3 class="book-title">تئوری موسیقی برای هنرجویان مبتدی</h3>
                    <p>در حال چاپ - ۱۴۰۴</p>
                </div>
            </div>
        </div>

        <!-- جوایز و افتخارات -->
        <div class="profile-card">
            <h2 class="section-title">جوایز و افتخارات</h2>
            <div class="awards-grid">
                <div class="award-item">
                    <div class="award-icon">🏆</div>
                    <h3>رتبه اول جشنواره موسیقی فجر</h3>
                    <p>بخش پژوهش موسیقی ایرانی - ۱۴۰۱</p>
                </div>
                <div class="award-item">
                    <div class="award-icon">🥇</div>
                    <h3>نشان برتر فرهنگی</h3>
                    <p>از سوی وزارت فرهنگ و ارشاد اسلامی - ۱۴۰۲</p>
                </div>
                <div class="award-item">
                    <div class="award-icon">🌟</div>
                    <h3>استاد نمونه آموزشگاه‌های موسیقی</h3>
                    <p>سال ۱۴۰۳</p>
                </div>
            </div>
        </div>

        <!-- ویدئو معرفی -->
        <div class="profile-card">
            <h2 class="section-title">ویدئو معرفی</h2>
            <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:20px; box-shadow:var(--shadow);">
                <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" 
                        src="https://www.youtube.com/embed/YOUR_VIDEO_ID" 
                        title="معرفی یاسین موسوی اول" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen></iframe>
            </div>
        </div>

        <!-- مهارت‌ها -->
        <div class="profile-card">
            <h2 class="section-title">مهارت‌ها و تخصص‌ها</h2>
            <div class="skills">
                <span class="skill">ردیف و دستگاه‌های موسیقی ایرانی</span>
                <span class="skill">تحلیل و تئوری موسیقی</span>
                <span class="skill">تئوری موسیقی جهانی</span>
                <span class="skill">تحلیل موسیقی ایرانی</span>
                <span class="skill">مقایسه موسیقی ملل</span>
                <span class="skill">آموزش آنلاین و حضوری</span>
                <span class="skill">پژوهش موسیقی</span>
                <span class="skill">مدیریت آموزشی</span>
            </div>
        </div>

        <!-- شبکه‌های اجتماعی -->
        <div class="profile-card">
            <h2 class="section-title">شبکه‌های اجتماعی</h2>
            <div class="social-links">
                <a href="https://instagram.com/sornaz.music" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://t.me/sornazmusic" target="_blank"><i class="fab fa-telegram"></i></a>
                <a href="https://youtube.com/@sornazmusic" target="_blank"><i class="fab fa-youtube"></i></a>
                <a href="https://linkedin.com/in/alirezaei" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <!-- تماس مستقیم -->
        <div class="profile-card">
            <h2 class="section-title">تماس مستقیم</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <div><strong>ایمیل:</strong><br>ali.rezaei@sornaz.com</div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📱</span>
                    <div><strong>شماره تماس:</strong><br>۰۹۱۲ XXX XXXX</div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <div><strong>آدرس آموزشگاه:</strong><br>تهران، خیابان ولیعصر، پلاک ۱۲۳۴</div>
                </div>
            </div>
        </div>

        <!-- دکمه‌های اقدام -->
        <div class="action-buttons">
            <a href="/articles" class="btn btn-primary">مشاهده مقالات من</a>
            <a href="/contact" class="btn btn-outline">ارسال پیام</a>
        </div>

    </main>
