<?

// dump($data['contact_messages']);

$contact_messages = $data['contact_messages'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');

$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'contact_table_row_'), 'variable_name');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <!-- محتوای اصلی -->
    <div class="content">
        
        <div class="header_ac">
            <h1 class="h1_ac">ثبت‌نام کلاس‌های موسیقی</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <!-- هدر بالا -->
        <div class="topbar">
            <div class="search-bar">
                <input type="text" placeholder="جستجو در پنل...">
            </div>
            <div class="topbar-right">
                <i class="fas fa-bell notification"></i>
                <div class="profile">
                    <img src="https://via.placeholder.com/40" alt="پروفایل">
                </div>
            </div>
        </div>

        

        <!-- کارت‌های خوش‌آمدگویی و آمار -->
        <div class="welcome-card">
            <h2>خوش آمدید، مدیر!</h2>
            <p>امروز یک روز عالی برای مدیریت سایت سُرناز است!</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">85%</div>
                <div class="stat-label">بازدید روزانه</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                <div class="stat-number">400</div>
                <div class="stat-label">پیام جدید</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number">350</div>
                <div class="stat-label">کاربران فعال</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-number">۶۰,۶۰۶</div>
                <div class="stat-label">درآمد کل</div>
            </div>
        </div>

        <!-- محتوای اصلی -->
        <h1>خوش آمدید به داشبورد</h1>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div class="stat-number">۴۴۵</div>
                <div class="stat-label">تعداد کل هنرجو</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div class="stat-number">۴۵</div>
                <div class="stat-label">هنرجوی فعال</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👨‍🏫</div>
                <div class="stat-number">۷</div>
                <div class="stat-label">مدرس فعال</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number">۱۲</div>
                <div class="stat-label">کلاس فعال</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number">۸</div>
                <div class="stat-label">شهریه پرداخت نشده</div>
            </div>
        </div>

        <div class="welcome-card">
            <p>پیام های شما</p>
        </div>

        <? if(sizeof($contact_messages) != 0) { ?>
            <?// = showTable($contact_messages, 'Contact Messages', $settings) ?>
            <?//= showTable($contact_messages, 'Contact Messages', $settings, $table_headers_title) ?>
        <? } ?>
        <br>



        <!-- نمودار ساده (می‌تونی بعداً Chart.js اضافه کنی) -->
        <div class="chart-card">
            <h3>فعالیت اخیر</h3>
            <!-- اینجا می‌تونی canvas برای نمودار بذاری یا تصویر نمونه -->
            <img src="https://via.placeholder.com/800x300/0066cc/ffffff?text=نمودار+فعالیت" alt="نمودار فعالیت">
        </div>
        

        <div class="container_index">
            <h2>کلاس‌های فعال</h2>
            <div class="classes-grid">
                <div class="class-card">
                    <img src="<?=baseUrl() . '/pictures/home/slider_1-copyright.jpg'?>" alt="کلاس ویولن" class="class-img">
                    <div class="class-body">
                        <h3>کلاس ویولن مقدماتی</h3>
                        <p>مدرس: استاد احمدی<br>روزها: شنبه و دوشنبه - ۱۸:۰۰ تا ۱۹:۳۰<br>شهریه: ۱,۲۰۰,۰۰۰ تومان</p>
                        <a href="enroll.html">ثبت‌نام</a>
                    </div>
                </div>
                
                <div class="class-card">
                    <img src="<?=baseUrl() . '/pictures/home/slider_2-copyright-scaled.jpg'?>" alt="کلاس ویولن" class="class-img">
                    <div class="class-body">
                        <h3>کلاس پیانو پیشرفته</h3>
                        <p>مدرس: استاد رضایی<br>روزها: یکشنبه و چهارشنبه - ۱۷:۰۰ تا ۱۸:۳۰<br>شهریه: ۱,۸۰۰,۰۰۰ تومان</p>
                        <a href="enroll.html">ثبت‌نام</a>
                    </div>
                </div>

            </div>

            <!-- بخش نمودارها -->
            <div class="charts-section" style="margin-top: 4rem;">
                <h2 style="text-align:center; font-size:2.4rem; color:#0066cc; margin-bottom:2.5rem;">گزارش‌های آماری</h2>

                <div class="charts-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(480px,1fr)); gap:2.5rem;">
                    <!-- نمودار ۱: بازدید ماهانه (Line Chart) -->
                    <div class="chart-card" style="background:white; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.1); padding:2rem;">
                        <h3 style="text-align:center; margin-bottom:1.5rem;">بازدید ماهانه سایت</h3>
                        <canvas id="visitsChart" height="180"></canvas>
                    </div>

                    <!-- نمودار ۲: توزیع کلاس‌ها بر اساس ساز (Pie Chart) -->
                    <div class="chart-card" style="background:white; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.1); padding:2rem;">
                        <h3 style="text-align:center; margin-bottom:1.5rem;">توزیع کلاس‌ها بر اساس ساز</h3>
                        <canvas id="instrumentsChart" height="180"></canvas>
                    </div>

                    <!-- نمودار ۳: تعداد ثبت‌نام‌ها در ماه (Bar Chart) -->
                    <div class="chart-card" style="background:white; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.1); padding:2rem; grid-column: span 2;">
                        <h3 style="text-align:center; margin-bottom:1.5rem;">تعداد ثبت‌نام‌های ماهانه</h3>
                        <canvas id="enrollmentsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- اسکریپت Chart.js (در انتهای body یا فایل js جداگانه) -->
<script>
    // نمودار بازدید ماهانه (خطی)
    const visitsCtx = document.getElementById('visitsChart').getContext('2d');
    new Chart(visitsCtx, {
        type: 'line',
        data: {
            labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن'],
            datasets: [{
                label: 'بازدید ماهانه',
                data: [1200, 1900, 3000, 4500, 5200, 4800, 6100, 7200, 6800, 8500, 9200],
                borderColor: '#0066cc',
                backgroundColor: 'rgba(0,102,204,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // نمودار توزیع سازها (دایره‌ای)
    const instrumentsCtx = document.getElementById('instrumentsChart').getContext('2d');
    new Chart(instrumentsCtx, {
        type: 'pie',
        data: {
            labels: ['ویولن', 'پیانو', 'ستار', 'تمبک', 'گیتار', 'خوانندگی'],
            datasets: [{
                data: [28, 22, 15, 12, 18, 5],
                backgroundColor: ['#0066cc', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1']
            }]
        },
        options: { responsive: true }
    });

    // نمودار ثبت‌نام ماهانه (ستونی)
    const enrollmentsCtx = document.getElementById('enrollmentsChart').getContext('2d');
    new Chart(enrollmentsCtx, {
        type: 'bar',
        data: {
            labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
            datasets: [{
                label: 'تعداد ثبت‌نام',
                data: [15, 24, 38, 45, 52, 41],
                backgroundColor: '#0066cc',
                borderColor: '#004080',
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
