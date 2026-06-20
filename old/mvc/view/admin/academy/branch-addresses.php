<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$branches_addresses = $data['branches_addresses'] ?? [];
$iran_cities = $data['iran_cities'] ?? [];
$iran_provinces = $data['iran_provinces'] ?? [];
// dump($branches_addresses);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac"><h1 class="h1_ac">افزودن لینک جلسه (Zoom و غیره)</h1></div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>address_id</th> -->
                        <!-- <th>addresses_table_id</th> -->
                        <!-- <th>addresses_table_name</th> -->
                        <th>row</th>
                        <th>title</th>
                        <!-- <th>brief</th> -->
                        <!-- <th>description</th> -->
                        <th>country_id</th>
                        <th>state_id</th>
                        <th>city_id</th>
                        <th>address</th>
                        <th>is_main</th>
                        <th>latitude</th>
                        <th>longitude</th>
                        <th>postal_code</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($branches_addresses[$branch_id] as $key => $branches_address) { ?>
                        <tr>
                            <!-- <td><?//= $branches_address['address_id']?></td> -->
                            <!-- <td><?//= $branches_address['addresses_table_id']?></td> -->
                            <!-- <td><?//= $branches_address['addresses_table_name']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $branches_address['title']?></td>
                            <!-- <td><?//= $branches_address['brief']?></td> -->
                            <!-- <td><?//= $branches_address['description']?></td> -->
                            <td><?= $branches_address['country_id']?></td>
                            <td><?= $branches_address['state_id']?></td>
                            <td><?= $branches_address['city_id']?></td>
                            <td><?= $branches_address['text_1']?></td>
                            <td><?= $branches_address['is_main']?></td>
                            <td><?= $branches_address['latitude']?></td>
                            <td><?= $branches_address['longitude']?></td>
                            <td><?= $branches_address['postal_code']?></td>
                            <td><?= $branches_address['created_at']?></td>
                            <td><?= $branches_address['created_by']?></td>
                            <td><?= $branches_address['updated_at']?></td>
                            <td><?= $branches_address['updated_by']?></td>
                            <td><?= $branches_address['approved_at']?></td>
                            <td><?= $branches_address['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_user_address/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="addresses_table_name" value="users" />
            <input type="hidden" name="country_id" value="0" />
            <input type="hidden" name="subject_1" value="address" />
            
            <div class="form-group">
                <label for="addresses_table_id">شعبه</label>
                <select id="addresses_table_id" name="addresses_table_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
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

            <div><label for="text_1">آدرس</label><input type="text" id="text_1" name="text_1" required></div>

            <div><label for="latitude">طول جغرافیایی</label><input type="decimal" id="latitude" name="latitude" required></div>
            
            <div><label for="longitude">عرض جغرافیایی</label><input type="decimal" id="longitude" name="longitude" required></div>
            
            <div><label for="postal_code">کد پستی</label><input type="text" id="postal_code" name="postal_code" required></div>

            <div class="form-group">
                <label for="is_main">آیا نشانی اصلی است؟</label>
                <input type="checkbox" id="is_main" name="is_main">
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
                <label for="description">آدرس کامل</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <br>
            <button type="submit">ثبت نشانی</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



