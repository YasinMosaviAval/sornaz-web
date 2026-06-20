<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$educations = $data['educations'] ?? [];
// dump($educations);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">تحصیلات</h1>
            <p>تحصیلاتی که هنوز به پایان نرسیده اند با رنگ سبز در جدول مشخص شده اند!</p>
        </div>


        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_education_id</th> -->
                        <!-- <th>user_id</th> -->
                        <th>row</th>
                        <th>title</th>
                        <th>institution</th>
                        <th>field_of_study</th>
                        <th>degree</th>
                        <th>start_date</th>
                        <th>end_date</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($educations[$branch_id] as $key => $education) { ?>
                        <tr style="<?= $education['end_date'] == null ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $education['user_education_id']?></td> -->
                            <!-- <td><?//= $education['user_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $education['title']?></td>
                            <td><?= $education['text_1']?></td>
                            <td><?= $education['text_2']?></td>
                            <td><?= $education['text_3']?></td>
                            <td><?= $education['start_date']?></td>
                            <td><?= $education['end_date']?></td>
                            <td><?= $education['created_at']?></td>
                            <td><?= $users[$education['created_by']]['title'] ?></td>
                            <td><?= $education['updated_at'] ?></td>
                            <td><?= $users[$education['updated_by']]['title'] ?></td>
                            <? if($education['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_education/<?= $education['user_education_id'] ?>">
                                        <button>تایید تحصیلات</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $education['approved_at'] ?></td>
                                <td><?= $users[$education['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_education/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="subject_1" value="institution" />
            <input type="hidden" name="subject_2" value="field_of_study" />
            <input type="hidden" name="subject_3" value="degree" />
            
            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="text_1">موسسه</label><input type="text" id="text_1" name="text_1" required></div>
            <div><label for="text_2">رشته مطالعاتی</label><input type="text" id="text_2" name="text_2" required></div>
            <div><label for="text_3">مدرک</label><input type="text" id="text_3" name="text_3" required></div>

            <div><label for="start_date">تاریخ شروع</label><input type="date" id="start_date" name="start_date" required></div>

            <div><label for="end_date">تاریخ پایان</label><input type="date" id="end_date" name="end_date"></div>

            <!-- <div class="form-group">
                <label for="is_current">آیا هنوز ادامه دارد؟</label>
                <input type="checkbox" id="is_current" name="is_current">
            </div> -->

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
            <button type="submit">ثبت تحصیلات</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



