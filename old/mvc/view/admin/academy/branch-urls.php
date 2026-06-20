<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$branches_urls = $data['branches_urls'] ?? [];
// dump($branches_urls);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac"><h1 class="h1_ac">افزودن لینک جلسه (Zoom و غیره)</h1></div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_id</th> -->
                        <th>row</th>
                        <th>title</th>
                        <!-- <th>brief</th> -->
                        <!-- <th>description</th> -->
                        <th>mode</th>
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
                    <? foreach($branches_urls[$branch_id] as $key => $branches_url) { ?>
                        <tr>
                            <!-- <td><?//= $branches_url['user_contact_id']?></td> -->
                            <!-- <td><?//= $branches_url['user_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $branches_url['title']?></td>
                            <!-- <td><?//= $branches_url['brief']?></td> -->
                            <!-- <td><?//= $branches_url['description']?></td> -->
                            <td><?= $branches_url['mode']?></td>
                            <td><?= $branches_url['platform']?></td>
                            <td><?= $branches_url['value']?></td>
                            <td><?= $branches_url['priority']?></td>
                            <td><?= $branches_url['is_main']?></td>
                            <td><?= $branches_url['status']?></td>
                            <td><?= $branches_url['created_at']?></td>
                            <td><?= $branches_url['created_by']?></td>
                            <td><?= $branches_url['updated_at']?></td>
                            <td><?= $branches_url['updated_by']?></td>
                            <td><?= $branches_url['approved_at']?></td>
                            <td><?= $branches_url['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_contact/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="mode" value="social" />
            
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
                    <option value="instagram">اینستاگرام</option>
                    <option value="telegram">تلگرام</option>
                    <option value="x">ایکس</option>
                    <option value="whats-app">واتس اپ</option>
                    <option value="youtube">یوتیوب</option>
                    <option value="linked-in">لینکدین</option>
                    <option value="google-meet">گوگل میت</option>
                    <option value="skype">اسکایپ</option>
                    <option value="zoom">زوم</option>
                    <option value="spotify">اسپاتیفای</option>
                    <option value="soundcloud">ساند کلاد</option>
                    <option value="website">وبسایت</option>
                    <option value="custom">شخصی</option>
                    <option value="other">سایر</option>
                </select>
            </div>

            <div><label for="value">لینک</label><input type="url" id="value" name="value" required></div>

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
                <label for="is_main">آیا آدرس اینترنتی اصلی است؟</label>
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
            <button type="submit">ثبت لینک</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



