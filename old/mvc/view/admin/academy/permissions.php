<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$permissions = $data['permissions'] ?? [];
// dump($branches_term_sessions);
// dump($branches_course_terms);
// exit();
?>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">مجوزها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRole/' ?>">اضافه کردن نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . '/admin/addPermission/' ?>">اضافه کردن دسترسی <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addRolePermission/' ?>">اضافه کردن دسترسی به نقش <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . '/admin/addSettingPermission/' ?>">اضافه کردن دسترسی به تنظیمات <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_access_system_permission/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />

            <div>
                <label for="group_name">نام گروه</label>
                <input type="text" id="group_name" name="group_name">
            </div>

            <div>
                <label for="name">نام</label>
                <input type="text" id="name" name="name">
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
                <label for="name_1">نام</label>
                <input type="text" id="name_1" name="name_1">
            </div>

            <div>
                <label for="title_1">عنوان</label>
                <input type="text" id="title_1" name="title_1">
            </div>

            <div>
                <label for="title_en_1">عنوان انگیسی</label>
                <input type="text" id="title_en_1" name="title_en_1">
            </div>






            
            <div>
                <label for="name_2">نام</label>
                <input type="text" id="name_2" name="name_2">
            </div>

            <div>
                <label for="title_2">عنوان</label>
                <input type="text" id="title_2" name="title_2">
            </div>

            <div>
                <label for="title_en_2">عنوان انگیسی</label>
                <input type="text" id="title_en_2" name="title_en_2">
            </div>






            
            <div>
                <label for="name_3">نام</label>
                <input type="text" id="name_3" name="name_3">
            </div>

            <div>
                <label for="title_3">عنوان</label>
                <input type="text" id="title_3" name="title_3">
            </div>

            <div>
                <label for="title_en_3">عنوان انگیسی</label>
                <input type="text" id="title_en_3" name="title_en_3">
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
                    <!-- <th>permission_id</th> -->
                    <th>name</th>
                    <!-- <th>group_name</th> -->
                    <th>locale</th>
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
                <? foreach($permissions as $key => $permission) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <!-- <td><?//= $permission['permission_id']?></td> -->
                        <td><?= $permission['name']?></td>
                        <!-- <td><?//= $permission['group_name']?></td> -->
                        <td><?= $permission['locale']?></td>
                        <td><?= $permission['title']?></td>
                        <td><?= $permission['created_at']?></td>
                        <td><?= $permission['created_by']?></td>
                        <td><?= $permission['updated_at']?></td>
                        <td><?= $permission['updated_by']?></td>
                        <td><?= $permission['approved_at']?></td>
                        <td><?= $permission['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>



    </div>
</div>
