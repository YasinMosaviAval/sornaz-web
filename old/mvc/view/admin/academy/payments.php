<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">پرداخت ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <h1>پرداخت شهریه کلاس موسیقی</h1>

        <form action="">
            <div class="container_ac">
                <div class="payment-box">
                    <h2>جزئیات پرداخت</h2>
                    <p class="details">
                        نام هنرجو: علی رضایی<br>
                        کلاس: ویولن مقدماتی<br>
                        مدرس: استاد احمدی<br>
                        دوره: ۱۲ جلسه (۳ ماه)<br>
                        شهریه کل: 
                    </p>
                    <div class="amount">۱,۲۰۰,۰۰۰ تومان</div>

                    <button class="btn-pay">پرداخت آنلاین با درگاه بانکی</button>
                    <p style="margin-top:1.5rem; color:#777;">پس از پرداخت موفق، تأییدیه برای شما ارسال می‌شود.</p>
                </div>
            </div>
        </form>
    </div>
</div>
