<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$levels = setIndexforDataArray($data['levels'], 'level_id');
$branches = $data['branches'] ?? [];
$instruments = $data['instruments'] ?? [];
// dump($instruments);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">ساز ها</h1>
            <p>سازهای اصلی با رنگ سبز در جدول مشخص شده اند!</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_10_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_10_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_12_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_12_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_instrument_id</th> -->
                        <th>row</th>
                        <!-- <th>user_id</th> -->
                        <!-- <th>instrument_id</th> -->
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
                    <? foreach($instruments[$branch_id] as $key => $instrument) { ?>
                        <tr style="<?= $instrument['is_primary'] == 1 ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $instrument['user_instrument_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $users[$instrument['user_id']]['title'] ?></td> -->
                            <!-- <td><?//= $instrument['instrument_id']?></td> -->
                            <td><a href="<?=baseUrl()?>/admin/editUserInstrument/<?= $instrument['user_instrument_id'] ?>"><?= $instrument['title']?></a></td>
                            <td><?= $levels[$instrument['level_id']]['title'] ?></td>
                            <!-- <td><?//= $instrument['years_of_experience']?></td> -->
                            <!-- <td><?= $instrument['//is_primary']?></td> -->
                            <td><?= $instrument['created_at']?></td>
                            <td><?= $users[$instrument['created_by']]['title'] ?></td>
                            <td><?= $instrument['updated_at'] ?></td>
                            <td><?= $users[$instrument['updated_by']]['title'] ?></td>
                            <? if($instrument['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_instrument/<?= $instrument['user_instrument_id'] ?>">
                                        <button>تایید ساز</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $instrument['approved_at'] ?></td>
                                <td><?= $users[$instrument['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_instrument/">
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
                <label for="is_primary">آیا ساز اصلی است؟</label>
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
            <button type="submit">ثبت ساز</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



