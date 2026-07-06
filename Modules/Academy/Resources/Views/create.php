<h1>ایجاد آموزشگاه</h1>

<form method="post" action="/academy">
    <div>
        <label>نام کاربری</label>
        <input type="text" name="username">
    </div>
    <div>
        <label>ایمیل</label>
        <input type="email" name="email">
    </div>
    <div>
        <label>موبایل</label>
        <input type="text" name="phone">
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
        <input type="text" name="locale" value="fa">
    </div>
    <div>
        <label>Timezone</label>
        <input type="text" name="timezone" value="Asia/Tehran">
    </div>
    <button type="submit">ذخیره آموزشگاه</button>
</form>