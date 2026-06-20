<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branch_types = $data['branch_types'] ?? [];
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">نوع شعبه ها</h1>
            <p>شاید یک آموزشگاه شعبه هایی متفاوت داشته باشد مثلا یک شعبه موسیقی باشد و شعبه دیگر ادبیات</p>
        </div>


        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

<br>


                <table>
                    <thead>
                        <tr>
                            <th>row</th>
                            <th>type</th>
                            <th>title</th>
                            <!-- <th>brief</th> -->
                            <!-- <th>description</th> -->
                            <th>created_at</th>
                            <th>created_by</th>
                            <th>updated_at</th>
                            <th>updated_by</th>
                            <th>approved_at</th>
                            <th>approved_by</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach($branch_types as $key => $branch_type) { ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $branch_type['type'] ?></td>
                                <td><?= $branch_type['title'] ?></td>
                                <!-- <td><?//= $branch_type['brief'] ?></td> -->
                                <!-- <td><?//= $branch_type['description'] ?></td> -->
                                <td><?= $branch_type['created_at'] ?></td>
                                <td><?= $branch_type['created_by'] ?></td>
                                <td><?= $branch_type['updated_at'] ?></td>
                                <td><?= $branch_type['updated_by'] ?></td>
                                <td><?= $branch_type['approved_at'] ?></td>
                                <td><?= $branch_type['approved_by'] ?></td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>

    <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_type/" enctype="multipart/form-data" id="addCategoryForm">
        <input type="hidden" name="manager_id" id="manager_id" value="<?= session_get('user_id') ?>" />

        <div class="form-group">
            <label for="type">نوع شعبه</label>
            <select id="type" name="type">
                <option value="music">موسیقی</option>
                <option value="poetry">شعر</option>
                <option value="painting">نقاشی</option>
                <option value="other">غیره</option>
                <option value="hybrid">مشترک</option>
            </select>
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

        <!-- <button type="submit"><?//= translate($settings, 'add_new_academy_cta_button') ?></button> -->
        <button type="submit">ثبت شعبه</button>
        <button type="reset" class="btn-outline"><?= translate($settings, 'add_new_academy_discard_button') ?></button>
    </form>
    </div>
</div>



