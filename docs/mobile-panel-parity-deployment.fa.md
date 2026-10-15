# تکمیل قرارداد پنل موبایل

این تغییر، پنل Native موجود Flutter را به عملیات فعلی وب متصل می‌کند؛ کنترلر، سرویس مالی، هویت کاربر و مدل مجوز جایگزین نمی‌شوند. تغییرات بر مبنای ادغام `c328baf` هستند.

## فایل‌های اجرایی لازم

- `Modules/Analytics/Services/MobilePanelCatalog.php`
- `Modules/Analytics/Services/MobilePanelAccess.php`
- `Modules/Analytics/Services/PersonalPanelDataService.php`
- `Modules/Analytics/Controllers/Web/PersonalPanelDataController.php`
- `Modules/Analytics/Routes/routes.php`

کلاس اجرایی یا وابستگی جدید و migration جدید وجود ندارد. migrationهای قبلی نسخهٔ وب، از جمله فیلدهای نمایش دسترسی‌ها و مالکیت سازمانی کلاس‌ها، باید مطابق انتشار همان نسخه اعمال شده باشند؛ این مأموریت آن‌ها را روی هیچ دیتابیسی اجرا نکرده است.

ابتدا سرویس دادهٔ شخصی و کنترلر، سپس مسیر جدید و در پایان کاتالوگ و تشخیص دسترسی را منتشر کنید و API را با حساب آزمایشی کاربر عادی، مدیر شعبه، مدیر آموزشگاه و مدیر سایت بررسی کنید. سپس نسخهٔ Flutter هماهنگ توزیع شود. عملیات قدیمی `users/update` همچنان قابل فراخوانی است و عملیات جدید نقش/مجوز جدا از آن کار می‌کنند. عملیات ناشناخته همچنان رد می‌شوند و middleware و کنترل مالکیت مسیر اصلی اجرا می‌شوند.

## آزمون‌ها

```powershell
php scripts/mobile_panel_test.php --api-only
php scripts/mobile_panel_parity_test.php
php scripts/mobile_panel_parity_test.php --inventory
php scripts/user_access_partial_save_test.php
php scripts/personal_panel_api_test.php
php scripts/profile_achievements_access_test.php
php scripts/tenant_access_test.php
```

گزینهٔ `--api-only` صرفاً بخش مستقل تست API را اجرا می‌کند؛ اجرای پیش‌فرض `mobile_panel_test.php` آزمون‌های پیش‌نمایش عمومی را نیز حفظ می‌کند. در نسخهٔ موجود، آزمون پیش‌نمایش انتظار قدیمی از HTML دارد و `branches_panel_visibility_test.php` نیز helper قالب `component()` را در fixture تعریف نکرده است؛ موفقیت آزمون API به‌معنای موفقیت این دو آزمون نیست.

## بررسی پس از انتشار

با دادهٔ تست، ذخیرهٔ مستقل نقش و مجوز، ایجاد کلاس متعلق به خود آموزشگاه، تنظیم تعطیلات آموزشگاه، تأیید/نهایی‌کردن ثبت‌نام و دسترسی به سوابق خود کاربر بررسی شوند. درخواست تغییر شناسه به سازمان یا کاربر دیگر باید رد شود. جریان‌های پیامک، پرداخت و نوشتن واقعی MySQL نیازمند آزمون محیط تست هستند؛ در این مأموریت به سرور اصلی متصل نشده‌اند.

## بازگشت

نسخهٔ قبلی همین پنج فایل اجرایی را بازگردانید. هیچ داده‌ای نباید پاک شود. کاتالوگ موبایل عملیات قابل دسترسی را از سرور دریافت می‌کند؛ بخش‌های تازه با بازگشت کاتالوگ قبلی دیگر ارائه نمی‌شوند. تنظیمات محیط، اطلاعات کاربران و امضا یا شناسهٔ اپلیکیشن تغییری نکرده‌اند.
