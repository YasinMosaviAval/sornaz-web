<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
// $branches = $data['branches'] ?? [];
$poll = $data['poll'] ?? [];
// dump($poll);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">نظرسنجی ها</h1>
            <p>نظرسنجی هایی که فعال هستند با رنگ سبز در جدول مشخص شده اند!</p>
        </div>

        <br>

        <form method="POST" action="<?=baseUrl()?>/admin/edit_user_poll/<?= $poll['user_poll_id'] ?>">
            <input type="hidden" name="target_type" value="" />
            <input type="hidden" name="target_id" value="0" />
            <input type="hidden" name="subject_1" value="question" />
            
            <div class="form-group">
                <label for="owner_id">شعبه</label>
                <select id="owner_id" name="owner_id">
                    <option value="<?= $poll['owner_id'] ?>"><?= $users[$poll['owner_id']]['title'] ?></option>
                </select>
            </div>
            
            <div><label for="text_1">سوال</label><textarea id="text_1" name="text_1" rows="3" required><?= $poll['text_1'] ?></textarea></div>

            
            <div class="form-group">
                <label for="status">وضعیت</label>
                <select id="status" name="status">
                    <option value="active" <?= $poll['status'] === 'active' ? 'selected' : '' ?>>فعال</option>
                    <option value="deactive" <?= $poll['status'] === 'deactive' ? 'selected' : '' ?>>غیرفعال</option>
                    <option value="closed" <?= $poll['status'] === 'closed' ? 'selected' : '' ?>>بسته</option>
                </select>
            </div>


            <div class="form-group">
                <label for="type">شهر</label>
                <select id="type" name="type">
                    <option value="single" <?= $poll['type'] === 'single' ? 'selected' : '' ?>>تکی</option>
                    <option value="multiple" <?= $poll['type'] === 'multiple' ? 'selected' : '' ?>>چندگانه</option>
                </select>
            </div>

            <div><label for="expires_at">تاریخ پایان</label><input type="datetime-local" id="expires_at" name="expires_at" value="<?= $poll['expires_at'] ?>"></div>

            <div class="form-group">
                <label for="is_anonymous">آیا ناشناس باشد؟</label>
                <input type="checkbox" id="is_anonymous" name="is_anonymous" <?= $poll['is_anonymous'] == 1 ? 'checked' : '' ?>>
            </div>

            <div>
                <label for="title">عنوان</label>
                <input type="text" id="title" name="title" value="<?= $poll['title'] ?>">
            </div>
            <div>
                <label for="brief">توضیح خلاصه</label>
                <input type="text" id="brief" name="brief" value="<?= $poll['brief'] ?>">
            </div>
            <div>
                <label for="description">توضیح کامل</label>
                <textarea id="description" name="description" rows="3"><?= $poll['description'] ?></textarea>
            </div>

            <br>
            <button type="submit">ویرایش نظرسنجی</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



