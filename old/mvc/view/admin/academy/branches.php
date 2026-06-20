<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$branch_types = $data['branch_types'] ?? [];

// dump($branches);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">شعبه ها</h1>
            <p>ساختن شعبه جدید فقط با اکانت اصلی آموزشگاه امکان پذیر است</p>
            <p>تا در یوزر مربوط به شعبه بخش created_by آی دی یوزر آموزشگاه ثبت شود</p>
            <p>برای این کار یک پرمیشن create_new_branch تعریف شود که فقط به یوزر اصلی آموزشگاه اختصاص داده شود</p>
        </div>


        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_1']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_1') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

<br>

                <table>
                    <thead>
                        <tr>
                            <!-- <th>user_id</th> -->
                            <!-- <th>branch_id</th> -->
                            <th>row</th>
                            <!-- <th>academy_branch_type_id</th> -->
                            <!-- <th>email</th> -->
                            <!-- <th>username</th> -->
                            <th>title</th>
                            <!-- <th>brief</th> -->
                            <!-- <th>description</th> -->
                            <!-- <th>password</th> -->
                            <!-- <th>phone</th> -->
                            <!-- <th>national_code</th> -->
                            <!-- <th>gender</th> -->
                            <th>status</th>
                            <th>visibility</th>
                            <!-- <th>birthday</th> -->
                            <!-- <th>register_time</th> -->
                            <!-- <th>last_visit_time</th> -->
                            <th>created_at</th>
                            <th>created_by</th>
                            <th>updated_at</th>
                            <th>updated_by</th>
                            <th>approved_at</th>
                            <th>approved_by</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? $row = 0; ?>
                        <? foreach($branches as $branch_id => $branch) { ?>
                            <? $row++; ?>
                            <tr>
                                <!-- <td><?//= $branch_id ?></td> -->
                                <!-- <td><?//= $branch['user_id']?></td> -->
                                <td><?= $row ?></td>
                                <!-- <td><?//= $branch['academy_branch_type_id'] ?></td> -->
                                <!-- <td><?//= $branch['email']?></td> -->
                                <!-- <td><?//= $branch['username']?></td> -->
                                <td><?= $branch['title']?></td>
                                <!-- <td><?//= $branch['brief']?></td> -->
                                <!-- <td><?//= $branch['description']?></td> -->
                                <!-- <td><?//= $branch['password']?></td> -->
                                <!-- <td><?//= $branch['phone']?></td> -->
                                <!-- <td><?//= $branch['national_code']?></td> -->
                                <!-- <td><?//= $branch['gender']?></td> -->
                                <td><?= $branch['status']?></td>
                                <td><?= $branch['visibility']?></td>
                                <!-- <td><?//= $branch['birthday']?></td> -->
                                <!-- <td><?//= $branch['register_time']?></td> -->
                                <!-- <td><?//= $branch['last_visit_time']?></td> -->
                                <td><?= $branch['created_at']?></td>
                                <td><?= $branch['created_by']?></td>
                                <td><?= $branch['updated_at']?></td>
                                <td><?= $branch['updated_by']?></td>
                                <td><?= $branch['approved_at']?></td>
                                <td><?= $branch['approved_by']?></td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>

    <form method="POST" action="<?=baseUrl()?>/admin/add_new_branch/" enctype="multipart/form-data" id="addCategoryForm">
        <input type="hidden" name="manager_id" id="manager_id" value="<?= session_get('user_id') ?>" />

		<div>
			<label for="email"><?= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
			<input type="email" id="email" name="email"/>
		</div>
		<div>
			<label for="username"><?= translate($settings, 'authentication_username') ?> <span class="required">*</span></label>
			<input type="text" id="username" name="username"/>
		</div>
		<div>
			<label for="password1"><?= translate($settings, 'authentication_password') ?> <span class="required">*</span></label>
			<input type="password" id="password1" name="password1"/>
		</div>
		<div>
			<label for="password2"><?= translate($settings, 'authentication_confirm_password') ?> <span class="required">*</span></label>
			<input type="password" id="password2" name="password2"/>
		</div>
		<div>
			<label for="fullname">نام شعبه <span class="required">*</span></label>
			<input type="text" id="fullname" name="fullname"/>
		</div>


        <div class="form-group">
            <label for="academy_branch_type_id">نوع آموزش شعبه</label>
            <select id="academy_branch_type_id" name="academy_branch_type_id">
                    <? foreach($branch_types as $branch_type) { ?>
                        <option value="<?= $branch_type['academy_branch_type_id'] ?>"><?= $branch_type['title'] . ' - ' . $branch_type['brief'] ?></option>
                    <? } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="mode">نوع فیزیکی شعبه</label>
            <select id="mode" name="mode">
                <option value="physical">فیزیکی</option>
                <option value="online">آنلاین</option>
                <option value="hybrid">فیزیکی و آنلاین</option>
            </select>
        </div>

        <div class="form-group">
            <label for="timezone">موقعیت زمانی</label>
            <select id="timezone" name="timezone">
                <option value="Asia/Tehran">آسیا/تهران</option>
            </select>
        </div>

        <div class="form-group">
            <label for="is_main">آیا شعبه اصلی است؟</label>
            <input type="checkbox" id="is_main" name="is_main">
        </div>


        <div>
            <label for="brief">توضیح کوتاه</label>
            <input type="text" id="brief" name="brief" placeholder="<?//= $settings['academy_table_row_']['url'] ?>">
        </div>
        <div>
            <label for="biography">بیوگرافی</label>
            <textarea id="biography" name="biography" rows="3" placeholder="<?//= $settings['academy_table_row_']['description_fa'] ?>"></textarea>
        </div>

        <br>

        <!-- <button type="submit"><?//= translate($settings, 'add_new_academy_cta_button') ?></button> -->
        <button type="submit">ثبت شعبه</button>
        <button type="reset" class="btn-outline"><?= translate($settings, 'add_new_academy_discard_button') ?></button>
    </form>
    </div>
</div>

