<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
            <div class="header_ac">
                <h1 class="h1_ac">لیست کالا ها</h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>


        <div style="text-align: end; margin: 2rem;">
            <a href="<?=baseUrl() . $settings['superadmin_panel_topbar_2_sidebar_9']['url'] ?>" class="btn-outline"><?= translate($settings, 'superadmin_panel_topbar_2_sidebar_9') ?></a>
        </div>

    </div>
</div>
