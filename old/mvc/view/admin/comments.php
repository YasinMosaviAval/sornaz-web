<?


// dump($data['comments']);

$comments = $data['comments'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');

$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'contact_table_row_'), 'variable_name');
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac">نظرات</h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>


            <!-- فیلترهای افقی ثابت (شبیه وردپرس) -->
            <div class="comments-filter-bar">
                <ul class="filter-list">
                    <li class="filter-item active"><a href="#">همه <span class="count">(۱۲۳)</span></a></li>
                    <li class="filter-item"><a href="#">در انتظار تأیید <span class="count">(۱۹)</span></a></li>
                    <li class="filter-item"><a href="#">تأیید شده <span class="count">(۹۸)</span></a></li>
                    <li class="filter-item"><a href="#">اسپم <span class="count">(۴)</span></a></li>
                    <li class="filter-item"><a href="#">زباله <span class="count">(۲)</span></a></li>
                </ul>
            </div>

            
            <br>
            <table>
                <thead>
                    <tr>
                        <th>row</th>
                        <th>user_id</th>
                        <th>receiver_user_id</th>
                        <th>post_id</th>
                        <th>author</th>
                        <th>author_email</th>
                        <th>date</th>
                        <th>content</th>
                        <th>has_response</th>
                        <th>approved</th>
                        <th>parent</th>
                        <th><?= translate($settings, 'tables_action_title') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($comments as $key => $comment) { ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $comment['user_id']?></td>
                            <td><?= $comment['receiver_user_id']?></td>
                            <td><?= $comment['post_id']?></td>
                            <td><?= $comment['author']?></td>
                            <td><?= $comment['author_email']?></td>
                            <td><?= $comment['date']?></td>
                            <td><?= $comment['content']?></td>
                            <td><?= $comment['has_response']?></td>
                            <td><?= $comment['approved']?></td>
                            <td><?= $comment['parent']?></td>
                            <td class="actions">
                                <a href="<?=baseUrl()?>/admin/showComment/<?= $comment['contact_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_preview') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/editComment/<?= $comment['contact_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_edit') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/delete_comment/<?= $comment['contact_id'] ?>" class="delete-cat"><?= translate($settings, 'tables_action_delete') ?></a>
                            </td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>


            <?//= showTable($comments, '', $settings, $table_headers_title) ?>

            <!-- صفحه‌بندی ساده -->
            <!-- <div class="pagination">
                <a href="#" class="prev">قبلی</a>
                <span class="current">۱</span>
                <a href="#" class="page">۲</a>
                <a href="#" class="page">۳</a>
                <a href="#" class="next">بعدی</a>
            </div> -->
        </div>
    </div>

</div>
