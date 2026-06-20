<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$session = $data['session'][0] ?? [];
$classrooms = $data['classrooms'] ?? [];
$branches_classrooms = $data['branches_classrooms'] ?? [];

// dump($session);
// dump($classrooms);
// dump($branches_classrooms);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">ثبت کلاس</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

<br>
        <div>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $session['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $session['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $session['description']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $session['classroom_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $session['status']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $session['created_by']?> &nbsp; / </span>
            <span> &nbsp; <?= $session['created_at']?> &nbsp; / </span>
        </div>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <!-- <th>term_session_classroom_id</th> -->
                    <th>row</th>
                    <th>title</th>
                    <th>session_id</th>
                    <th>classroom_id</th>
                    <th>created_at</th>
                    <th>created_by</th>
                    <th>updated_at</th>
                    <th>updated_by</th>
                    <th>approved_at</th>
                    <th>approved_by</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($classrooms as $key => $classroom) { ?>
                    <tr>
                        <!-- <td><?//= $classroom['term_session_classroom_id']?></td> -->
                        <td><?= $key ?></td>
                        <td><?= $classroom['title']?></td>
                        <td><?= $classroom['session_id']?></td>
                        <td><?= $classroom['classroom_id']?></td>
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
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_session_classroom/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="user_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="session_id" id="session_id" value="<?= $session['term_session_id'] ?>" />


            <div class="form-group">
                <label for="classroom_id">کلاس</label>
                <select id="classroom_id" name="classroom_id">
                    <? foreach ($branches_classrooms as $branch_id => $branches_classroom) { ?>
                        <? foreach ($branches_classroom as $classroom) { ?>
                            <option value="<?= $classroom['classroom_id'] ?>"><?= $classroom['title'] . " - " ?></option>
                            <!-- <option value="<?//= $classroom['classroom_id'] ?>"><?//= $classroom['title'] . " - " . $branches[$branch_id]['title'] ?></option> -->
                        <? } ?>
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
