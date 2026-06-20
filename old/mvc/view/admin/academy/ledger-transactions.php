<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">حساب های مالی</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

    </div>
</div>
