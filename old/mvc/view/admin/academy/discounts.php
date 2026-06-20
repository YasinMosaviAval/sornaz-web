<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;

// $branches_course_terms = $data['branches_course_terms'] ?? [];
// $branches_members = $data['branches_members'] ?? [];
// $terms_enrollments = $data['terms_enrollments'] ?? [];
// $currencies = $data['currencies'] ?? [];
// $branches = $data['branches'] ?? [];
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">تخفیف ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_financial_system_discount/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />

            <div class="form-group">
                <label for="discount_type">نوع تخفیف</label>
                <select id="discount_type" name="discount_type">
                    <option value="percentage">درصد</option>
                    <option value="fixed">مقدار ثابت</option>
                </select>
            </div>

            <div>
                <label for="value">مقدار</label>
                <input type="number" id="value" name="value" step="0.01">
            </div>

            <div>
                <label for="start_date">تاریخ شروع تخفیف</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div>
                <label for="end_date">تاریخ پایان تخفیف</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <div>
                <label for="max_usage">حداکثر تعداد استفاده</label>
                <input type="number" id="max_usage" name="max_usage">
            </div>

            <!-- <div>
                <label for="used_count">تعداد استفاده شده</label>
                <input type="number" id="used_count" name="used_count" />
            </div> -->
            <!-- این آیتم باید به صورت آپدیت در هر بار استفاده از تخفیف افزایش پیدا کنه و در صورت رسیدن به مقدار ماکزیمم، تخفیف غیر فعال بشه -->

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
