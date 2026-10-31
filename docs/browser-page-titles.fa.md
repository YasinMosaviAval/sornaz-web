# عنوان صفحات در تب مرورگر

قالب‌ها عنوان تعیین‌شده با `View::title()` را نمایش می‌دهند؛ عنوان اختصاصی `pageTitle` در صفحات عمومی حفظ شده است. در نبود عنوان، نام ترجمه‌شدهٔ سایت استفاده می‌شود تا عنوان خالی نماند. عنوان با `e()` escape می‌شود. در پنل، تغییر بخش، ویرایش نوشته، حساب کاربری و پیش‌نمایش رابط، عنوان مرورگر را همراه عنوان هدر تغییر می‌دهند. این تغییر درخواست شبکه، تایمر یا observer تازه ندارد.

فایل‌های زمان اجرا برای آپلود:

علت خالی‌شدن عنوان پس از لود صفحات عمومی، پردازش head توسط ویرایشگر ترجمه بود: قرارگرفتن span داخل title عنوان مرورگر را خالی می‌کرد. `admin-inline-editor.js` اکنون پیمایش پیش‌فرض را از body آغاز می‌کند و head و title را از علامت‌گذاری کنار می‌گذارد. ترجمه و ویرایش متن‌های قابل مشاهده همچنان فعال است. بازتولید مرورگر پیش از اصلاح عنوان خالی را نشان داد؛ پس از اصلاح هر هشت حالت مهمان/مدیر/عضو/مدیر دیگر در فارسی و انگلیسی موفق شد.

- `Modules/Page/Resources/Views/layouts/main.php`
- `Modules/System/Resources/Views/layouts/auth.php`
- `Modules/Analytics/Resources/Views/layouts/auth.php`
- `Modules/Analytics/Resources/Views/layouts/admin.php`
- `Modules/Home/Resources/Views/layouts/main.php`
- `views/layouts/main.php`
- `resources/views/layouts/dashboard.php`
- `resources/views/layouts/partials/head.php`
- `resources/views/layouts/public-app.php`
- `assets/Analytics/js/admin.js`
- `assets/Analytics/js/admin-inline-editor.js`
- `assets/Analytics/js/account.js`
- `assets/Analytics/js/posts.js`
- `assets/Analytics/js/public-ui-preview-v2.js`

Migration و وابستگی تازه لازم نیست و Flutter تغییر نکرده است. قالب‌ها و JS مستقل از هم قابل انتشارند؛ نسخهٔ فایل‌های ثابت طبق سازوکار فعلی تازه شود. بازگشت با برگرداندن همین فایل‌ها انجام می‌شود و داده‌ای تغییر نمی‌کند.

پس از آپلود، عنوان تب خانه، درباره ما، تماس با ما، ورود، ثبت‌نام و یک نوشته را در فارسی و انگلیسی بررسی کنید. در پنل بین نوشته‌ها، سازمان و کاربران جابه‌جا شوید؛ عنوان تب باید با عنوان هدر همان بخش هماهنگ باشد. آزمون مرورگر مرتبط `scripts/panel_workspace_tabs_browser_test.cjs` است. بررسی محلی قالب‌های واقعی، عنوان اختصاصی، عنوان خالی و escape نویسه‌های خاص را نیز پوشش داده است.
