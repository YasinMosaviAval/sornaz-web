<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$poll = $data['poll'][0] ?? [];
$poll_votes = $data['poll_votes'] ?? [];
$poll_options = $data['poll_options'] ?? [];
// dump($poll_votes);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac"><h1 class="h1_ac">آرای نظرسنجی ها</h1></div>

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
        <div>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $poll['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['description']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $poll['creator_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['type']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['is_anonymous']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['status']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['votes_count']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['expires_at']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $poll['created_by']?> &nbsp; / </span>
            <span> &nbsp; <?= $poll['created_at']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $poll['text_1']?></span>
        </div>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <!-- <th>user_poll_vote_id</th> -->
                    <th>row</th>
                    <th>poll_id</th>
                    <th>option_id</th>
                    <th>title</th>
                    <th>user_id</th>
                    <th>created_at</th>
                    <th>created_by</th>
                    <th>updated_at</th>
                    <th>updated_by</th>
                    <th>approved_at</th>
                    <th>approved_by</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($poll_votes as $key => $poll_vote) { ?>
                    <tr>
                        <!-- <td><?//= $poll_vote['user_poll_vote_id']?></td> -->
                        <td><?= $key + 1 ?></td>
                        <td><?= $poll_vote['poll_id']?></td>
                        <td><?= $poll_vote['option_id']?></td>
                        <td><?= $poll_vote['title']?></td>
                        <td><?= $poll_vote['user_id']?></td>
                        <td><?= $poll_vote['created_at']?></td>
                        <td><?= $users[$poll_vote['created_by']]['title'] ?></td>
                        <td><?= $poll_vote['updated_at'] ?></td>
                        <td><?= $users[$poll_vote['updated_by']]['title'] ?></td>
                        <td><?= $poll_vote['approved_at'] ?></td>
                        <td><?= $users[$poll_vote['approved_by']]['title'] ?? '' ?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_poll_vote/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="user_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="poll_id" id="poll_id" value="<?= $poll['user_poll_id'] ?>" />

            <div class="radio-group">
                <? foreach($poll_options as $poll_option) { ?>
                    <label>
                        <input type="radio" name="option_id" value="<?= $poll_option['user_poll_option_id'] ?>">
                        <span><?= $poll_option['title'] ?></span>
                    </label>
                <!-- $poll_option['title']?> . ' - ' . $poll_option['brief']?> . ' - ' . $poll_option['description']?> . ' - ' . $poll_option['votes_count']?> . ' - ' .  -->
                <? } ?>
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
            <button type="submit">ثبت رأی</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



