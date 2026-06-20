<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$branches = $data['branches'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$roles = $data['roles'] ?? [];

// dump($branches);
// dump($branches_members);
// dump($_SESSION);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">مدیران</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>


<br>

        <? foreach($branches as $branch_id => $branch) { ?>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $branch['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['description']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['phone']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['national_code']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['birthday']?></span>
            <br>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>branch_id</th>
                        <th>user_id</th>
                        <th>title</th>
                        <th>brief</th>
                        <th>description</th>
                        <th>role_id</th>
                        <th>status</th>
                        <th>joined_at</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($branches_members[$branch_id] as $branch_member) { ?>
                        <tr>
                            <td><?= $branch_member['id']?></td>
                            <td><?= $branch_member['branch_id']?></td>
                            <td><?= $branch_member['user_id']?></td>
                            <td><?= $branch_member['title']?></td>
                            <td><?= $branch_member['brief']?></td>
                            <td><?= $branch_member['description']?></td>
                            <td><?= $branch_member['role_id']?></td>
                            <td><?= $branch_member['status']?></td>
                            <td><?= $branch_member['joined_at']?></td>
                            <td><?= $branch_member['created_at']?></td>
                            <td><?= $users[$branch_member['created_by']]['title'] ?></td>
                            <td><?= $branch_member['updated_at'] ?></td>
                            <td><?= $users[$branch_member['updated_by']]['title'] ?></td>
                            <td><?= $branch_member['approved_at'] ?></td>
                            <td><?= $users[$branch_member['approved_by']]['title'] ?? '' ?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_member/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            
            <div class="form-group">
                <label for="branch_id">شعبه</label>
                <select id="branch_id" name="branch_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $key ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role_id">نقش</label>
                <select id="role_id" name="role_id">
                    <? foreach ($roles as $key => $role) { ?>
                        <option value="<?= $role['table_id'] ?>"><?= $role['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">وضعیت</label>
                <select id="status" name="status">
                    <option value="active">active</option>
                    <option value="pending">pending</option>
                    <option value="rejected">rejected</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gender">جنسیت</label>
                <select id="gender" name="gender">
                    <option value="male">مرد</option>
                    <option value="female">زن</option>
                </select>
            </div>
            
            <div>
                <label for="joined_at">تاریخ شروع کار</label>
                <input type="date" id="joined_at" name="joined_at">
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
            <button type="submit">ثبت کلاس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>
