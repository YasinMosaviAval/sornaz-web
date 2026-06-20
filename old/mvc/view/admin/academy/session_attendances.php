<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$session = $data['session'][0] ?? [];
$members = $data['members'] ?? [];
$attendances = $data['attendances'] ?? [];
// dump($session);
// dump($members);
// dump($attendances);
// exit();
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">حضور و غیاب</h1>
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
                    <!-- <th>session_attendance_id</th> -->
                    <th>row</th>
                    <th>title</th>
                    <th>session_id</th>
                    <!-- <th>term_enrollment_id</th> -->
                    <th>member_id</th>
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
                <? foreach($attendances as $key => $attendance) { ?>
                    <tr>
                        <!-- <td><?//= $attendance['session_attendance_id']?></td> -->
                        <td><?= $key + 1 ?></td>
                        <td><?= $attendance['title']?></td>
                        <td><?= $attendance['session_id']?></td>
                        <!-- <td><?//= $attendance['term_enrollment_id']?></td> -->
                        <td><?= $attendance['member_id']?></td>
                        <td><?= $attendance['status']?></td>
                        <td><?= $attendance['created_at']?></td>
                        <td><?= $attendance['created_by']?></td>
                        <td><?= $attendance['updated_at']?></td>
                        <td><?= $attendance['updated_by']?></td>
                        <td><?= $attendance['approved_at']?></td>
                        <td><?= $attendance['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_session_attendance/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="user_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="session_id" value="<?= $session['term_session_id'] ?>" />

            <? foreach($members as $member) { ?>
                <? $checked = false; ?>
                <? $counter = 0; ?>
                <div class="radio-group">
                
                <? foreach($attendances as $attendance) { ?>
                    <? if($attendance['member_id'] == $member['member_id'] ) { ?>
                        <? $checked = true; ?>
                    <? } ?>
                <? } ?>
                <? if($checked) { ?>
                    <? continue; ?>
                <? } ?>
                    <hr>
                    <br>
                    <label>
                        <input type="hidden" name="term_enrollment_id_<?= $member['term_enrollment_id'] ?>" value="<?= $member['term_enrollment_id'] ?>">
                        <input type="hidden" name="member_id_<?= $member['term_enrollment_id'] ?>" value="<?= $member['member_id'] ?>">
                        <span><?= $member['title'] ?></span>
                        <select name="status_<?= $member['term_enrollment_id'] ?>" id="status_<?= $member['term_enrollment_id'] ?>">
                            <option value="present">حاضر</option>
                            <option value="absent">غایب</option>
                            <option value="late">دارای تاخیر</option>
                        </select>
                        <div>
                            <label for="title_<?= $member['term_enrollment_id'] ?>">عنوان</label>
                            <input type="text" id="title_<?= $member['term_enrollment_id'] ?>" name="title_<?= $member['term_enrollment_id'] ?>">
                        </div>
                        <div>
                            <label for="brief_<?= $member['term_enrollment_id'] ?>">توضیح خلاصه</label>
                            <input type="text" id="brief_<?= $member['term_enrollment_id'] ?>" name="brief_<?= $member['term_enrollment_id'] ?>">
                        </div>
                        <div>
                            <label for="description_<?= $member['term_enrollment_id'] ?>">توضیح کامل</label>
                            <textarea id="description_<?= $member['term_enrollment_id'] ?>" name="description_<?= $member['term_enrollment_id'] ?>" rows="3"></textarea>
                        </div>
                    </label>
                    <br>
                </div>
                <? $counter++ ?>
                <? } ?>
            
            <? if($counter) { ?>
                <button type="submit">ثبت رأی</button>
                <button type="reset" class="btn-outline">انصراف</button>
            <? } else { ?>
                <?= "حضور و غیاب تمامی اعضا ثبت شده است،" ?>
                <br>
                <?= "اگر نیاز به ویرایش دارید از گزینه ویرایش سطرهای جدول فوق استفاده کنید!" ?>
            <? } ?>
        </form>



<!-- 
academy_branch_course_term_session_attendances

    session_attendance_id
    session_id
    academy_member_id
    status 	            (present,absent,late)

-->



    </div>
</div>
