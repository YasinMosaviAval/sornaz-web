<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$branches = $data['branches'] ?? [];
$classrooms = $data['classrooms'] ?? [];
$classroom_assets = $data['classroom_assets'] ?? [];
// dump($branches);
// dump($classrooms);
// dump($classroom_assets);
// exit();
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">افزودن تجهیزات کلاس/اتاق</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_7']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_7') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <? foreach($branches as $branch_id => $branch) { ?>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; <?= $branch['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['description']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['phone']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['national_code']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['birthday']?></span>
            <br>
            <br>

            <? foreach($classrooms as $classroom) { ?>
                <? foreach($classroom as $class) { ?>
                    <? if($class['branch_id'] == $branch_id) { ?>
                        <span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - <?= $class['title']?> &nbsp; / </span>
                        <span> &nbsp; <?= $class['brief']?> &nbsp; / </span>
                        <span> &nbsp; <?= $class['description']?> &nbsp; / </span>
                        <br>
                        <br>

                        <table>
                            <thead>
                                <tr>
                                    <!-- <th>classroom_asset_id</th> -->
                                    <!-- <th>classroom_id</th> -->
                                    <th>row</th>
                                    <th>title</th>
                                    <!-- <th>brief</th> -->
                                    <!-- <th>description</th> -->
                                    <th>quantity</th>
                                    <th>created_at</th>
                                    <th>created_by</th>
                                    <th>updated_at</th>
                                    <th>updated_by</th>
                                    <th>approved_at</th>
                                    <th>approved_by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach($classroom_assets as $classroom_id => $classroom_asset) { ?>
                                    <? $row = 0; ?>
                                    <? foreach($classroom_asset as $asset) { ?>
                                        <? if($class['classroom_id'] == $classroom_id) { ?>
                                            <? $row++; ?>
                                            <tr>
                                                <!-- <td><?//= $asset['classroom_asset_id']?></td> -->
                                                <!-- <td><?//= $asset['classroom_id']?></td> -->
                                                <td><?= $row ?></td>
                                                <td><?= $asset['title']?></td>
                                                <!-- <td><?//= $asset['brief']?></td> -->
                                                <!-- <td><?//= $asset['description']?></td> -->
                                                <td><?= $asset['quantity']?></td>
                                                <td><?= $asset['created_at']?></td>
                                                <td><?= $asset['created_by']?></td>
                                                <td><?= $asset['updated_at']?></td>
                                                <td><?= $asset['updated_by']?></td>
                                                <td><?= $asset['approved_at']?></td>
                                                <td><?= $asset['approved_by']?></td>
                                            </tr>
                                        <? } ?>
                                    <? } ?>
                                <? } ?>
                            </tbody>
                        </table>
                        <br>
                    <? } ?>
                <? } ?>
            <? } ?>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_classroom_asset/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />

            <div class="form-group">
                <label for="classroom_id">کلاس</label>
                <select id="classroom_id" name="classroom_id">
                    <? foreach ($classrooms as $branch_id => $classroom) { ?>
                        <? foreach ($classroom as $class) { ?>
                            <option value="<?= $class['classroom_id'] ?>"><?= $class['title'] . ' - ' . $branches[$branch_id]['title'] ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div>
                <label for="quantity">تعداد</label>
                <input type="number" id="quantity" name="quantity">
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