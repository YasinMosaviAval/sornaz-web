<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$classroom_types = $data['classroom_types'] ?? [];
$classrooms = $data['classrooms'] ?? [];
// dump($classrooms);
// exit();
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">افزودن کلاس/اتاق</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        
        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
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
                        <!-- <th>classroom_id</th> -->
                        <!-- <th>branch_id</th> -->
                        <th>row</th>
                        <th>type_id</th>
                        <th>title</th>
                        <!-- <th>brief</th> -->
                        <!-- <th>description</th> -->
                        <th>capacity</th>
                        <th>is_active</th>
                        <th>status</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($classrooms[$branch_id] as $key => $classroom) { ?>
                        <tr>
                            <!-- <td><?//= $classroom['classroom_id']?></td> -->
                            <!-- <td><?//= $classroom['branch_id']?></td> -->
                            <td><?=  $key + 1 ?></td>
                            <td><?= $classroom['type_id']?></td>
                            <td><?= $classroom['title']?></td>
                            <!-- <td><?//= $classroom['brief']?></td> -->
                            <!-- <td><?//= $classroom['description']?></td> -->
                            <td><?= $classroom['capacity']?></td>
                            <td><?= $classroom['is_active']?></td>
                            <td><?= $classroom['status']?></td>
                            <td><?= $classroom['created_at']?></td>
                            <td><?= $classroom['created_by']?></td>
                            <td><?= $classroom['updated_at']?></td>
                            <td><?= $classroom['updated_by']?></td>
                            <td><?= $classroom['approved_at']?></td>
                            <td><?= $classroom['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_classroom/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            
            <div class="form-group">
                <label for="branch_id">شعبه</label>
                <select id="branch_id" name="branch_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['table_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>


            <div class="form-group">
                <label for="type_id">نوع کلاس</label>
                <select id="type_id" name="type_id">
                    <? foreach ($classroom_types as $branch_id => $classroom_type) { ?>
                        <? foreach ($classroom_type as $type) { ?>
                            <option value="<?= $type['classroom_type_id'] ?>"><?= $type['title'] . ' - ' . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>
            
            <div><label for="capacity">ظرفیت</label><input type="number" id="capacity" name="capacity"></div>
            
            <div class="form-group">
                <label for="is_active">آیا کلاس فعال است؟</label>
                <input type="checkbox" id="is_active" name="is_active">
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
            <button type="submit">ثبت کلاس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>