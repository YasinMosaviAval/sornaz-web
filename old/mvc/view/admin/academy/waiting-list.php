<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches_course_terms = $data['branches_course_terms'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$terms_waiting_list = $data['terms_waiting_list'] ?? [];
$branches = $data['branches'] ?? [];
// dump($branches_course_terms);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">ثبت نام دانش‌آموز در ترم</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_5_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_5_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <? foreach($branches as $branch_id => $branch) { ?>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; <?= $branch['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['description']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['phone']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['national_code']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['birthday']?></span>
            <br>
            <br>

            <? foreach($branches_course_terms as $branch_course_terms) { ?>
                <? foreach($branch_course_terms as $branch_course_term) { ?>
                    <? if($branch_course_term['branch_id'] == $branch_id) { ?>
                        <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $branch_course_term['title']?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course_term['brief']?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course_term['description']?> &nbsp; / </span>
                        <br>
                        <br>

                        <table>
                            <thead>
                                <tr>
                                    <!-- <th>term_waiting_list_id</th> -->
                                    <!-- <th>term_id</th> -->
                                    <th>row</th>
                                    <th>member_id</th>
                                    <th>title</th>
                                    <th>created_at</th>
                                    <th>created_by</th>
                                    <th>updated_at</th>
                                    <th>updated_by</th>
                                    <th>approved_at</th>
                                    <th>approved_by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach($terms_waiting_list as $term_id => $terms_waiting) { ?>
                                    <? $row = 0; ?>
                                    <? foreach($terms_waiting as $waiting) { ?>
                                        <? if($branch_course_term['term_id'] == $term_id) { ?>
                                            <? $row++; ?>
                                            <tr>
                                                <!-- <td><?//= $waiting['term_waiting_list_id']?></td> -->
                                                <!-- <td><?//= $waiting['term_id']?></td> -->
                                                <td><?= $row ?></td>
                                                <td><?= $waiting['member_id']?></td>
                                                <td><?= $waiting['title']?></td>
                                                <td><?= $waiting['created_at']?></td>
                                                <td><?= $waiting['created_by']?></td>
                                                <td><?= $waiting['updated_at']?></td>
                                                <td><?= $waiting['updated_by']?></td>
                                                <td><?= $waiting['approved_at']?></td>
                                                <td><?= $waiting['approved_by']?></td>
                                            </tr>
                                        <? } ?>
                                    <? } ?>
                                <? } ?>
                            </tbody>
                        </table>
                        <br>
                    <? } ?>
                <? } ?>
            <? } ?>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_waiting_list/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />

            <div class="form-group">
                <label for="term_id">ترم دوره</label>
                <select id="term_id" name="term_id">
                    <? foreach ($branches_course_terms as $branch_id => $branches_course_term) { ?>
                        <? foreach ($branches_course_term as $term) { ?>
                            <option value="<?= $term['term_id'] ?>"><?= $term['title'] . " - " . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="member_id">اعضا</label>
                <select id="member_id" name="member_id">
                    <? foreach ($branches_members as $branch_id => $branch_members) { ?>
                        <? foreach ($branch_members as $member) { ?>
                            <option value="<?= $member['member_id'] ?>"><?= $member['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
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
            <button type="submit">ثبت نام</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>
