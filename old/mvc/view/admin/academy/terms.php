<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches_courses = $data['branches_courses'] ?? [];
$courses_terms = $data['courses_terms'] ?? [];
$currencies = $data['currencies'] ?? [];
$branches = $data['branches'] ?? [];
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">افزودن ترم دوره</h1>
            <p>افزودن ترم دوره برای شروع ثبت نام هنرجو</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_5_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_5_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $branch_course['title']?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course['brief']?> &nbsp; / </span>
                        <span> &nbsp; <?= $branch_course['description']?> &nbsp; / </span>
                        <br>
                        <br>

                        <table>
                            <thead>
                                <tr>
                                    <!-- <th>term_id</th> -->
                                    <!-- <th>course_id</th> -->
                                    <th>row</th>
                                    <th>title</th>
                                    <th>start_date</th>
                                    <th>end_date</th>
                                    <th>session_count</th>
                                    <!-- <th>price</th> -->
                                    <!-- <th>currency_id</th> -->
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
                                <? foreach($courses_terms as $course_id => $course_terms) { ?>
                                    <? $row = 0; ?>
                                    <? foreach($course_terms as $course_term) { ?>
                                        <? if($branch_course['course_id'] == $course_id) { ?>
                                            <? $row++; ?>
                                            <tr>
                                                <!-- <td><?//= $course_term['term_id']?></td> -->
                                                <!-- <td><?//= $course_term['course_id']?></td> -->
                                                <td><?= $row ?></td>
                                                <td><?= $course_term['title']?></td>
                                                <td><?= $course_term['start_date']?></td>
                                                <td><?= $course_term['end_date']?></td>
                                                <td><?= $course_term['session_count']?></td>
                                                <!-- <td><?//= $course_term['price']?></td> -->
                                                <!-- <td><?//= $course_term['currency_id']?></td> -->
                                                <td><?= $course_term['status']?></td>
                                                <td><?= $course_term['created_at']?></td>
                                                <td><?= $course_term['created_by']?></td>
                                                <td><?= $course_term['updated_at']?></td>
                                                <td><?= $course_term['updated_by']?></td>
                                                <td><?= $course_term['approved_at']?></td>
                                                <td><?= $course_term['approved_by']?></td>
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



        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
                        
            <div class="form-group">
                <label for="course_id">دوره</label>
                <select id="course_id" name="course_id">
                    <? foreach ($branches_courses as $branch_id => $branch_courses) { ?>
                        <? foreach ($branch_courses as $course) { ?>
                            <option value="<?= $course['course_id'] ?>"><?= $course['title'] . " - " . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>
            
            <div>
                <label for="start_date">تاریخ شروع</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            
            <div>
                <label for="end_date">تاریخ پایان</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            
            <div>
                <label for="session_count">تعداد جلسات</label>
                <input type="number" id="session_count" name="session_count">
            </div>
            
            <div class="form-group">
                <label for="currency_id">نوع پول</label>
                <select id="currency_id" name="currency_id">
                    <? foreach ($currencies as $key => $currency) { ?>
                        <option value="<?= $currency['table_id'] ?>"><?= $currency['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div>
                <label for="price">قیمت</label>
                <input type="number" id="price" name="price" step="0.01">
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
            <button type="submit">ثبت ترم</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>
