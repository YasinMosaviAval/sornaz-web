<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$branches = $data['branches'] ?? [];
$polls = $data['polls'] ?? [];
// dump($polls);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">نظرسنجی ها</h1>
            <p>نظرسنجی هایی که فعال هستند با رنگ سبز در جدول مشخص شده اند!</p>
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
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_18_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_18_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_poll_id</th> -->
                        <th>row</th>
                        <!-- <th>owner_id</th> -->
                        <th>title</th>
                        <!-- <th>question</th> -->
                        <!-- <th>target_type</th> -->
                        <!-- <th>target_id</th> -->
                        <!-- <th>type</th> -->
                        <!-- <th>is_anonymous</th> -->
                        <!-- <th>status</th> -->
                        <!-- <th>votes_count</th> -->
                        <th>expires_at</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                        <th>status</th>
                        <th>poll options</th>
                        <th>poll votes</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($polls[$branch_id] as $key => $poll) { ?>
                        <tr style="<?= $poll['status'] == 'active' ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $poll['user_poll_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $users[$poll['owner_id']]['title'] ?></td> -->
                            <td><a href="<?=baseUrl()?>/admin/editUserPoll/<?= $poll['user_poll_id'] ?>"><?= $poll['title']?></a></td>
                            <!-- <td><?//= $poll['text_1']?></td> -->
                            <!-- <td><?//= $poll['target_type']?></td> -->
                            <!-- <td><?//= $poll['target_id']?></td> -->
                            <!-- <td><?//= $poll['type']?></td> -->
                            <!-- <td><?//= $poll['is_anonymous']?></td> -->
                            <!-- <td><?//= $poll['status']?></td> -->
                            <!-- <td><?//= $poll['votes_count']?></td> -->
                            <td><?= $poll['expires_at']?></td>
                            <td><?= $poll['created_at']?></td>
                            <td><?= $users[$poll['created_by']]['title'] ?></td>
                            <td><?= $poll['updated_at'] ?></td>
                            <td><?= $users[$poll['updated_by']]['title'] ?></td>
                            <? if($poll['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_poll/<?= $poll['user_poll_id'] ?>">
                                        <button>تایید نظرسنجی</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $poll['approved_at'] ?></td>
                                <td><?= $users[$poll['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                            <? if($poll['status'] == 'closed') { ?>
                                <td><a href="<?=baseUrl()?>/admin/status_active_user_poll/<?= $poll['user_poll_id'] ?>"><button>باز کردن</button></a></td>
                            <? } else { ?>
                                <td><a href="<?=baseUrl()?>/admin/status_closed_user_poll/<?= $poll['user_poll_id'] ?>"><button>بستن</button></a></td>
                            <? } ?>
                            <td><a href="<?= baseUrl() . $settings['user_panel_topbar_18_1_sidebar_1']['url'] . '/' . $poll['user_poll_id'] ?>"><?= translate($settings, 'user_panel_topbar_18_1_sidebar_1') ?></a></td>
                            <td><a href="<?= baseUrl() . $settings['user_panel_topbar_18_2_sidebar_1']['url'] . '/' . $poll['user_poll_id'] ?>"><?= translate($settings, 'user_panel_topbar_18_2_sidebar_1') ?></a></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_poll/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="target_type" value="" />
            <input type="hidden" name="target_id" value="0" />
            <input type="hidden" name="status" value="deactive" />
            <input type="hidden" name="subject_1" value="question" />
            
            <div class="form-group">
                <label for="owner_id">شعبه</label>
                <select id="owner_id" name="owner_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>
            
            <div><label for="text_1">سوال</label><textarea id="text_1" name="text_1" rows="3" required></textarea></div>

            <div class="form-group">
                <label for="type">شهر</label>
                <select id="type" name="type">
                    <option value="single">تکی</option>
                    <option value="multiple">چندگانه</option>
                </select>
            </div>
            <div><label for="expires_at">تاریخ پایان</label><input type="datetime-local" id="expires_at" name="expires_at"></div>
            
            <div class="form-group">
                <label for="is_anonymous">آیا ناشناس باشد؟</label>
                <input type="checkbox" id="is_anonymous" name="is_anonymous">
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
            <button type="submit">ثبت نظرسنجی</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



