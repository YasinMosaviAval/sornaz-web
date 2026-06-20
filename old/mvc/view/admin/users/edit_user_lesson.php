<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$levels = setIndexforDataArray($data['levels'], 'level_id');
$lesson = $data['lesson'] ?? [];
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">درس ها</h1>
            <p>درس های اصلی با رنگ سبز در جدول مشخص شده اند!</p>
        </div>

        <br>

        <form method="POST" action="<?=baseUrl()?>/admin/edit_user_lesson/<?= $lesson['user_lesson_id'] ?>">
            <input type="hidden" name="created_at" value="<?= $lesson['created_at'] ?>" />
            <input type="hidden" name="created_by" value="<?= $lesson['created_by'] ?>" />
            
            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <option value="<?= $lesson['user_id'] ?>"><?= $users[$lesson['user_id']]['title'] ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="level_id">سطح</label>
                    <select id="level_id" name="level_id">
                    <? foreach ($levels as $key => $level) { ?>
                        <option value="<?= $level['level_id'] ?>" <?= $lesson['level_id'] === $level['level_id'] ? 'selected' : '' ?>><?= $level['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div><label for="years_of_experience">سال‌های تجربه</label><input type="number" id="years_of_experience" name="years_of_experience" value="<?= $lesson['years_of_experience'] ?>" required></div>
            
            <div class="form-group">
                <label for="is_primary">آیا درس اصلی است؟</label>
                <input type="checkbox" id="is_primary" name="is_primary" <?= $lesson['is_primary'] == 1 ? 'checked' : '' ?>>
            </div>

            <div>
                <label for="title">عنوان</label>
                <input type="text" id="title" name="title" value="<?= $lesson['title'] ?>">
            </div>
            <div>
                <label for="brief">توضیح خلاصه</label>
                <input type="text" id="brief" name="brief" value="<?= $lesson['brief'] ?>">
            </div>
            <div>
                <label for="description">توضیح کامل</label>
                <textarea id="description" name="description" rows="3"><?= $lesson['description'] ?></textarea>
            </div>

            <br>
            <button type="submit">ثبت درس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



