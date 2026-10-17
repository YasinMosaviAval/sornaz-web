# عملیات تخفیف، راهنمای ترم و تب‌های برنامه زمانی

این تغییر ادامهٔ بستهٔ [تخفیف‌های سازمانی](term-discounts-deployment.fa.md) است و migration تازه ندارد. migration قبلی برای ایجاد و ویرایش تخفیف سازمانی همچنان لازم است؛ روی دیتابیس اصلی در این کار اجرا نشده است.

## فایل‌های این تغییر

- `Modules/Academy/Services/AcademyDiscountService.php`
- `Modules/Academy/Controllers/Web/AcademyTermController.php`
- `Modules/Academy/Routes/web.php`
- `Modules/Analytics/Resources/Views/sections/term-discounts.php`
- `Modules/Analytics/Resources/Views/sections/schedule-page-tabs.php` (قالب جدید؛ کلاس جدیدی اضافه نشده)
- `Modules/Analytics/Resources/Views/sections/schedules.php`
- `Modules/Analytics/Resources/Views/sections/member-schedules.php`
- `Modules/Analytics/Resources/Views/sections/availabilities.php`
- `Modules/Analytics/Resources/Views/sections/availability-exceptions.php`
- `Modules/Analytics/Resources/Views/sections/scheduling-rules.php`
- `Modules/Analytics/Resources/Views/sections/dashboard.php`
- `assets/Analytics/js/terms.js`
- `assets/Analytics/js/panel-page-guides.js`
- `assets/Analytics/js/dashboard.js`

آزمون‌ها در `scripts/term_discount_policy_test.cjs`، `scripts/term_discounts_browser_test.cjs` و `scripts/panel_guides_browser_test.cjs` گسترش یافته‌اند؛ fixture نمای جدول و فهرست عملیات وب در آزمون parity نیز به‌روزرسانی شده‌اند. این ابزارها جزو فایل‌های لازم در اجرای سایت نیستند.

## رفتار و ترتیب انتشار

ابتدا سرویس و کنترلر، سپس مسیرهای جدید POST به نشانی‌های `/academy/admin/term-discounts/{id}/update` و `/academy/admin/term-discounts/{id}/delete` و قالب جدید تب‌های برنامه زمانی را منتشر کنید. سپس قالب‌های مصرف‌کننده و فایل‌های JavaScript را بارگذاری کنید. نسخه قبلی رابط همچنان با مسیر ایجاد و خواندن سازگار است. کش فایل‌های ثابت را مطابق روش انتشار سایت تازه کنید.

ویرایش و حذف در سرور با محدودهٔ سازمان و مالکیت بررسی می‌شوند و CSRF دارند. شعبه نمی‌تواند تخفیف ارث‌رسیده از آموزشگاه را ویرایش یا حذف کند. سازمان یک تخفیف سازمانی هنگام ویرایش جابه‌جا نمی‌شود. حذف، `deleted_at` و ثبت‌کنندهٔ حذف را تنظیم می‌کند؛ رکورد و ارتباط سازمانی آن باقی می‌ماند و دیگر در انتخاب تخفیف جدید دیده نمی‌شود. رکوردهای ترم و فاکتورهای قبلی تغییر نمی‌کنند. حذف متن ترجمه با تراکنش مشترک حذف نرم انجام می‌شود.

جزئیات در دیالوگ، ویرایش زیر سطر و تأیید حذف با دیالوگ مشترک پنل نمایش داده می‌شوند. راهنمای ترم یک بار، بالای جدول و داخل تب مدیریت ترم قرار دارد؛ دکمهٔ ویرایش مدیر کنار آن است. تب تخفیف راهنمای آبی و هشدار زرد ندارد. هر پنج صفحهٔ برنامه زمانی در پروژه موجود بودند و بدون بازیابی فایل قدیمی، با قالب تب مشترک برای مدیر سایت پیوند داده شده‌اند. میانبرهای پایین داشبورد حذف شده‌اند.

## آزمون پس از بارگذاری و بازگشت

در سرور تست با حساب آموزشگاه، جزئیات تخفیف را باز کنید، ویرایش زیر سطر را ذخیره کنید و یک بار حذف را لغو و بار دیگر تأیید کنید. رکورد باید در دیتابیس باقی بماند ولی در فهرست فعال نباشد. درخواست مستقیم ویرایش/حذف با حساب آموزشگاه دیگر و ویرایش تخفیف آموزشگاه با حساب شعبه باید رد شود. راهنما را با مدیر و کاربر عادی بررسی کنید؛ در تب تخفیف نباید نمایش داده شود. با مدیر سایت هر پنج تب برنامه زمانی را باز کنید و نبود میانبرهای پایین داشبورد را بررسی کنید.

برای بازگشت رابط، نسخهٔ پیشین قالب‌ها و JavaScript را برگردانید؛ پیش از حذف قالب جدید تب‌ها، مصرف‌کنندگان آن را برگردانید. سرویس محدودکنندهٔ سازمان را نگه دارید. دیتابیس، ارتباط سازمانی و رکوردهای حذف نرم را پاک نکنید؛ بازیابی رکورد حذف‌شده باید پس از بررسی مستقل انجام شود. محدودیت rollback بستهٔ تخفیف‌های سازمانی نیز همچنان برقرار است.
