<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$roles = $data['roles'] ?? [];
// dump($branches_term_sessions);
// dump($branches_course_terms);
// exit();
?>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">نقش ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . '/admin/addRole/' ?>">اضافه کردن نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addPermission/' ?>">اضافه کردن دسترسی <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRolePermission/' ?>">اضافه کردن دسترسی به نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addSettingPermission/' ?>">اضافه کردن دسترسی به تنظیمات <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_access_system_role/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />

            <div>
                <label for="name">نام</label>
                <input type="text" id="name" name="name">
            </div>

            <div class="form-group">
                <label for="type">نوع</label>
                <select id="type" name="type">
                    <option value="system">سیستم</option>
                    <option value="academy">آموزشگاه</option>
                    <option value="other">سایر</option>
                </select>
            </div>

            <div>
                <label for="title">عنوان</label>
                <input type="text" id="title" name="title">
            </div>

            <div>
                <label for="title_en">عنوان انگیسی</label>
                <input type="text" id="title_en" name="title_en">
            </div>


            <div>
                <label for="color">رنگ</label>
                <input type="color" id="color" name="color">
            </div>
            <div>
                <label for="sort_order">ترتیب</label>
                <input type="number" id="sort_order" name="sort_order">
            </div>

            <br>
            <button type="submit">ثبت مجوز</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>



        <br>

        <table>
            <thead>
                <tr>
                    <th>row</th>
                    <!-- <th>role_id</th> -->
                    <th>name</th>
                    <!-- <th>type</th> -->
                    <!-- <th>color</th> -->
                    <!-- <th>sort_order</th> -->
                    <th>locale</th>
                    <th>title</th>
                    <!-- <th>brief</th> -->
                    <!-- <th>description</th> -->
                    <th>created_at</th>
                    <th>created_by</th>
                    <th>updated_at</th>
                    <th>updated_by</th>
                    <th>approved_at</th>
                    <th>approved_by</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($roles as $key => $role) { ?>
                    <tr style="background-color: <?= $role['color'] ?>;">
                        <td><?= $key + 1 ?></td>
                        <!-- <td><?//= $role['role_id']?></td> -->
                        <td><?= $role['name']?></td>
                        <!-- <td><?//= $role['type']?></td> -->
                        <!-- <td><?//= $role['color']?></td> -->
                        <!-- <td><?//= $role['sort_order']?></td> -->
                        <td><?= $role['locale']?></td>
                        <td><?= $role['title']?></td>
                        <!-- <td><?//= $role['brief']?></td> -->
                        <!-- <td><?//= $role['description']?></td> -->
                        <td><?= $role['created_at']?></td>
                        <td><?= $role['created_by']?></td>
                        <td><?= $role['updated_at']?></td>
                        <td><?= $role['updated_by']?></td>
                        <td><?= $role['approved_at']?></td>
                        <td><?= $role['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>
        
    </div>
</div>
