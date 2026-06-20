<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;

?>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <!-- محتوای اصلی -->
    <div class="content">
        <div class="header_ac">
            <img src="<?=baseUrl() . '/pictures/profile/Yasin_Mosavi_Aval.jpg' ?>" alt="علی رضایی" class="avatar">
            <h1 class="welcome h1_ac">خوش آمدی، یاسین موسوی اول!</h1>
            <p>آخرین ورود: ۱۴۰۴/۱۱/۲۷ - ۱۸:۴۵</p>
        </div>



            
        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_10_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_10_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_12_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_12_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_18_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_18_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>
        <hr>
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_2_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_3_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_4_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_4_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_6_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_6_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_8_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_8_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_11_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_11_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_14_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_14_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_15_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_15_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_16_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_16_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_17_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_17_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_19_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_19_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_20_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_20_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_21_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_21_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_22_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_22_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_23_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_23_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_24_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_24_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_25_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_25_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_26_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_26_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_27_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_27_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_29_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_29_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_30_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_30_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_31_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_31_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_32_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_32_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_33_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_33_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_34_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_34_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_35_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_35_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_28_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_28_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        <!-- <li class="filter-item"><a href="<?//=baseUrl() . $settings['user_panel_topbar_36_sidebar_1']['url'] ?>"><?//= translate($settings, 'user_panel_topbar_36_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li> -->
        
        
        
        <br>
        <br>
        <br>

        <main class="main-content">
            <div class="profile-sections">
                <!-- اطلاعات شخصی و دسترسی‌ها -->
                <section class="profile-card">
                    <h2 class="section-title">اطلاعات مدیر</h2>
                    <ul class="info-list">
                        <li class="info-item">
                            <i class="fas fa-user icon"></i>
                            <strong>نام کامل:</strong> یاسین موسوی اول</span>
                        </li>
                        <li class="info-item">
                            <i class="fas fa-envelope icon"></i>
                            <span><strong>ایمیل:</strong> admin@sornaz.com</span>
                        </li>
                        <li class="info-item">
                            <i class="fas fa-phone icon"></i>
                            <span><strong>شماره تماس:</strong> ۰۹۱۲XXXXXXX</span>
                        </li>
                        <li class="info-item">
                            <i class="fas fa-calendar-alt icon"></i>
                            <span><strong>تاریخ عضویت:</strong> ۱۴۰۳/۰۵/۱۰</span>
                        </li>
                        <li class="info-item">
                            <i class="fas fa-shield-alt icon"></i>
                            <span><strong>سطح دسترسی:</strong> مدیر کل (تمام دسترسی‌ها)</span>
                        </li>
                        <li class="info-item">
                            <i class="fas fa-clock icon"></i>
                            <span><strong>آخرین ورود:</strong> ۱۴۰۴/۱۱/۲۷ - ۱۸:۴۵</span>
                        </li>
                    </ul>
                </section>

                <!-- فعالیت‌های اخیر مدیر -->
                <section class="profile-card">
                    <h2 class="section-title">فعالیت‌های اخیر</h2>
                    <div class="activity-log">
                        <div class="log-item">
                            <span class="log-time">۱۴۰۴/۱۱/۲۷ - ۱۸:۳۰</span>
                            انتشار مقاله جدید: "ساختار موسیقی برنامه‌ای ایرانی"
                        </div>
                        <div class="log-item">
                            <span class="log-time">۱۴۰۴/۱۱/۲۷ - ۱۷:۱۰</span>
                            تأیید ۵ نظر جدید در مقاله "ردیف شور"
                        </div>
                        <div class="log-item">
                            <span class="log-time">۱۴۰۴/۱۱/۲۶ - ۲۲:۴۵</span>
                            افزودن مدرس جدید: استاد رضایی (پیانو)
                        </div>
                        <div class="log-item">
                            <span class="log-time">۱۴۰۴/۱۱/۲۶ - ۱۹:۲۰</span>
                            پشتیبان‌گیری کامل سیستم انجام شد
                        </div>
                        <div class="log-item">
                            <span class="log-time">۱۴۰۴/۱۱/۲۵ - ۱۴:۵۵</span>
                            تغییر تنظیمات سئو سایت
                        </div>
                        <!-- فعالیت‌های بیشتر -->
                    </div>
                </section>
            </div>

            <!-- دکمه‌های اقدام سریع -->
            <div style="text-align:center; margin:4rem 0;">
                <a href="settings.html" class="btn btn-primary">
                    <i class="fas fa-cog"></i> تنظیمات سایت
                </a>
                <a href="stats.html" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> مشاهده آمار کامل
                </a>
                <a href="backup.html" class="btn btn-primary">
                    <i class="fas fa-database"></i> پشتیبان‌گیری فوری
                </a>
            </div>
        </main>

        <br>
        <br>

        <main class="dashboard-grid">


            <!-- کارت کلاس فعلی -->
            <div class="dash-card">
                <div class="card-icon"><i class="fas fa-music"></i></div>
                <h2 class="card-title">کلاس فعلی شما</h2>
                <p class="class-info"><strong>ساز:</strong> ویولن مقدماتی</p>
                <p class="class-info"><strong>مدرس:</strong> استاد احمدی</p>
                <p class="class-info"><strong>زمان:</strong> شنبه و دوشنبه ۱۸:۰۰</p>
                <p class="class-info"><strong>جلسه باقی‌مانده:</strong> ۷ از ۱۲</p>
                <a href="#" class="btn">جزئیات کلاس</a>
            </div>

            <!-- کارت کلاس بعدی -->
            <div class="dash-card next-class">
                <div class="card-icon"><i class="far fa-calendar-check"></i></div>
                <h2 class="card-title">کلاس بعدی</h2>
                <p class="class-info"><strong>تاریخ:</strong> شنبه ۱۴۰۴/۱۲/۰۲</p>
                <p class="class-info"><strong>ساعت:</strong> ۱۸:۰۰ تا ۱۹:۳۰</p>
                <p class="class-info"><strong>موضوع:</strong> تکنیک آرشه و گام‌های پایه</p>
                <a href="#" class="btn">یادآوری تنظیم کن</a>
            </div>

            <!-- وضعیت مالی / شهریه -->
            <div class="dash-card">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <h2 class="card-title">وضعیت شهریه</h2>
                <p class="class-info"><strong>مبلغ کل:</strong> ۱,۲۰۰,۰۰۰ تومان</p>
                <p class="class-info"><strong>پرداخت شده:</strong> ۸۰۰,۰۰۰ تومان</p>
                <p class="class-info"><strong>باقیمانده:</strong> ۴۰۰,۰۰۰ تومان</p>
                <div style="margin-top:1.5rem;">
                    <a href="payment.html" class="btn">پرداخت اقساط</a>
                </div>
            </div>

            <!-- آخرین نظرات یا پیام‌ها -->
            <div class="dash-card">
                <div class="card-icon"><i class="fas fa-comment-dots"></i></div>
                <h2 class="card-title">پیام‌های اخیر</h2>
                <ul style="list-style:none; padding:0; font-size:1.4rem; color:#555;">
                    <li style="margin:1rem 0;">• استاد احمدی: تمرین هفته آینده گام مینور رو تمرین کنید</li>
                    <li style="margin:1rem 0;">• سیستم: شهریه جلسه بعد تا ۵ روز آینده پرداخت شود</li>
                </ul>
                <a href="#" class="btn">مشاهده همه پیام‌ها</a>
            </div>
        </main>

        <br>
        <br>
    </div>
</div>