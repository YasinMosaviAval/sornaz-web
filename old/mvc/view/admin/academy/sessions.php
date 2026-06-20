<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches_course_terms = $data['branches_course_terms'] ?? [];
$branches_urls = $data['branches_urls'] ?? [];
$branches_classrooms = $data['branches_classrooms'] ?? [];
$branches_term_sessions = $data['branches_term_sessions'] ?? [];
$branches_courses = $data['branches_courses'] ?? [];
$branches = $data['branches'] ?? [];
$currencies = $data['currencies'] ?? [];
// dump($branches_term_sessions);
// dump($branches_course_terms);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">جلسه ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_5_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_5_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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

            <? foreach($branches_courses as $branch_courses) { ?>
                <? foreach($branch_courses as $branch_course) { ?>
                    <? if($branch_course['branch_id'] == $branch_id) { ?>
                        <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $branch_course['title'] ?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course['brief'] ?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course['description'] ?> &nbsp; / </span>
                        <br>
                        <br>

                        <? foreach($branches_course_terms as $branches_course_term) { ?>
                            <? foreach($branches_course_term as $term) { ?>
                                <? if($term['course_id'] == $branch_course['course_id']) { ?>
                                    <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $term['title'] ?> &nbsp; / </span>
                                    <span> &nbsp; <?= $term['brief'] ?> &nbsp; / </span>
                                    <span> &nbsp; <?= $term['description'] ?> &nbsp; / </span>
                                    <br>
                                    <br>

                                    <table>
                                        <thead>
                                            <tr>
                                                <!-- <th>term_session_id</th> -->
                                                <th>row</th>
                                                <!-- <th>term</th> -->
                                                <!-- <th>term_id</th> -->
                                                <!-- <th>booking_id</th> -->
                                                <!-- <th>classroom_id</th> -->
                                                <!-- <th>branch_url_id</th> -->
                                                <!-- <th>teacher_id</th> -->
                                                <th>title</th>
                                                <th>status</th>
                                                <th>created_at</th>
                                                <th>created_by</th>
                                                <th>updated_at</th>
                                                <th>updated_by</th>
                                                <th>approved_at</th>
                                                <th>approved_by</th>
                                                <th><?= translate($settings, 'academy_beanch_cource_term_session_attendances') ?></th>
                                                <th><?= translate($settings, 'academy_beanch_cource_term_session_classrooms') ?></th>
                                                <th><?= translate($settings, 'academy_beanch_cource_term_session_changes') ?></th>
                                                <!-- <th>deleted_at</th> -->
                                                <!-- <th>deleted_by</th> -->
                                                <!-- <th>course_id</th> -->
                                                <!-- <th>start_date</th> -->
                                                <!-- <th>end_date</th> -->
                                                <!-- <th>session_count</th> -->
                                                <!-- <th>price</th> -->
                                                <!-- <th>currency_id</th> -->
                                                <!-- <th>branch_id</th> -->
                                                <!-- <th>level_id</th> -->
                                                <!-- <th>capacity</th> -->
                                                <!-- <th>translation_id</th> -->
                                                <!-- <th>table_name</th> -->
                                                <!-- <th>table_id</th> -->
                                                <!-- <th>locale</th> -->
                                                <!-- <th>code</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? foreach($branches_term_sessions as $term_sessions) { ?>
                                                <? $row = 0 ?>
                                                <? foreach($term_sessions as $session) { ?>
                                                    <? if($session['term_id'] == $term['term_id']) { ?>
                                                        <? $row++ ?>
                                                        <tr>
                                                            <td><?= $row ?></td>
                                                            <!-- <td><?//= $session['term_session_id']?></td> -->
                                                            <!-- <td><?//= $session['term_id']?></td> -->
                                                            <!-- <td><?//= $term['title']?></td> -->
                                                            <!-- <td><?//= $session['booking_id']?></td> -->
                                                            <!-- <td><?//= $session['classroom_id']?></td> -->
                                                            <!-- <td><?//= $session['branch_url_id']?></td> -->
                                                            <!-- <td><?//= $session['teacher_id']?></td> -->
                                                            <td><?= $session['title']?></td>
                                                            <td><?= $session['status']?></td>
                                                            <td><?= $session['created_at']?></td>
                                                            <td><?= $session['created_by']?></td>
                                                            <td><?= $session['updated_at']?></td>
                                                            <td><?= $session['updated_by']?></td>
                                                            <td><?= $session['approved_at']?></td>
                                                            <td><?= $session['approved_by']?></td>
                                                            <td><a href="<?=baseUrl() . $settings['academy_beanch_cource_term_session_attendances']['url'] . '/' . $session['term_session_id'] ?>"><?= translate($settings, 'academy_beanch_cource_term_session_attendances') ?></a></td>
                                                            <td><a href="<?=baseUrl() . $settings['academy_beanch_cource_term_session_classrooms']['url'] . '/' . $session['term_session_id'] ?>"><?= translate($settings, 'academy_beanch_cource_term_session_classrooms') ?></a></td>
                                                            <td><a href="<?=baseUrl() . $settings['academy_beanch_cource_term_session_changes']['url'] . '/' . $session['term_session_id'] ?>"><?= translate($settings, 'academy_beanch_cource_term_session_changes') ?></a></td>
                                                            <!-- <td><?//= $session['deleted_at']?></td> -->
                                                            <!-- <td><?//= $session['deleted_by']?></td> -->
                                                            <!-- <td><?//= $session['course_id']?></td> -->
                                                            <!-- <td><?//= $session['start_date']?></td> -->
                                                            <!-- <td><?//= $session['end_date']?></td> -->
                                                            <!-- <td><?//= $session['session_count']?></td> -->
                                                            <!-- <td><?//= $session['price']?></td> -->
                                                            <!-- <td><?//= $session['currency_id']?></td> -->
                                                            <!-- <td><?//= $session['branch_id']?></td> -->
                                                            <!-- <td><?//= $session['level_id']?></td> -->
                                                            <!-- <td><?//= $session['capacity']?></td> -->
                                                            <!-- <td><?//= $session['translation_id']?></td> -->
                                                            <!-- <td><?//= $session['table_name']?></td> -->
                                                            <!-- <td><?//= $session['table_id']?></td> -->
                                                            <!-- <td><?//= $session['locale']?></td> -->
                                                            <!-- <td><?//= $session['code']?></td> -->
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
                <? } ?>
            <? } ?>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>




        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_session/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="type" value="student" />

            <div class="form-group">
                <label for="term_id">ترم دوره</label>
                <select id="term_id" name="term_id">
                    <? foreach ($branches_course_terms as $branch_id => $branches_course_term) { ?>
                        <? foreach ($branches_course_term as $term) { ?>
                            <option value="<?= $term['term_id'] ?>"><?= $term['title'] . " - " . $branches[$branch_id]['title'] . ' - ' . $term['price'] . ' ' . $currencies[$term['currency_id'] - 1]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="classroom_id">کلاس</label>
                <select id="classroom_id" name="classroom_id">
                    <option value="0"></option>
                    <? foreach ($branches_classrooms as $branch_id => $branches_classroom) { ?>
                        <? foreach ($branches_classroom as $classroom) { ?>
                            <option value="<?= $classroom['classroom_id'] ?>"><?= $classroom['title'] . " - " . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>


            <div class="form-group">
                <label for="branch_url_id">آدرس اینترنتی</label>
                <select id="branch_url_id" name="branch_url_id">
                    <option value="0"></option>
                    <? foreach ($branches_urls as $branch_id => $branches_url) { ?>
                        <? foreach ($branches_url as $url) { ?>
                            <option value="<?= $url['branch_url_id'] ?>"><?= $url['title'] . " - " . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

<!-- teacher_id -->

            <div>
                <label for="requested_date">تاریخ برگزاری</label>
                <input type="date" id="requested_date" name="requested_date" required>
            </div>

            <div>
                <label for="start_time">زمان شروع</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>

            <div>
                <label for="end_time">زمان پایان</label>
                <input type="time" id="end_time" name="end_time" required>
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
