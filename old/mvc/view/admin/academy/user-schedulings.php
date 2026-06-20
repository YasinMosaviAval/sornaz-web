<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
// $branches = $data['branches'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$user_availabilities = $data['user_availabilities'] ?? [];
// dump($user_availabilities);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">زمانبندی کاربران</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        
        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_15']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_15') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_15']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_15') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>


            <br>
            <table>
                <thead>
                    <tr>
                        <!-- <th>user_availability_id</th> -->
                        <th>row</th>
                        <th>user_id</th>
                        <th>title</th>
                        <th>date</th>
                        <th>day_of_week</th>
                        <th>start_time</th>
                        <th>end_time</th>
                        <th>timezone</th>
                        <th>type</th>
                        <th>is_repeating</th>
                        <th>repeat_period</th>
                        <th>is_closed</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($user_availabilities as $key => $user_availability) { ?>
                        <tr>
                            <!-- <td><?//= $user_availability['user_availability_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $user_availability['user_id']?></td>
                            <td><?= $user_availability['title']?></td>
                            <td><?= $user_availability['date']?></td>
                            <td><?= $user_availability['day_of_week']?></td>
                            <td><?= $user_availability['start_time']?></td>
                            <td><?= $user_availability['end_time']?></td>
                            <td><?= $user_availability['timezone']?></td>
                            <td><?= $user_availability['type']?></td>
                            <td><?= $user_availability['is_repeating']?></td>
                            <td><?= $user_availability['repeat_period']?></td>
                            <td><?= $user_availability['is_closed']?></td>
                            <td><?= $user_availability['created_at']?></td>
                            <td><?= $user_availability['created_by']?></td>
                            <td><?= $user_availability['updated_at']?></td>
                            <td><?= $user_availability['updated_by']?></td>
                            <td><?= $user_availability['approved_at']?></td>
                            <td><?= $user_availability['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>


                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>



        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_availability/">
            <!-- <input type="hidden" name="user_id" value="<?//= session_get('user_id') ?>" /> -->
            <input type="hidden" name="created_by" value="<?= session_get('user_id') ?>" />

            <div class="form-group">
                <label for="member_user_id">اعضا</label>
                <select id="member_user_id" name="member_user_id">
                    <? foreach ($branches_members as $branch_id => $branch_members) { ?>
                        <? foreach ($branch_members as $member) { ?>
                            <option value="<?= $member['user_id'] ?>"><?= $member['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="day_of_week">روزهای هفته</label>
                <select id="day_of_week" name="day_of_week">
                    <option value="saturday">شنبه</option>
                    <option value="sunday">یکشنبه</option>
                    <option value="monday">دوشنبه</option>
                    <option value="tuesday">سه شنبه</option>
                    <option value="wednesday">چهارشنبه</option>
                    <option value="thursday">پنجشنبه</option>
                    <option value="friday">جمعه</option>
                </select>
            </div>

            <div class="form-group">
                <label for="repeat_period">دوره تکرار</label>
                <select id="repeat_period" name="repeat_period">
                    <option value="week">هفتگی</option>
                    <option value="2-week">دو هفته</option>
                    <option value="3-week">سه هفته</option>
                    <option value="4-week">چهار هفته</option>
                    <option value="month">ماهانه</option>
                    <option value="year">سالانه</option>
                </select>
            </div>

            <div>
                <label for="date">تاریخ</label>
                <input type="date" id="date" name="date">
            </div>

            <div class="form-group">
                <label for="timezone">موقعیت زمانی</label>
                <select id="timezone" name="timezone">
                    <option value="IRAN/TEHRAN">ایران-تهران</option>
                </select>
            </div>

            <div>
                <label for="start_time">ساعت شروع</label>
                <input type="time" id="start_time" name="start_time">
            </div>

            <div>
                <label for="end_time">ساعت پایان</label>
                <input type="time" id="end_time" name="end_time">
            </div>

            <div class="form-group">
                <label for="type">حضور</label>
                <select id="type" name="type">
                    <option value="available">در دسترس</option>
                    <option value="unavailable">خارج از دسترس</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_repeating">آیا تکرار می شود؟</label>
                <input type="checkbox" id="is_repeating" name="is_repeating">
            </div>

            <!--
            <div class="form-group">
                <label for="is_closed">آیا شعبه اصلی است؟</label>
                <input type="checkbox" id="is_closed" name="is_closed">
            </div>
            در زمان ویرایش زمانبندی که از دسترس آموزشگاه خارج شده است
            -->

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
