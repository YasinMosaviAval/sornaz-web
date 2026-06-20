<?
$settings_array = setIndexforDataArray($data['settings'], 'setting_id');
$settings = setIndexforDataArray($data['settings'], 'variable_name');
// dump($settings);
// exit()
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac"><?= translate($settings, 'settings_page_title') ?></h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="admin-content">
            <h3><?= translate($settings, 'add_new_setting_title') ?></h3>
            <form method="POST" action="<?=baseUrl()?>/admin/add_setting/" enctype="multipart/form-data" id="addSettingForm">
                <div>
                    <label for="page">page <span class="required">*</span></label>
                    <input type="text" id="page" name="page" required placeholder="<?= $settings['settings_table_row_0']['page'] ?>">
                </div>
                <div>
                    <label for="variable_name">variable_name <span class="required">*</span></label>
                    <input type="text" id="variable_name" name="variable_name" required placeholder="<?= $settings['settings_table_row_0']['variable_name'] ?>">
                </div>
                <div>
                    <label for="table_name">table_name <span class="required">*</span></label>
                    <input type="text" id="table_name" name="table_name" required placeholder="<?= $settings['settings_table_row_0']['table_name'] ?>">
                </div>
                <div>
                    <label for="value">value</label>
                    <input type="text" id="value" name="value" placeholder="<?= $settings['settings_table_row_0']['value'] ?>">
                </div>
                <div>
                    <label for="url">url</label>
                    <input type="text" id="url" name="url" placeholder="<?= $settings['settings_table_row_0']['url'] ?>">
                </div>
                <div>
                    <label for="source">source</label>
                    <input type="text" id="source" name="source" placeholder="<?= $settings['settings_table_row_0']['source'] ?>">
                </div>
                <div>
                    <label for="status">status</label>
                    <input type="text" id="status" name="status" placeholder="<?= $settings['settings_table_row_0']['status'] ?>">
                </div>
                <div>
                    <label for="icon">icon</label>
                    <input type="text" id="icon" name="icon" placeholder="<?= $settings['settings_table_row_0']['icon'] ?>">
                    <!-- <small><?//= translate($settings, 'settings_table_row_10', 'title') ?></small> -->
                </div>

                <div>
                    <label for="title_fa">عنوان فارسی <span class="required">*</span></label>
                    <input type="text" id="title_fa" name="title_fa" required placeholder="<?= $settings['settings_table_row_0']['title'] ?>">
                </div>
                <div>
                    <label for="title_en">عنوان انگلیسی <span class="required">*</span></label>
                    <input type="text" id="title_en" name="title_en" required placeholder="<?= $settings['settings_table_row_0']['title'] ?>">
                </div>

                <div>
                    <label for="brief_fa">خلاصه فارسی <span class="required">*</span></label>
                    <input type="text" id="brief_fa" name="brief_fa" required placeholder="<?= $settings['settings_table_row_0']['brief'] ?>">
                </div>
                <div>
                    <label for="brief_en">خلاصه انگلیسی <span class="required">*</span></label>
                    <input type="text" id="brief_en" name="brief_en" required placeholder="<?= $settings['settings_table_row_0']['brief'] ?>">
                </div>

                <div>
                    <label for="description_fa">تشریح فارسی</label>
                    <textarea id="description_fa" name="description_fa" rows="3" placeholder="<?= $settings['settings_table_row_0']['description'] ?>"></textarea>
                </div>
                <div>
                    <label for="description_en">تشریح انگلیسی</label>
                    <textarea id="description_en" name="description_en" rows="3" placeholder="<?= $settings['settings_table_row_0']['description'] ?>"></textarea>
                </div>


                <button type="submit"><?= translate($settings, 'add_new_setting_cta_button') ?></button>
                <!-- <button type="reset" class="btn-outline"><?//= translate($settings, 'add_new_setting_discard_button') ?></button> -->
            </form>

            <br>

            <table>
                <thead>
                    <tr>
                        <th>row</th>
                        <!-- <th>parent_id</th> -->
                        <th>page</th>
                        <th>title</th>
                        <th>variable_name</th>
                        <!-- <th>table_name</th> -->
                        <!-- <th>value</th> -->
                        <!-- <th>url</th> -->
                        <!-- <th>source</th> -->
                        <th>status</th>
                        <th>icon</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($settings_array as $setting_id => $setting) { ?>
                        <tr>
                            <td><?= $setting_id ?></td>
                            <!-- <td><?//= $setting['parent_id']?></td> -->
                            <td><?= $setting['page']?></td>
                            <td><?= $setting['title']?></td>
                            <td><?= $setting['variable_name']?></td>
                            <!-- <td><?//= $setting['table_name']?></td> -->
                            <!-- <td><?//= $setting['value']?></td> -->
                            <!-- <td><?//= $setting['url']?></td> -->
                            <!-- <td><?//= $setting['source']?></td> -->
                            <td><?= $setting['status']?></td>
                            <td><?= $setting['icon']?></td>
                            <td><?= $setting['created_at']?></td>
                            <td><?= $setting['created_by']?></td>
                            <td><?= $setting['updated_at']?></td>
                            <td><?= $setting['updated_by']?></td>
                            <td><?= $setting['approved_at']?></td>
                            <td><?= $setting['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
