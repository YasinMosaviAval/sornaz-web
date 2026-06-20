<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
// $addresses = setIndexforDataArray($data['addresses'], 'address_id');
$events = $data['events'] ?? [];
// dump($events);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac"><h1 class="h1_ac">رویداد ها</h1></div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_sidebar_2']['url'] ?>"><?= translate($settings, 'user_panel_sidebar_2') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_5_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_5_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_7_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_7_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_9_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_9_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['user_panel_topbar_10_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_10_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_12_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_12_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_37_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_37_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_13_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_13_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['user_panel_topbar_18_sidebar_1']['url'] ?>"><?= translate($settings, 'user_panel_topbar_18_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>user_event_id</th> -->
                        <!-- <th>user_id</th> -->
                        <th>row</th>
                        <th>title</th>
                        <th>role</th>
                        <th>event_type</th>
                        <!-- <th>address</th> -->
                        <th>event_date</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($events[$branch_id] as $key => $event) { ?>
                        <tr>
                            <!-- <td><?//= $event['user_event_id']?></td> -->
                            <!-- <td><?//= $event['user_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $event['title']?></td>
                            <td><?= $event['text_1']?></td>
                            <td><?= $event['event_type']?></td>
                            <!-- <td><?//= $addresses[$event['address_id']]['text_1'] ?></td> -->
                            <td><?= $event['event_date']?></td>
                            <td><?= $event['created_at']?></td>
                            <td><?= $users[$event['created_by']]['title'] ?></td>
                            <td><?= $event['updated_at'] ?></td>
                            <td><?= $users[$event['updated_by']]['title'] ?></td>
                            <? if($event['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_user_event/<?= $event['user_event_id'] ?>">
                                        <button>تایید رویداد</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $event['approved_at'] ?></td>
                                <td><?= $users[$event['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_event/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="addresses_table_name" value="users" />
            <input type="hidden" name="country_id" value="0" />
            <input type="hidden" name="is_main" value="1" />
            <input type="hidden" name="subject_1" value="role" />
            <input type="hidden" name="subject_2" value="address" />

            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="text_1">نقش</label><input type="text" id="text_1" name="text_1" required></div>

            <div class="form-group">
                <label for="event_type">نوع رویداد</label>
                <select id="event_type" name="event_type">
                    <option value="concert">کنسرت</option>
                    <option value="festival">فستیوال</option>
                    <option value="competition">مسابقه</option>
                    <option value="workshop">کارگاه</option>
                    <option value="other">غیره</option>
                </select>
            </div>

            <div class="form-group">
                <label for="state_id">استان</label>
                    <select id="state_id" name="state_id">
                    <? foreach ($iran_provinces as $key => $iran_province) { ?>
                        <option value="<?= $iran_province['province_id'] ?>"><?= $iran_province['name'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="city_id">شهر</label>
                <select id="city_id" name="city_id">
                    <? foreach ($iran_cities as $key => $iran_city) { ?>
                        <option value="<?= $iran_city['city_id'] ?>"><?= $iran_city['name'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="text_2">آدرس</label><input type="text" id="text_2" name="text_2" required></div>

            <div><label for="latitude">طول جغرافیایی</label><input type="decimal" id="latitude" name="latitude"></div>
            
            <div><label for="longitude">عرض جغرافیایی</label><input type="decimal" id="longitude" name="longitude"></div>
            
            <div><label for="postal_code">کد پستی</label><input type="text" id="postal_code" name="postal_code"></div>

            <div><label for="event_date">تاریخ رویداد</label><input type="date" id="event_date" name="event_date" required></div>

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
            <button type="submit">ثبت رویداد</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



