<style>
    .admin-content {
        padding: 2rem;
    }
    .topbar {
        background: white;
        padding: 1.5rem 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        border-radius: 12px;
    }
    .page-title {
        font-size: 2.6rem;
        color: var(--primary);
        margin: 0;
    }

    /* فرم افزودن مدرس جدید */
    .form-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 3rem;
        margin-bottom: 3.5rem;
    }
    .form-title {
        font-size: 2.4rem;
        color: #1a3c6d;
        margin-bottom: 2.2rem;
        text-align: center;
    }
    .form-row {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    .form-group {
        flex: 1;
        min-width: 280px;
    }
    .form-group.full { flex: 100%; }
    .form-group label {
        display: block;
        margin-bottom: 0.8rem;
        font-size: 1.5rem;
        color: #444;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 1.2rem;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1.55rem;
    }
    .form-group textarea { min-height: 120px; resize: vertical; }

    /* جدول مدرسین */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 1.4rem 1.8rem;
        text-align: right;
        border-bottom: 1px solid #eee;
        font-size: 1.55rem;
    }
    th {
        background: var(--primary);
        color: white;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
    }
    th:hover {
        background: #0055aa;
    }
    tr:hover {
        background: #f0f7ff;
    }
    .teacher-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
    }
    .actions a {
        margin-left: 1rem;
        padding: 0.6rem 1.1rem;
        border-radius: 6px;
        font-size: 1.4rem;
        text-decoration: none;
    }
    .edit-btn { color: var(--primary); }
    .delete-btn { color: #d32f2f; }
    .edit-btn:hover, .delete-btn:hover { background: #f0f0f0; }

    .sort-icon {
        margin-right: 8px;
        font-size: 1.2rem;
    }
</style>
<?

$user_array = $data['user'][0];
$lessons = $data['lessons'];
$academies = $data['academies'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');
$daily_hours = setIndexforDataArray(getFilteredList($settings, 'daily_hour_'), 'variable_name');
$weekly_days = setIndexforDataArray(getFilteredList($settings, 'weekly_day_'), 'variable_name');
$genders = setIndexforDataArray(getFilteredList($settings, 'gender_'), 'variable_name');
$student_levels = setIndexforDataArray(getFilteredList($settings, 'student_level_'), 'variable_name');
$academy_roles = setIndexforDataArray(getFilteredList($settings, 'academy_role_'), 'variable_name');
$instruments = setIndexforDataArray(getFilteredList($settings, 'instruments_'), 'variable_name');
$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'user_table_row_'), 'variable_name');
$user_activities = setIndexforDataArray(getFilteredList($settings, 'user_activity_'), 'variable_name');

$time_sheet = explode(',', $user_array['time_sheet']);
// dump($user_activities);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="topbar">
                <h1 class="page-title">ویرایش کاربر</h1>
            </div>
            <form action="<?=baseUrl()?>/admin/edit_user_from_admin_panel/<?= $user_array['user_id'] ?>" method="post" enctype="multipart/form-data">
                <div>
                    <label><?= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
                    <input type="email" name="email" value="<?= $user_array['email'] ?>"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_username') ?> <span class="required">*</span></label>
                    <input type="text" name="username" value="<?= $user_array['username'] ?>"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_fullname_fa') ?> <span class="required">*</span></label>
                    <input type="text" name="fullname_fa" value="<?= $user_array['fullname_fa'] ?>"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_fullname_en') ?> <span class="required">*</span></label>
                    <input type="text" name="fullname_en" value="<?= $user_array['fullname_en'] ?>"/>
                </div>

                <div>
                    <label>parent_name_fa <span class="required">*</span></label>
                    <input type="text" name="parent_name_fa" value="<?= $user_array['parent_name_fa'] ?>"/>
                </div>
                <div>
                    <label>parent_name_en <span class="required">*</span></label>
                    <input type="text" name="parent_name_en" value="<?= $user_array['parent_name_en'] ?>"/>
                </div>
                <div>
                    <label>national_code <span class="required">*</span></label>
                    <input type="text" name="national_code" value="<?= $user_array['national_code'] ?>"/>
                </div>

                <div>
                    <label>birthday</label>
                    <input type="date" name="birthday" placeholder="۱۲ سال" value="<?= $user_array['birthday'] ?>">
                </div>
                <div>
                    <label>academy_register_date</label>
                    <input type="date" name="academy_register_date" placeholder="۱۲ سال" value="<?= $user_array['academy_register_date'] ?>">
                </div>
                <div>
                    <label>سابقه تدریس (سال)</label>
                    <input type="date" name="start_career_date" placeholder="۱۲ سال" value="<?= $user_array['start_career_date'] ?>">
                </div>

                <div>
                    <label>شماره تماس</label>
                    <input type="tel" name="mobile" placeholder="۰۹۱۲ XXX XXXX" value="<?= $user_array['mobile'] ?>">
                </div>
                <div>
                    <label>شماره تماس پدر</label>
                    <input type="tel" name="parent_phone" placeholder="۰۹۱۲ XXX XXXX" value="<?= $user_array['parent_phone'] ?>">
                </div>

                <div>
                    <label>تخصص درس <span>*</span></label>
                    <select name="lessons_id" required>
                        <? foreach($lessons as $key => $lesson) { ?>
                            <? if(strhas($user_array['lessons_id'], $lesson['lesson_id'])) { ?>
                                <option value="<?= $key+1 ?>"><?= translateStrings($lesson, 'name')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($lessons as $key => $lesson) { ?>
                            <? if(!strhas($user_array['lessons_id'], $lesson['lesson_id'])) { ?>
                                <option value="<?= $key+1 ?>"><?= translateStrings($lesson, 'name')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>ساز تخصصی <span>*</span></label>
                    <select name="instruments_id" required>
                        <? foreach($instruments as $key => $instrument) { ?>
                            <? if(strhas($user_array['instruments_id'], $instrument['setting_id'])) { ?>
                                <option value="<?= $instrument['setting_id'] ?>"><?= translateStrings($instrument, 'text')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($instruments as $key => $instrument) { ?>
                            <? if(!strhas($user_array['instruments_id'], $instrument['setting_id'])) { ?>
                                <option value="<?= $instrument['setting_id'] ?>"><?= translateStrings($instrument, 'text')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>نقش<span>*</span></label>
                    <select name="role" required>
                        <? foreach($academy_roles as $key => $academy_role) { ?>
                            <? if(strhas($user_array['role'], $academy_role['value'])) { ?>
                                <option value="<?= $academy_role['value'] . '-' . substr($academy_role['value'], 0, strlen($academy_role['value']) - 1) ?>"><?= translateStrings($academy_role, 'text')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($academy_roles as $key => $academy_role) { ?>
                            <? if(!strhas($user_array['role'], $academy_role['value'])) { ?>
                                <option value="<?= $academy_role['value'] . '-' . substr($academy_role['value'], 0, strlen($academy_role['value']) - 1) ?>"><?= translateStrings($academy_role, 'text')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>gender<span>*</span></label>
                    <select name="gender" required>
                        <? foreach($genders as $key => $gender) { ?>
                            <? if($gender['value'] == $user_array['gender']) { ?>
                                <option value="<?= $gender['value'] ?>"><?= translateStrings($gender, 'text')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($genders as $key => $gender) { ?>
                            <? if($gender['value'] != $user_array['gender']) { ?>
                                <option value="<?= $gender['value'] ?>"><?= translateStrings($gender, 'text')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>student_levels<span>*</span></label>
                    <select name="student_level" required>
                        <? foreach($student_levels as $key => $student_level) { ?>
                            <? if($student_level['value'] == $user_array['student_level']) { ?>
                                <option value="<?= $student_level['value'] ?>" ><?= translateStrings($student_level, 'text')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($student_levels as $key => $student_level) { ?>
                            <? if($student_level['value'] != $user_array['student_level']) { ?>
                                <option value="<?= $student_level['value'] ?>" ><?= translateStrings($student_level, 'text')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>زمان های در دسترسی</label>
                    <? foreach($weekly_days as $key => $day) { ?>
                        <div>
                            <?= translateStrings($day, 'text') ?>
                            <? foreach($daily_hours as $key => $time) { ?>
                                <span><?= translateStrings($time, 'text') ?></span>
                                <? if(in_array($day['value'] . '-' . $time['value'], $time_sheet)){ ?>
                                    <input type="checkbox" name="time_sheet<?= $day['value'] . '-' . $time['value'] ?>" value="<?= $day['value'] . '-' . $time['value'] ?>" checked>
                                <? } else { ?>
                                    <input type="checkbox" name="time_sheet<?= $day['value'] . '-' . $time['value'] ?>" value="<?= $day['value'] . '-' . $time['value'] ?>">
                                <? } ?>
                            <? } ?>
                        </div>
                    <? } ?>
                </div>

                <div>
                    <label>عکس استاد</label>
                    <input type="file" name="picture_type" accept="image/*">
                </div>
                <div>
                    <label>وضعیت</label>
                    <select name="activity_status" >
                        <? foreach($user_activities as $key => $user_activity) { ?>
                            <? if($user_activity['value'] == $user_array['activity_status']) { ?>
                                <option value="<?= $user_activity['value'] ?>"><?= translateStrings($user_activity, 'text')  ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($user_activities as $key => $user_activity) { ?>
                            <? if($user_activity['value'] != $user_array['activity_status']) { ?>
                                <option value="<?= $user_activity['value'] ?>"><?= translateStrings($user_activity, 'text')  ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>
                <div>
                    <label>آموزشگاه</label>
                    <select name="academy_id">
                        <!-- <option value="<?//= $user_array['academy_id'] ?>"></option> -->
                        <? foreach($academies as $key => $academy) { ?>
                            <? if($user_array['academy_id'] == $academy['academy_id']) { ?>
                                <option value="<?= $academy['academy_id'] ?>"><?= $academy['name'] ?></option>
                                <? continue; ?>
                            <? } ?>
                        <? } ?>
                        <? foreach($academies as $key => $academy) { ?>
                            <? if($user_array['academy_id'] != $academy['academy_id']) { ?>
                                <option value="<?= $academy['academy_id'] ?>"><?= $academy['name'] ?></option>
                            <? } ?>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label for="content">biography_fa</label>
                    <textarea id="content" name="biography_fa" rows="6" required><?= $user_array['biography_fa'] ?></textarea>
                </div>
                <div>
                    <label for="content">biography_en</label>
                    <textarea id="content" name="biography_en" rows="6" required><?= $user_array['biography_en'] ?></textarea>
                </div>

                <button type="submit"><?= translate($settings, 'authentication_edit_button') ?></button>
                <!-- <button type="submit">ثبت مدرس جدید</button> -->
            </form>
        </div>
    </div>
</div>



<!--
    academy_id
    access
-->