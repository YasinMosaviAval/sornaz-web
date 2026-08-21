# فرآیند ثبت‌نام کاربر

این سند مسیر کامل ثبت‌نام کاربر عادی را برای توسعه‌دهنده‌ای توضیح می‌دهد که با ساختار کلی پروژه آشناست اما هنوز ارتباط بین route، controller، service، view و دیتابیس را نمی‌شناسد.

## ۱. شروع فرآیند و مسیرها

صفحه فرم از این مسیرها در `Modules/System/Routes/web.php` به `SystemController` می‌رسد:

- `GET /register` و `GET /system/register`: نمایش فرم ثبت‌نام
- `POST /register`: ثبت نهایی کاربر
- `POST /register/send-otp`: ارسال کد تأیید ایمیل یا موبایل

فرم در `Modules/System/Resources/Views/register.php` قرار دارد و منطق رابط کاربری، انتخاب روش تماس، شمارش معکوس OTP، سنجش رمز عبور و اعتبارسنجی لحظه‌ای نام کاربری در `assets/System/js/auth.js` است.

## ۲. ارسال و تأیید OTP

درخواست ارسال OTP به `UserController::sendRegistrationOtp()` می‌رسد. این کنترلر مقدار ایمیل یا تلفن را بررسی کرده و از `RegistrationOtpService` برای ارسال و نگهداری موقت کد استفاده می‌کند.

در ارسال نهایی، `UserController::store()`:

1. داده‌های فرم را اعتبارسنجی می‌کند.
2. OTP را با `RegistrationOtpService::verify()` تأیید می‌کند.
3. روش ثبت‌نام را به `email` یا `phone` تبدیل می‌کند.
4. در ثبت موبایلی، `phone_verified_at` را مقداردهی می‌کند.
5. `approved_at` و `approved_by` را به‌دلیل تأیید OTP مقداردهی می‌کند.

## ۳. ایجاد داده‌ها در سرویس کاربر

`UserController` متد `UserService::register()` را فراخوانی می‌کند. این سرویس از `UserRepository` برای درج کاربر استفاده می‌کند.

در `UserService`:

- `users.type` برای کاربر عادی `human` است.
- حساب مالی با نوع `user_wallet` ایجاد می‌شود.
- ترجمه نام کامل در جدول `translations` ذخیره می‌شود.
- در `user_referrals` یک کد دعوت یکتا ایجاد می‌شود.
- نقش `user` در `user_roles` ثبت می‌شود.
- `created_by` و `updated_by` در حالت ثبت مستقیم، شناسه خود کاربر هستند.

## ۴. ورود خودکار و تکمیل ثبت‌نام

پس از موفقیت، کاربر با `auth()->login()` وارد می‌شود و `UserController::recordLogin()` مقدارهای زیر را ثبت می‌کند:

- `users.last_login_at`
- `users.last_login_ip`

سپس کاربر به صفحه اصلی سایت منتقل می‌شود و پیام موفقیت ورود خودکار نمایش داده می‌شود.

## ۵. اعلان‌ها

`UserNotificationService::send()` چهار اعلان اصلی ایجاد می‌کند:

1. برای کاربر جدید: ثبت‌نام موفق
2. برای مدیر سایت `user_id=1`: ثبت کاربر و اطلاعات تماس
3. برای مدیر سایت: ایجاد حساب مالی
4. برای مدیر سایت: ایجاد کد دعوت
5. برای مدیر سایت: ایجاد نقش کاربر

عنوان و متن اعلان‌ها در `translations` با localeهای `fa` و `en` ذخیره می‌شوند. ستون‌های `title` و `message` جدول `user_notifications` استفاده نمی‌شوند.

## ۶. جدول‌های درگیر

- `users`: اطلاعات اصلی، تأیید تماس و آخرین ورود
- `financial_system_accounts`: حساب `user_wallet`
- `user_referrals`: کد دعوت
- `user_roles`: نقش `user`
- `access_system_roles`: تعریف نقش
- `user_notifications`: گیرنده اعلان‌ها
- `translations`: متن فارسی و انگلیسی اعلان‌ها و نام کامل
- `user_sessions`: نشست احراز هویت، در صورت استفاده توسط لایه auth

اعلان خودکار عمومی برای `translations` و تمام اعلان‌های خودکار `database_update` در `Core/database/DatabaseChangeNotifier.php` غیرفعال شده‌اند.
