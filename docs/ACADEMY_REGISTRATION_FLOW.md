# فرآیند ثبت آموزشگاه

## ۱. مسیر شروع

مسیرهای اصلی در `Modules/Academy/Routes/web.php` تعریف شده‌اند:

- `GET /academy/send-academy-request`: نمایش فرم
- `POST /academy/send-academy-request/send-otp`: ارسال OTP
- `POST /academy/send-academy-request`: ثبت نهایی آموزشگاه

View اصلی `Modules/Academy/Resources/Views/send-academy-request.php` و JavaScript آن `assets/Academy/js/academy-registration.js` است. این فایل‌ها انتخاب ایمیل/موبایل، سنجش رمز، OTP و قوانین را کنترل می‌کنند.

## ۲. اعتبارسنجی و OTP

`AcademyRegistrationController::sendOtp()` داده‌ها را با `AcademyRegistrationRequest` و متد `validatedData()` بررسی می‌کند. سپس `RegistrationOtpService` کد تأیید را ارسال می‌کند.

در `AcademyRegistrationController::store()`:

1. فرم و پذیرش قوانین بررسی می‌شود.
2. OTP ایمیل یا موبایل تأیید می‌شود.
3. `AcademyRegistrationService::register()` اجرا می‌شود.
4. ثبت آموزشگاه همین‌جا تمام می‌شود و هیچ مرحله‌ای برای ثبت شعبه باز نمی‌شود.
   کاربر واردشده مستقیماً به `/analytics/admin-panel#branches`، یعنی منوی سازمان،
   منتقل می‌شود. مهمان به `/system/login` می‌رود تا با حساب آموزشگاه وارد شود؛
   حساب جاری به‌صورت خودکار عوض نمی‌شود.

## ۳. ساخت کاربر و آموزشگاه

`AcademyRegistrationService::createAcademy()`:

- کاربر آموزشگاه را با `type=academy` ایجاد می‌کند.
- وضعیت کاربر را `approved` می‌کند.
- `created_by`، `updated_by` و `approved_by` را برابر user_id درخواست‌کننده قرار می‌دهد.
- نقش `academy_owner` را برای حساب آموزشگاه ایجاد می‌کند.
- درخواست‌کننده را به‌عنوان عضو آموزشگاه با نقش `academy_manager` درجهٔ ۱ ثبت می‌کند.
- رکورد `academies` را ایجاد می‌کند.
- پروفایل `z_user_profiles` را ایجاد یا پیدا می‌کند.
- حساب مالی نوع `academy_wallet` را ایجاد می‌کند.
- عنوان، شعار و توضیحات آموزشگاه را در `translations` ذخیره می‌کند.

در این مرحله هیچ حساب یا رکورد شعبه‌ای ایجاد نمی‌شود. ثبت شعبه‌های اصلی و فرعی
فقط از منوی سازمان در پنل کاربری انجام می‌شود و به ادامهٔ ثبت آموزشگاه وابسته نیست.

## قرارداد اپ و سازگاری نشست‌های قبلی

`RegistrationController` در API پس از ثبت موفق آموزشگاه مستقیماً
`stage=complete` و `without_branch=true` برمی‌گرداند. فیلدهای افزوده‌شدهٔ
`next_section=branches` و `next_url=/analytics/admin-panel#branches` مقصد پنل را
مشخص می‌کنند؛ `requires_sign_in` برای مهمان `true` است. نشست قدیمی با
`stage=choice` بدون ایجاد شعبه به حالت کامل تبدیل می‌شود. ارسال دوبارهٔ فرم
کامل‌شده یا درخواست مرحلهٔ قدیمی شعبه رکورد جدید ایجاد نمی‌کند؛ توکن جریان،
مالک نشست، انقضا و اتصال OTP به اطلاعات فرم همچنان کنترل می‌شوند.

مسیرهای قدیمی `/academy/registration-complete`، تکمیل بدون شعبه و
`/academy/register-main-branch` برای سازگاری باقی می‌مانند، اما فرم شعبه را باز
یا ذخیره نمی‌کنند و کاربر را به سازمان یا ورود هدایت می‌کنند. ارسال OTP از مسیر
قدیمی شعبه با پاسخ `409` و راهنمای مقصد رد می‌شود. فرم ثبت شعبه در پنل سازمان
و مسیرهای مجوزدار `/academy/admin/branches/registration` مستقل باقی می‌مانند.

## ۴. اعلان‌های آموزشگاه

پس از موفقیت ثبت، `AcademyRegistrationService::register()` اعلان‌های زیر را با `UserNotificationService` ایجاد می‌کند:

- برای مدیر سایت: درخواست ثبت آموزشگاه با شناسه درخواست‌کننده و آموزشگاه
- برای مدیر سایت: ایجاد نقش مدیر آموزشگاه درجهٔ ۱
- برای درخواست‌کننده: موفقیت ارسال درخواست
- برای حساب آموزشگاه: ارسال درخواست توسط کاربر درخواست‌کننده

متن فارسی و انگلیسی اعلان‌ها در `translations` قرار می‌گیرد.

## ۵. جدول‌های درگیر

- `users`: حساب آموزشگاه
- `academies`: رکورد حقوقی آموزشگاه
- `z_user_profiles`: پروفایل کاربر آموزشگاه
- `financial_system_accounts`: حساب `academy_wallet`
- `access_system_roles`: نقش‌های آموزشگاه
- `user_roles`: نقش `academy_owner` حساب آموزشگاه
- `academy_branch_members` و `academy_branch_member_roles`: عضویت و نقش مدیر درجهٔ ۱ درخواست‌کننده در همان آموزشگاه
- `translations`: مشخصات متنی آموزشگاه و اعلان‌ها
- `user_notifications`: اعلان‌های ثبت درخواست
- `user_referrals`: برای حساب‌هایی که از مسیر عمومی UserService ایجاد می‌شوند

ثبت آموزشگاه داخل transaction اجرا می‌شود؛ در صورت خطا، درج‌های همین فرآیند برگشت داده می‌شوند.
