<?
$sidebar = setIndexforDataArray($data['settings'], 'variable_name');;
$superadmin_panel_sidebar_items = getFilteredList($sidebar, 'superadmin_panel_sidebar_');
$academy_panel_sidebar_items = getFilteredList($sidebar, 'academy_panel_sidebar_');
$user_panel_sidebar_items = getFilteredList($sidebar, 'user_panel_sidebar_');
$academy_managing_panel_sidebar_items = getFilteredList($sidebar, 'academy_managing_panel_sidebar_');

?>

<nav class="sidebar">
    <div>
        <a href="<?=baseUrl() . $sidebar['superadmin_panel_title']['url']?>"><h2><?= translate($sidebar, 'superadmin_panel_title') ?></h2></a>
    </div>

<hr>

    <ul class="sidebar-menu">
        <? if(isUser()) { ?>
            <? foreach($user_panel_sidebar_items as $key => $value) { ?>
                <? if($value['url'] != null) { ?>
                    <!-- <li class="active"></li> -->
                    <li><a href="<?=baseUrl() . $value['url'] ?>"><i class="fas fa-home"></i><span><?= translate($user_panel_sidebar_items, $key) ?></span></a></li>
                    <? if($value['status'] === "close-list") { ?>
                            </ul>
                        </li>
                    <? } ?>
                <? } else { ?>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-alt"></i><span><?= translate($user_panel_sidebar_items, $key) ?></span></a>
                        <ul class="submenu">
                <? } ?>
            <? } ?>
        <? } ?>

<hr>

        <? if(isSuperAdmin()) { ?>
            <? foreach($superadmin_panel_sidebar_items as $key => $value) { ?>
                <? if($value['url'] != null) { ?>
                    <!-- <li class="active"></li> -->
                    <li><a href="<?=baseUrl() . $value['url'] ?>"><i class="fas fa-home"></i><span><?= translate($superadmin_panel_sidebar_items, $key) ?></span></a></li>
                    <? if($value['status'] === "close-list") { ?>
                            </ul>
                        </li>
                    <? } ?>
                <? } else { ?>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-alt"></i><span><?= translate($superadmin_panel_sidebar_items, $key) ?></span></a>
                        <ul class="submenu">
                <? } ?>
            <? } ?>
        <? } ?>

<hr>

        <? if(isManager() || isReceptor()) { ?>
            <? foreach($academy_managing_panel_sidebar_items as $key => $value) { ?>
                <? if($value['url'] != null) { ?>
                    <!-- <li class="active"></li> -->
                    <li><a href="<?=baseUrl() . $value['url'] ?>"><i class="fas fa-home"></i><span><?= translate($academy_managing_panel_sidebar_items, $key) ?></span></a></li>
                    <? if($value['status'] === "close-list") { ?>
                            </ul>
                        </li>
                    <? } ?>
                <? } else { ?>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-alt"></i><span><?= translate($academy_managing_panel_sidebar_items, $key) ?></span></a>
                        <ul class="submenu">
                <? } ?>
            <? } ?>
        <? } ?>

<hr>

        <? if(isManager() || isReceptor() || isTeacher()) { ?>
            <? foreach($academy_panel_sidebar_items as $key => $value) { ?>
                <? if($value['url'] != null) { ?>
                    <!-- <li class="active"></li> -->
                    <li><a href="<?=baseUrl() . $value['url'] ?>"><i class="fas fa-home"></i><span><?= translate($academy_panel_sidebar_items, $key) ?></span></a></li>
                    <? if($value['status'] === "close-list") { ?>
                            </ul>
                        </li>
                    <? } ?>
                <? } else { ?>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-alt"></i><span><?= translate($academy_panel_sidebar_items, $key) ?></span></a>
                        <ul class="submenu">
                <? } ?>
            <? } ?>
        <? } ?>


    <li><a href="<?=baseUrl() . '/admin/addPermission/' ?>">add Permission</a></li>

    </ul>
</nav>

<script src="<?=baseUrl()?>/assets/scripts/admin.js"></script>



