<?
$settings = setIndexforDataArray($data['settings'], 'setting_id');
$setting_permissions = $data['setting-permissions'] ?? [];
$permissions = setIndexforDataArray($data['permissions'], 'permission_id') ?? [];
$menu_settings = $data['menu-settings'] ?? [];

$line_part = 0;
$group_name = '';
$permission_group = $data['permission-group'] ?? '';


$panel_sidebar_items = getFilteredList($settings, 'panel_sidebar_');
$sort_order = array_column($panel_sidebar_items, 'sort_order');
array_multisort($sort_order, SORT_ASC, $panel_sidebar_items);
?>
    

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">مجوزهای تنظیمات</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRole/' ?>">اضافه کردن نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addPermission/' ?>">اضافه کردن دسترسی <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRolePermission/' ?>">اضافه کردن دسترسی به نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . '/admin/addSettingPermission/' ?>">اضافه کردن دسترسی به تنظیمات <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_access_system_setting_permission/">


            <select name="setting_id" id="setting_id">





                <? foreach($panel_sidebar_items as $key => $sidebar_item) { ?>
                    <?// if($sidebar_item['setting_id']) continue; ?>
                    <option value="<?= $sidebar_item['setting_id'] ?>"><?= $sidebar_item['title'] ?></option>
                <? } ?>
            </select>

            <br>
            <br>

            <? foreach($permissions as $permission) { ?>
                <? if ($permission['group_name'] !== $permission_group && $permission_group !== 'all' && $permission_group !== '') continue; ?>
                <? $line_part++; ?>
                <? if ($line_part > 1 && $line_part % 4 === 1)  { ?>
                <?// if (strhas($permission['name'], 'seen') || strhas($permission['name'], 'select')) { ?>
                    <br>
                <? } ?>
                <? if ($group_name !== $permission['group_name'] || $group_name === '') { ?>
                    <h4 style="width: 100%;"><?= $permission['group_name'] ?></h4>
                    <? $group_name = $permission['group_name']; ?>
                <? } ?>
                <!-- <label> -->
                    <input type="checkbox" name="permissions[]" value="<?= $permission['permission_id'] ?>">
                    <span style="width: 300px; display: inline-block;"><?= $permission['title'] ?></span>
                <!-- </label> -->
            <? } ?>

            <div>
                <label for="title">عنوان</label>
                <input type="text" id="title" name="title">
            </div>
            <div>
                <label for="brief">توضیح خلاصه</label>
                <input type="text" id="brief" name="brief">
            </div>
            <div>
                <label for="description">توضیح کامل</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <br>
            <button type="submit">ثبت مجوز تنظیمات</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>



        <br>

        <table>
            <thead>
                <tr>
                    <th>row</th>
                    <th>setting_title</th>
                    <th>permission_title</th>
                    <th>title</th>
                    <th>created_at</th>
                    <th>created_by</th>
                    <th>updated_at</th>
                    <th>updated_by</th>
                    <th>approved_at</th>
                    <th>approved_by</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($setting_permissions as $key => $setting_permission) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $settings[$setting_permission['setting_id']]['title']?></td>
                        <td><?= $permissions[$setting_permission['permission_id']]['title']?></td>
                        <td><?= $setting_permission['title']?></td>
                        <td><?= $setting_permission['created_at']?></td>
                        <td><?= $setting_permission['created_by']?></td>
                        <td><?= $setting_permission['updated_at']?></td>
                        <td><?= $setting_permission['updated_by']?></td>
                        <td><?= $setting_permission['approved_at']?></td>
                        <td><?= $setting_permission['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>



    </div>
</div>
