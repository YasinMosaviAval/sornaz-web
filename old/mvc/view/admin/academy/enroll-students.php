<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches_course_terms = $data['branches_course_terms'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$terms_enrollments = $data['terms_enrollments'] ?? [];
$currencies = $data['currencies'] ?? [];
$branches = $data['branches'] ?? [];
$discounts = $data['discounts'] ?? [];
// dump($discounts);
// exit();
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
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_10']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_10') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                                    <!-- <th>term_enrollment_id</th> -->
                                    <!-- <th>term_id</th> -->
                                    <th>row</th>
                                    <th>member_id</th>
                                    <th>title</th>
                                    <!-- <th>type</th> -->
                                    <th>status</th>
                                    <th>joined_at</th>
                                    <th>created_at</th>
                                    <th>created_by</th>
                                    <th>updated_at</th>
                                    <th>updated_by</th>
                                    <th>approved_at</th>
                                    <th>approved_by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach($terms_enrollments as $term_id => $term_enrollments) { ?>
                                    <? $row = 0; ?>
                                    <? foreach($term_enrollments as $term_enrollment) { ?>
                                        <? if($branch_course_term['term_id'] == $term_id) { ?>
                                            <? $row++; ?>
                                            <tr>
                                                <!-- <td><?//= $term_enrollment['term_enrollment_id']?></td> -->
                                                <!-- <td><?//= $term_enrollment['term_id']?></td> -->
                                                <td><?= $row ?></td>
                                                <td><?= $term_enrollment['member_id']?></td>
                                                <td><?= $term_enrollment['title']?></td>
                                                <!-- <td><?//= $term_enrollment['type']?></td> -->
                                                <td><?= $term_enrollment['status']?></td>
                                                <td><?= $term_enrollment['joined_at']?></td>
                                                <td><?= $term_enrollment['created_at']?></td>
                                                <td><?= $term_enrollment['created_by']?></td>
                                                <td><?= $term_enrollment['updated_at']?></td>
                                                <td><?= $term_enrollment['updated_by']?></td>
                                                <td><?= $term_enrollment['approved_at']?></td>
                                                <td><?= $term_enrollment['approved_by']?></td>
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

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_enrollment/">
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
                <label for="member_id">هنرجو</label>
                <select id="member_id" name="member_id">
                    <? foreach ($branches_members as $branch_id => $branch_members) { ?>
                        <? foreach ($branch_members as $member) { ?>
                            <option value="<?= $member['member_id'] ?>"><?= $member['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <!-- <span>نمایش هزینه ترم: <?//= $price . " " . $currencies[$term_currency_id - 1]['title']?></span> -->

            <div class="form-group">
                <label for="currency_id">نوع پول</label>
                <select id="currency_id" name="currency_id">
                    <? foreach ($currencies as $key => $currency) { ?>
                        <option value="<?= $currency['table_id'] ?>"><?= $currency['title'] ?></option>
                    <? } ?>
                </select>
            </div>


            <div class="form-group">
                <label for="discount_id">نوع تخفیف</label>
                <select id="discount_id" name="discount_id">
                    <? foreach ($discounts as $key => $discount) { ?>
                        <option value="<?= $discount['table_id'] ?>"><?= $discount['value'] . ' - ' . $discount['title']  . ' - ' . $discount['brief'] . ' (' . ($discount['max_usage'] - $discount['used_count']) . ' remaining)' ?></option>
                    <? } ?>
                </select>
            </div>


            <div>
                <label for="payable_amount">قیمت قابل پرداخت</label>
                <input type="number" id="payable_amount" name="payable_amount" step="0.01">
            </div>
            <span>نمایش و محاسبه آنلاین هزینه ترم با تخفیف</span>

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
                <label for="joined_at">تاریخ ثبت نام</label>
                <input type="date" id="joined_at" name="joined_at" required>
            </div>

            <div>
                <label for="issued_at">تاریخ صدور فاکتور</label>
                <input type="date" id="issued_at" name="issued_at" required>
            </div>

            <div>
                <label for="due_date">تاریخ سررسید - مهلت پرداخت</label>
                <input type="date" id="due_date" name="due_date" required>
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

