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
4. پس از ثبت، کاربر به صفحه ثبت شعبه اصلی منتقل می‌شود.

## ۳. ساخت کاربر و آموزشگاه

`AcademyRegistrationService::createAcademy()`:

- کاربر آموزشگاه را با `type=academy` ایجاد می‌کند.
- وضعیت کاربر را `approved` می‌کند.
- `created_by`، `updated_by` و `approved_by` را برابر user_id درخواست‌کننده قرار می‌دهد.
- نقش `academy_owner` را برای حساب آموزشگاه ایجاد می‌کند.
- نقش `academy_manager` را برای درخواست‌کننده ثبت می‌کند.
- رکورد `academies` را ایجاد می‌کند.
- پروفایل `z_user_profiles` را ایجاد یا پیدا می‌کند.
- حساب مالی نوع `academy_wallet` را ایجاد می‌کند.
- عنوان، شعار و توضیحات آموزشگاه را در `translations` ذخیره می‌کند.

در این مرحله کاربر یا رکورد شعبه اصلی ایجاد نمی‌شود؛ این کار عمداً به مرحله بعد موکول شده است.

## ۴. اعلان‌های آموزشگاه

پس از موفقیت ثبت، `AcademyRegistrationService::register()` اعلان‌های زیر را با `UserNotificationService` ایجاد می‌کند:

- برای مدیر سایت: درخواست ثبت آموزشگاه با شناسه درخواست‌کننده و آموزشگاه
- برای مدیر سایت: ایجاد نقش موسس آموزشگاه
- برای درخواست‌کننده: موفقیت ارسال درخواست
- برای حساب آموزشگاه: ارسال درخواست توسط کاربر درخواست‌کننده

متن فارسی و انگلیسی اعلان‌ها در `translations` قرار می‌گیرد.

## ۵. جدول‌های درگیر

- `users`: حساب آموزشگاه
- `academies`: رکورد حقوقی آموزشگاه
- `z_user_profiles`: پروفایل کاربر آموزشگاه
- `financial_system_accounts`: حساب `academy_wallet`
- `access_system_roles`: نقش‌های آموزشگاه
- `user_roles`: `academy_owner` و `academy_manager`
- `translations`: مشخصات متنی آموزشگاه و اعلان‌ها
- `user_notifications`: اعلان‌های ثبت درخواست
- `user_referrals`: برای حساب‌هایی که از مسیر عمومی UserService ایجاد می‌شوند

ثبت آموزشگاه داخل transaction اجرا می‌شود؛ در صورت خطا، درج‌های همین فرآیند برگشت داده می‌شوند.
