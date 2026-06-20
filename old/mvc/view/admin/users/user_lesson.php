<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$levels = setIndexforDataArray($data['levels'], 'level_id');
$branches = $data['branches'] ?? [];
$experiences = $data['experiences'] ?? [];
// dump($experiences);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">درس ها</h1>
            <p>درس های اصلی با رنگ سبز در جدول مشخص شده اند!</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_10_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_10_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_12_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_12_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_lesson_id</th> -->
                        <th>row</th>
                        <!-- <th>user_id</th> -->
                        <!-- <th>lesson_id</th> -->
                        <th>title</th>
                        <th>level</th>
                        <!-- <th>years_of_experience</th> -->
                        <!-- <th>is_primary</th> -->
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($lessons[$branch_id] as $key => $lesson) { ?>
                        <tr style="<?= $lesson['is_primary'] == 1 ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $lesson['user_lesson_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $lesson['user_id']?></td> -->
                            <!-- <td><?//= $lesson['lesson_id']?></td> -->
                            <td><a href="<?=baseUrl()?>/admin/editUserLesson/<?= $lesson['user_lesson_id'] ?>"><?= $lesson['title']?></a></td>
                            <td><?= $levels[$lesson['level_id']]['title'] ?></td>
                            <!-- <td><?//= $lesson['years_of_experience']?></td> -->
                            <!-- <td><?//= $lesson['is_primary']?></td> -->
                            <td><?= $lesson['created_at']?></td>
                            <td><?= $users[$lesson['created_by']]['title'] ?></td>
                            <td><?= $lesson['updated_at'] ?></td>
                            <td><?= $users[$lesson['updated_by']]['title'] ?></td>
                            <? if($lesson['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_lesson/<?= $lesson['user_lesson_id'] ?>">
                                        <button>تایید درس</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $lesson['approved_at'] ?></td>
                                <td><?= $users[$lesson['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_lesson/">
            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="level_id">سطح</label>
                    <select id="level_id" name="level_id">
                    <? foreach ($levels as $key => $level) { ?>
                        <option value="<?= $level['level_id'] ?>"><?= $level['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="years_of_experience">سال‌های تجربه</label><input type="number" id="years_of_experience" name="years_of_experience" required></div>
            
            <div class="form-group">
                <label for="is_primary">آیا درس اصلی است؟</label>
                <input type="checkbox" id="is_primary" name="is_primary">
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
            <button type="submit">ثبت درس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



