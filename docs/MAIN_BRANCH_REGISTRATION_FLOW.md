# فرآیند ثبت شعبه اصلی آموزشگاه

## ۱. ورود به مرحله شعبه

پس از تأیید آموزشگاه، شناسه آموزشگاه و شناسه مدیر در session با کلید `academy_branch_setup` ذخیره می‌شود و کاربر به مسیر زیر می‌رود:

- `GET /academy/register-main-branch`: نمایش فرم شعبه اصلی

View این صفحه `Modules/Academy/Resources/Views/register-main-branch.php` است. این view همان فرم آموزشگاه را بازاستفاده می‌کند و متن‌های آن را برای شعبه تغییر می‌دهد. بنابراین ظاهر، مراحل OTP و سنجش رمز یکسان هستند، اما قوانین شعبه متفاوت است.

## ۲. قوانین اختصاصی شعبه

در `assets/Academy/js/academy-registration.js`، هنگام باز کردن مودال قوانین تشخیص داده می‌شود که action فرم مربوط به `register-main-branch` است. در این حالت قوانین اختصاصی شعبه نمایش داده می‌شوند:

- صحت اطلاعات و مجوزهای شعبه
- رعایت مقررات آموزشگاه مادر
- مسئولیت خدمات شعبه
- حفظ حریم خصوصی
- بررسی و تعلیق شعبه

عنوان لینک و دیالوگ «قوانین ثبت و فعالیت شعبه» است.

## ۳. ارسال و تأیید OTP شعبه

مسیر ارسال کد:

- `POST /academy/register-main-branch/send-otp`

این مسیر به `AcademyRegistrationController::sendMainBranchOtp()` می‌رسد. فرم شامل نام کاربری، ایمیل یا موبایل، رمز عبور، تکرار رمز، کد OTP و پذیرش قوانین است.

در فرم شعبه، نام کاربری باید ۳ تا ۱۰۰ کاراکتر و فقط شامل حروف انگلیسی، عدد و `_` باشد و تکراری نباشد.

ثبت نهایی از مسیر زیر انجام می‌شود:

- `POST /academy/register-main-branch`

متد `storeMainBranch()` OTP را تأیید کرده و سپس `AcademyRegistrationService::registerMainBranch()` را فراخوانی می‌کند.

## ۴. ایجاد حساب شعبه بعد از ثبت کامل

کاربر شعبه پیش از ارسال موفق فرم ساخته نمی‌شود. در سرویس ثبت نهایی:

1. آموزشگاه از جدول `academies` پیدا می‌شود.
2. کاربر جدید با `type=branch` ایجاد می‌شود.
3. `created_by`، `updated_by` و `approved_by` برابر شناسه مدیر درخواست‌کننده ثبت می‌شوند.
4. وضعیت کاربر `approved` است.
5. حساب مالی نوع `branch_wallet` ایجاد می‌شود.
6. در `academy_branches` یک رکورد با `is_main=1` درج می‌شود.
7. برای کاربر شعبه در `user_referrals` کد دعوت یکتا ساخته می‌شود.
8. نقش `academy_branch_owner` برای کاربر شعبه ثبت می‌شود.
9. نقش `academy_branch_manager` برای مدیر درخواست‌کننده ثبت می‌شود.

تمام این عملیات داخل transaction انجام می‌شود تا حساب ناقص شعبه ایجاد نشود.

## ۵. اعلان‌های شعبه

پس از ثبت موفق، اعلان‌های اختصاصی زیر ساخته می‌شوند:

- مدیر سایت `user_id=1`: ثبت شعبه اصلی و شناسه درخواست‌کننده
- مدیر آموزشگاه: ثبت موفق شعبه اصلی
- مدیر درخواست‌کننده: موفقیت ارسال درخواست شعبه
- کاربر شعبه: ایجاد و تأیید حساب شعبه

اعلان‌ها با `UserNotificationService` ساخته می‌شوند و متن آن‌ها در `translations` برای فارسی و انگلیسی ذخیره می‌شود.

## ۶. انتقال نهایی و دسترسی پنل

پس از ثبت موفق شعبه، session مرحله پاک می‌شود و کاربر به `/`، یعنی صفحه اصلی سایت، منتقل می‌شود.

کاربر شعبه از طریق `AcademyPanelMiddleware` به `/analytics/admin-panel` دسترسی دارد. هدر اصلی سایت نیز نقش‌های `academy_manager` و `academy_branch_manager` را بررسی می‌کند و برای آن‌ها لینک «پنل ادمین» نشان می‌دهد.

## ۷. جدول‌های درگیر

- `users`: حساب شعبه
- `academy_branches`: رکورد شعبه اصلی
- `academies`: آموزشگاه مادر و ارتباط شعبه با آن
- `financial_system_accounts`: حساب `branch_wallet`
- `user_referrals`: کد معرفی کاربر شعبه
- `user_roles`: نقش‌های مالک و مدیر شعبه
- `access_system_roles`: تعریف نقش‌ها
- `translations`: متن اعلان‌ها و اطلاعات قابل ترجمه
- `user_notifications`: اعلان‌های چهار گیرنده

## ۸. فایل‌های کلیدی

- `Modules/Academy/Routes/web.php`
- `Modules/Academy/Controllers/Web/AcademyRegistrationController.php`
- `Modules/Academy/Services/AcademyRegistrationService.php`
- `Modules/Academy/Resources/Views/register-main-branch.php`
- `Modules/Academy/Resources/Views/send-academy-request.php`
- `assets/Academy/js/academy-registration.js`
- `Modules/System/Services/UserReferralService.php`
- `Modules/System/Services/UserNotificationService.php`
- `Modules/Academy/Middleware/AcademyPanelMiddleware.php`
