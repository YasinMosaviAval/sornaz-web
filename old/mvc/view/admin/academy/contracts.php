<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$currencies = $data['currencies'] ?? [];
// dump($branches_members);
// exit();

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">قرارداد ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_6']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_6') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
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
                        <th>row</th>
                        <!-- <th>member_id</th> -->
                        <!-- <th>branch_id</th> -->
                        <!-- <th>user_id</th> -->
                        <!-- <th>role_id</th> -->
                        <th>name</th>
                        <th>title</th>
                        <!-- <th>brief</th> -->
                        <!-- <th>description</th> -->
                        <th>status</th>
                        <th>joined_at</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                        <!-- <th>type</th> -->
                        <!-- <th>color</th> -->
                        <!-- <th>sort_order</th> -->
                    </tr>
                </thead>
                <tbody>
                    <? foreach($branches_members[$branch_id] as $key => $branches_member) { ?>
                        <tr style="background-color: <?= $branches_member['color'] ?>;">
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $branches_member['member_id']?></td> -->
                            <!-- <td><?//= $branches_member['branch_id']?></td> -->
                            <!-- <td><?//= $branches_member['user_id']?></td> -->
                            <!-- <td><?//= $branches_member['role_id']?></td> -->
                            <td><?= $branches_member['name']?></td>
                            <td><?= $branches_member['title']?></td>
                            <!-- <td><?//= $branches_member['brief']?></td> -->
                            <!-- <td><?//= $branches_member['description']?></td> -->
                            <td><?= $branches_member['status']?></td>
                            <td><?= $branches_member['joined_at']?></td>
                            <td><?= $branches_member['created_at']?></td>
                            <td><?= $branches_member['created_by']?></td>
                            <td><?= $branches_member['updated_at']?></td>
                            <td><?= $branches_member['updated_by']?></td>
                            <td><?= $branches_member['approved_at']?></td>
                            <td><?= $branches_member['approved_by']?></td>
                            <!-- <td><?//= $branches_member['type']?></td> -->
                            <!-- <td><?//= $branches_member['color'] ?></td> -->
                            <!-- <td><?//= $branches_member['sort_order']?></td> -->
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>




        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_member_contract/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            
            <div class="form-group">
                <label for="member_id">نام عضو</label>
                <select id="member_id" name="member_id">
                    <? foreach ($branches_members as $branch_id => $branch_members) { ?>
                        <? foreach ($branch_members as $member) { ?>
                            <option value="<?= $member['user_id'] ?>"><?= $member['title'] . ' - ' . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="type">نوع قرارداد</label>
                <select id="type" name="type">
                    <option value="teacher">teacher</option>
                    <option value="receptionist">receptionist</option>
                    <option value="manager">manager</option>
                    <option value="other">other</option>
                </select>
            </div>
            
            <div>
                <label for="start_date">تاریخ شروع قرارداد</label>
                <input type="date" id="start_date" name="start_date">
            </div>

            <div>
                <label for="end_date">تاریخ پایان قرارداد</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            
            <div class="form-group">
                <label for="currency_id">نوع پول</label>
                <select id="currency_id" name="currency_id">
                    <? foreach ($currencies as $key => $currency) { ?>
                        <option value="<?= $currency['table_id'] ?>"><?= $currency['title'] ?></option>
                    <? } ?>
                </select>
            </div>
            
            <div>
                <label for="price">قیمت</label>
                <input type="number" id="price" name="price">
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
