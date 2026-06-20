<?
$settings = $data['settings'];
$academy = $data['academy'];
$contact = $data['contact'];
$user = $data['user'];
$comments = $data['comments'];
$posts = $data['posts'];
$categories = $data['categories'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');
$contact_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'contact_table_row_'), 'variable_name');
$settings_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'settings_table_row_'), 'variable_name');
$comments_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'contact_table_row_'), 'variable_name');
$categories_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'settings_table_row_'), 'variable_name');
$user_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'user_table_row_'), 'variable_name');
$academy_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'academy_table_row_'), 'variable_name');
$posts_table_headers_title = setIndexforDataArray(getFilteredList($settings, 'post_table_row_'), 'variable_name');

$settings_table = $data['settings'];
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <!-- صفحه پشتیبان‌گیری (backup.html) -->
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac">پشتیبان‌گیری و بازیابی</h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>



            
            <div class="backup-section">
                <div class="backup-card">
                    <h3>ایجاد پشتیبان جدید</h3>
                    <p>شامل دیتابیس، فایل‌ها و تنظیمات سایت</p>
                    <button class="btn btn-primary">ایجاد پشتیبان کامل</button>
                    <button class="btn btn-outline">پشتیبان فقط دیتابیس</button>
                </div>
                <div class="backup-list">
                    <h3>پشتیبان‌های موجود</h3>
                    <table class="backup-table">
                        <thead>
                            <tr>
                                <th>نام فایل</th>
                                <th>تاریخ</th>
                                <th>حجم</th>
                                <th>نوع</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>backup-1404-11-27-full.zip</td>
                                <td>۱۴۰۴/۱۱/۲۷ - ۱۹:۳۰</td>
                                <td>۴۸۵ MB</td>
                                <td>کامل</td>
                                <td>
                                    <a href="#" class="download">دانلود</a>
                                    <a href="#" class="restore">بازیابی</a>
                                    <a href="#" class="delete">حذف</a>
                                </td>
                            </tr>
                            <!-- ردیف‌های بیشتر -->
                        </tbody>
                    </table>
                </div>
                <!-- <br><?//= showTable($contact, 'Contact', ['contact_id', 'guest_fullname']) ?> -->
                <br><?= showTable($contact, 'Contact', $settings, $contact_table_headers_title) ?>
                <br><?= showTable($settings_table, 'Settings', $settings, $settings_table_headers_title) ?>
                <br><?= showTable($comments, 'Comments', $settings, $comments_table_headers_title) ?>
                <br><?= showTable($categories, 'Categories', $settings, $categories_table_headers_title) ?>
                <br><?= showTable($user, 'Users', $settings, $user_table_headers_title) ?>
                <br><?= showTable($academy, 'Academy', $settings, $academy_table_headers_title) ?>
                <br><?= showTable($posts, 'Posts', $settings, $posts_table_headers_title) ?>
                <!-- <br><?//= showTable($posts, 'Posts', ['content']) ?> -->
            </div>
        </div>
    </div>
</div>
