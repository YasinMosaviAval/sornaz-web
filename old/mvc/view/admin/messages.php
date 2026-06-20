<?
$contact_messages = $data['contact_messages'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');;
$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'contact_table_row_'), 'variable_name');
// dump($contact_messages[0]);

?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        
        <div class="header_ac">
            <h1 class="h1_ac">پیام ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="topbar">
            <div class="search-bar">
                <input type="text" placeholder="جستجو در پنل...">
            </div>
            <div class="topbar-right">
                <i class="fas fa-bell notification"></i>
                <div class="profile">
                    <img src="https://via.placeholder.com/40" alt="پروفایل">
                </div>
            </div>
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
                    <? foreach($contact_messages as $key => $message) { ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $message['user_id']?></td>
                            <td><?= $message['receiver_user_id']?></td>
                            <td><?= $message['post_id']?></td>
                            <td><?= $message['author']?></td>
                            <td><?= $message['author_email']?></td>
                            <td><?= $message['date']?></td>
                            <td><?= $message['content']?></td>
                            <td><?= $message['has_response']?></td>
                            <td><?= $message['approved']?></td>
                            <td><?= $message['parent']?></td>
                            <td class="actions">
                                <a href="<?=baseUrl()?>/admin/showMessage/<?= $message['contact_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_preview') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/editMessage/<?= $message['contact_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_edit') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/delete_message/<?= $message['contact_id'] ?>" class="delete-cat"><?= translate($settings, 'tables_action_delete') ?></a>
                            </td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>

        <?//= showTable($contact_messages, '', $settings, $table_headers_title) ?>


    </div>
</div>

