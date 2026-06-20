<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$branches_users_data = $data['branches_users_data'] ?? [];
$users_exceptions = $data['users_exceptions'] ?? [];
// dump($users_exceptions);
// dump($branches_users_data);

?>
<!-- 


    id
    email
    username
    password
    phone
    national_code
    gender
    status
    visibility
    birthday
    register_time
    last_visit_time
    created_at
    created_by
    updated_at
    updated_by
    approved_at
    approved_by
    deleted_at
    deleted_by
    branch_id
    user_id
    role_id
    joined_at
    translation_id
    table_name
    table_id
    locale
    code
    title
    brief
    description

-->

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">عدم حضور - اتفاقات</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_15']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_15') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_15']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_15') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <br>

            <table>
                <thead>
                    <tr>
                        <!-- <th>user_availability_exception_id</th> -->
                        <th>row</th>
                        <th>user_id</th>
                        <th>title</th>
                        <th>date</th>
                        <th>start_time</th>
                        <th>end_time</th>
                        <th>type</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($users_exceptions as $key => $users_exception) { ?>
                        <tr>
                            <!-- <td><?//= $users_exception['user_availability_exception_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $users_exception['user_id']?></td>
                            <td><?= $users_exception['title']?></td>
                            <td><?= $users_exception['date']?></td>
                            <td><?= $users_exception['start_time']?></td>
                            <td><?= $users_exception['end_time']?></td>
                            <td><?= $users_exception['type']?></td>
                            <td><?= $users_exception['created_at']?></td>
                            <td><?= $users_exception['created_by']?></td>
                            <td><?= $users_exception['updated_at']?></td>
                            <td><?= $users_exception['updated_by']?></td>
                            <td><?= $users_exception['approved_at']?></td>
                            <td><?= $users_exception['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        
        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_availability_exception/">
            <!-- <input type="hidden" name="user_id" value="<?//= session_get('user_id') ?>" /> -->
            <input type="hidden" name="created_by" value="<?= session_get('user_id') ?>" />

            <div class="form-group">
                <label for="member_user_id">اعضا</label>
                <select id="member_user_id" name="member_user_id">
                    <? foreach ($branches_members as $branch_id => $branch_members) { ?>
                        <? foreach ($branch_members as $member) { ?>
                            <option value="<?= $member['user_id'] ?>"><?= $member['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div>
                <label for="date">تاریخ</label>
                <input type="date" id="date" name="date">
            </div>

            <div>
                <label for="start_time">ساعت شروع</label>
                <input type="time" id="start_time" name="start_time">
            </div>

            <div>
                <label for="end_time">ساعت پایان</label>
                <input type="time" id="end_time" name="end_time">
            </div>


            <div class="form-group">
                <label for="type">نوع عدم حضور</label>
                <select id="type" name="type">
                    <option value="unavailable">خارج از دسترس</option>
                    <option value="holiday">تعطیلات</option>
                    <option value="closed">بسته بودن</option>
                    <option value="busy">مشغله داشتن</option>
                    <option value="vacation">موقعیت مکانی دیگر</option>
                    <option value="blocked">بلاک شدن</option>
                </select>
            </div>

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
            <button type="submit">ثبت نام</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>
