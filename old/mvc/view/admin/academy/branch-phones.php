<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$branches = $data['branches'] ?? [];
$branches_phones = $data['branches_phones'] ?? [];
// dump($branches_phones);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">شماره تماس های شعبه</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>


        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_contact_id</th> -->
                        <th>row</th>
                        <!-- <th>user_id</th> -->
                        <th>title</th>
                        <!-- <th>brief</th> -->
                        <!-- <th>description</th> -->
                        <!-- <th>mode</th> -->
                        <th>platform</th>
                        <th>value</th>
                        <th>priority</th>
                        <th>is_main</th>
                        <th>status</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($branches_phones[$branch_id] as $key => $branches_phone) { ?>
                        <tr>
                            <!-- <td><?//= $branches_phone['user_contact_id'] ?></td> -->
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $branches_phone['user_id'] ?></td> -->
                            <td><?= $branches_phone['title']?></td>
                            <!-- <td><?//= $branches_phone['brief']?></td> -->
                            <!-- <td><?//= $branches_phone['description']?></td> -->
                            <!-- <td><?//= $branches_phone['mode']?></td> -->
                            <td><?= $branches_phone['platform']?></td>
                            <td><?= $branches_phone['value']?></td>
                            <td><?= $branches_phone['priority']?></td>
                            <td><?= $branches_phone['is_main']?></td>
                            <td><?= $branches_phone['status']?></td>
                            <td><?= $branches_phone['created_at']?></td>
                            <td><?= $branches_phone['created_by']?></td>
                            <td><?= $branches_phone['updated_at']?></td>
                            <td><?= $branches_phone['updated_by']?></td>
                            <td><?= $branches_phone['approved_at']?></td>
                            <td><?= $branches_phone['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>



        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_contact/" enctype="multipart/form-data" id="addCategoryForm">
            <input type="hidden" name="manager_id" id="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="mode" value="phone" />

            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="platform">پلتفرم</label>
                <select id="platform" name="platform">
                    <option value="telegram">تلگرام</option>
                    <option value="whats-app">واتس اپ</option>
                    <option value="other">سایر</option>
                </select>
            </div>

            <div><label for="value">شماره تماس</label><input type="tel" id="value" name="value" required></div>

            <div class="form-group">
                <label for="priority">اولویت</label>
                <select id="priority" name="priority">
                    <option value="primary">اصلی</option>
                    <option value="secondary">فرعی</option>
                    <option value="emergency">اضطراری</option>
                    <option value="ledger">مالی</option>
                    <option value="support">پشتیبانی</option>
                    <option value="other">سایر</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_main">آیا شماره تماس اصلی است؟</label>
                <input type="checkbox" id="is_main" name="is_main">
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

            <button type="submit">ثبت شماره</button>
            <button type="reset" class="btn-outline"><?= translate($settings, 'add_new_academy_discard_button') ?></button>
        </form>
    </div>
</div>

