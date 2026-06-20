<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <!-- صفحه آمار (stats.html) -->
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac">آمار و گزارش ها</h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>
            

            

            <div class="stats-overview">
                <div class="stat-box">
                    <h3>بازدید کل</h3>
                    <div class="stat-value">۴۵,۸۲۰</div>
                    <div class="stat-change positive">+۱۸% نسبت به ماه قبل</div>
                </div>
                <div class="stat-box">
                    <h3>مقاله‌های منتشرشده</h3>
                    <div class="stat-value">۴۸</div>
                </div>
                <div class="stat-box">
                    <h3>نظرات</h3>
                    <div class="stat-value">۲۱۲</div>
                </div>
                <div class="stat-box">
                    <h3>کاربران ثبت‌نام‌شده</h3>
                    <div class="stat-value">۱۸۰</div>
                </div>
            </div>

            <!-- نمودار ساده (placeholder) -->
            <div class="chart-container">
                <h3>روند بازدید ماهانه</h3>
                <img src="https://via.placeholder.com/1000x400/0066cc/ffffff?text=نمودار+بازدید+۱۴۰۴" alt="نمودار بازدید" class="chart-img">
            </div>
        </div>
    </div>

</div>
