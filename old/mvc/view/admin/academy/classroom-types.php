<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$classroom_types = $data['classroom_types'] ?? [];

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
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>
        
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
                        <!-- <th>classroom_type_id</th> -->
                        <!-- <th>branch_id</th> -->
                        <th>row</th>
                        <th>code</th>
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
                    <? foreach($classroom_types[$branch_id] as $key => $classroom_type) { ?>
                        <tr>
                            <!-- <td><?//= $classroom_type['classroom_type_id']?></td> -->
                            <!-- <td><?//= $classroom_type['branch_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <td><?= $classroom_type['code']?></td>
                            <td><?= $classroom_type['title']?></td>
                            <!-- <td><?//= $classroom_type['brief']?></td> -->
                            <!-- <td><?//= $classroom_type['description']?></td> -->
                            <td><?= $classroom_type['created_at']?></td>
                            <td><?= $classroom_type['created_by']?></td>
                            <td><?= $classroom_type['updated_at']?></td>
                            <td><?= $classroom_type['updated_by']?></td>
                            <td><?= $classroom_type['approved_at']?></td>
                            <td><?= $classroom_type['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_classroom_type/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            
            <div class="form-group">
                <label for="branch_id">شعبه</label>
                <select id="branch_id" name="branch_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['table_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
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
            <button type="submit">ثبت کلاس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>