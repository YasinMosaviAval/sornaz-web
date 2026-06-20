<?
$settings = setIndexforDataArray($data['settings'], 'setting_id');
$role_permissions = $data['role-permissions'] ?? [];
$permissions = setIndexforDataArray($data['permissions'], 'permission_id') ?? [];
$roles = setIndexforDataArray($data['roles'], 'role_id') ?? [];
// dump($permissions);
// dump($branches_course_terms);
// exit();
$line_part = 0;
$group_name = '';
$permission_group = $data['permission-group'] ?? '';

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">مجوزهای نقش</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRole/' ?>">اضافه کردن نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addPermission/' ?>">اضافه کردن دسترسی <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . '/admin/addRolePermission/' ?>">اضافه کردن دسترسی به نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addSettingPermission/' ?>">اضافه کردن دسترسی به تنظیمات <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_access_system_role_permission/">

            <select name="role_id" id="role_id">
                <? foreach($roles as $key => $role) { ?>
                    <option value="<?= $role['role_id'] ?>"><?= $role['title'] ?></option>
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
                    <span style="width: 300px; display: inline-block;"><?= $permission['permission_id'] . '-' . $permission['title'] ?></span>
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
            <button type="submit">ثبت مجوز نقش</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>



        <br>

        <table>
            <thead>
                <tr>
                    <th>row</th>
                    <th>role_title</th>
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
                <? foreach($role_permissions as $key => $role_permission) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $roles[$role_permission['role_id']]['title']?></td>
                        <td><?= $permissions[$role_permission['permission_id']]['title']?></td>
                        <td><?= $role_permission['title']?></td>
                        <td><?= $role_permission['created_at']?></td>
                        <td><?= $role_permission['created_by']?></td>
                        <td><?= $role_permission['updated_at']?></td>
                        <td><?= $role_permission['updated_by']?></td>
                        <td><?= $role_permission['approved_at']?></td>
                        <td><?= $role_permission['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>



    </div>
</div>
