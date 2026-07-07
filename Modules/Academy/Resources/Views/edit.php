<h1>ویرایش آموزشگاه</h1>

<?php
// dd($academy);
?>



<form action="/academy/<?= $academy['id'] ?>" method="post">
    <input type="hidden" name="_method" value="PUT">

    <div>
        <label>نام کاربری</label>
        <input type="text" name="username" value="<?= $academy['username'] ?>">
    </div>
    <div>
        <label>ایمیل</label>
        <input type="email" name="email" value="<?= $academy['email'] ?>">
    </div>
    <div>
        <label>موبایل</label>
        <input type="text" name="phone" value="<?= $academy['phone'] ?>">
    </div>
    <div>
        <label>وضعیت</label>
        <select name="status">
            <option value="approved">فعال</option>
            <option value="pending">غیرفعال</option>
        </select>
    </div>
    <div>
        <label>Locale</label>
        <input type="text" name="locale" value="<?= $academy['locale'] ?>">
    </div>
    <div>
        <label>Timezone</label>
        <input type="text" name="timezone" value="<?= $academy['timezone'] ?>">
    </div>
    <button type="submit">به روز رسانی آموزشگاه</button>
</form>