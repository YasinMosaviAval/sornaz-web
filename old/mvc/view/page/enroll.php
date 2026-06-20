<div class="header_ac">
    <h1 class="h1_ac">ثبت‌نام در کلاس موسیقی</h1>
    <p>به آموزشگاه سُرناز خوش آمدید - لطفاً اطلاعات خود را وارد کنید</p>
</div>

<div class="container">
    <form>
        <div class="form-group">
            <label for="full_name">نام و نام خانوادگی <span style="color:#d32f2f;">*</span></label>
            <input type="text" id="full_name" required>
        </div>

        <div class="form-group">
            <label for="phone">شماره موبایل <span style="color:#d32f2f;">*</span></label>
            <input type="tel" id="phone" required placeholder="۰۹۱۲XXXXXXX">
        </div>

        <div class="form-group">
            <label for="email">ایمیل</label>
            <input type="email" id="email" placeholder="example@email.com">
        </div>

        <div class="form-group">
            <label for="instrument">ساز مورد نظر</label>
            <select id="instrument" required>
                <option value="">انتخاب کنید...</option>
                <option value="violin">ویولن</option>
                <option value="piano">پیانو</option>
                <option value="guitar">گیتار</option>
                <option value="setar">ستار</option>
                <option value="tombak">تمبک</option>
            </select>
        </div>

        <div class="form-group">
            <label for="level">سطح فعلی شما</label>
            <select id="level">
                <option value="beginner">مبتدی</option>
                <option value="intermediate">متوسط</option>
                <option value="advanced">پیشرفته</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">پیام یا توضیح اضافی</label>
            <textarea id="message" rows="4" placeholder="اگر توضیح خاصی دارید بنویسید..."></textarea>
        </div>

        <button type="submit">ثبت‌نام در کلاس</button>
        <p class="note">پس از ثبت‌نام، با شما تماس گرفته می‌شود تا زمان کلاس نهایی شود.</p>
    </form>
</div>