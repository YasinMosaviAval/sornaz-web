<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$certificates = $data['certificates'] ?? [];
// dump($certificates);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">تاییدیه ها</h1>
            <p>تاییدیه هایی که هنوز به پایان نرسیده اند با رنگ سبز در جدول مشخص شده اند!</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_10_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_10_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_12_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_12_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_13_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_13_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_18_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_18_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_certificate_id</th> -->
                        <!-- <th>user_id</th> -->
                        <th>row</th>
                        <th>title</th>
                        <th>issuer</th>
                        <th>issue_date</th>
                        <th>expire_date</th>
                        <!-- <th>certificate_url</th> -->
                        <!-- <th>file_path</th> -->
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($certificates[$branch_id] as $key => $certificate) { ?>
                        <tr style="<?= $certificate['expire_date'] == null ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $certificate['user_certificate_id']?></td> -->
                            <!-- <td><?//= $certificate['user_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $certificate['title']?></td>
                            <td><?= $certificate['text_1']?></td>
                            <td><?= $certificate['issue_date']?></td>
                            <td><?= $certificate['expire_date']?></td>
                            <!-- <td><?//= $certificate['certificate_url']?></td> -->
                            <!-- <td><?//= $certificate['file_path']?></td> -->
                            <td><?= $certificate['created_at']?></td>
                            <td><?= $users[$certificate['created_by']]['title'] ?></td>
                            <td><?= $certificate['updated_at'] ?></td>
                            <td><?= $users[$certificate['updated_by']]['title'] ?></td>
                            <? if($certificate['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_certificate/<?= $certificate['user_certificate_id'] ?>">
                                        <button>تایید جایزه</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $certificate['approved_at'] ?></td>
                                <td><?= $users[$certificate['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_certificate/" enctype="multipart/form-data">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="subject_1" value="issuer" />
            
            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="text_1">تایید کننده</label><input type="text" id="text_1" name="text_1" required></div>

            <div><label for="issue_date">تاریخ اعطا</label><input type="date" id="issue_date" name="issue_date" required></div>

            <div><label for="expire_date">تاریخ انقضا</label><input type="date" id="expire_date" name="expire_date"></div>

            <div><label for="certificate_url">نشانی اینترنتی مدرک</label><input type="url" id="certificate_url" name="certificate_url"></div>

            <div>
                <label for="file_path"><?= translate($settings, 'edit_articles_cover') ?></label>
                <input type="file" id="file_path" name="file_path" accept="image/*">
                <!-- <input type="file" id="file_path" name="file_path" accept="image/*" value="" style="width: 500px;"> -->
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
            <button type="submit">ثبت تاییدیه</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>

