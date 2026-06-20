<?
$sidebar = setIndexforDataArray($data['settings'], 'variable_name');
$user_permissions = $data['user-permissions'] ?? [];
$menu_permissions = $data['menu-permissions'] ?? [];

$menu_permissions_to_ids = array_column($menu_permissions, 'permission_id');
$user_permissions_to_ids = array_column($user_permissions, 'permission_id');

$shown_menu_permissions = setIndexforDataArray($menu_permissions, 'permission_id');


// dump($shown_menu_permissions);
// foreach ($user_permissions_to_ids as $id) {
//     if (in_array($id, $menu_permissions_to_ids)) {
//         echo "permission_id $id در آرایه دوم وجود دارد ✅\n";
//     } else {
//         echo "permission_id $id در آرایه دوم وجود ندارد ❌\n";
//     }
// }
$setting_ids = [];
?>

<nav class="sidebar">
    
    <?// dump($menu_permissions);?>
    <?// dump($user_permissions_to_ids);?>
    <div>
        <a href="<?=baseUrl() . $sidebar['superadmin_panel_title']['url']?>"><h2><?= translate($sidebar, 'superadmin_panel_title') ?></h2></a>
    </div>
    <hr>
    <ul class="sidebar-menu">
        <?//= sizeof($shown_menu_permissions); ?>
        <? foreach ($user_permissions_to_ids as $id) { ?>
            <? if (in_array($id, $menu_permissions_to_ids) && !in_array($shown_menu_permissions[$id]['setting_id'], $setting_ids)) { ?>
                <? $setting_ids[] = $shown_menu_permissions[$id]['setting_id'] ?>
                <li><a href="<?=baseUrl() . $shown_menu_permissions[$id]['url'] ?>"><i class="fas fa-home"></i><span><?= $shown_menu_permissions[$id]['title'] ?></span></a></li>
            <? } ?>
        <? } ?>
    </ul>
</nav>

<script src="<?=baseUrl()?>/assets/scripts/admin.js"></script>



