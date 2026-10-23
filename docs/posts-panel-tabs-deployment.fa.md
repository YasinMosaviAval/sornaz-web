# تغییر منوی نوشته‌ها و حذف مرکز تست‌ها

منوی نوشته‌ها شامل دو تب نوشته‌ها و دسته‌بندی نوشته‌ها است. لینک قدیمی `#post-categories` همچنان کار می‌کند. مرکز تست‌ها، گزارش آن، داده‌های نمایشی زمان‌بندی و مسیرهای `/_test/` ماژول‌های Analytics و Academy حذف شدند. هیچ دادهٔ موجود در دیتابیس حذف نمی‌شود. سرویس مشترک ترجمه و زمان‌بندی حفظ شده است.

## فایل‌های سرور

- `Modules/Academy/Routes/web.php`
- `Modules/Analytics/Routes/routes.php`
- `Modules/Analytics/Controllers/Web/AdminTestController.php`
- `Modules/Analytics/Controllers/Web/AnalyticsController.php`
- `Modules/Analytics/Services/AdminTestDataService.php`
- `Modules/Analytics/Resources/Views/admin-panel.php`
- `Modules/Analytics/Resources/Views/sections/sidebar.php`
- `Modules/Analytics/Resources/Views/sections/posts.php`
- `Modules/Analytics/Resources/Views/sections/post-categories.php`
- `assets/Analytics/js/admin.js`
- `assets/theme/help-center.js`
- همین راهنما

پس از جایگزینی فایل‌ها، `Modules/Analytics/Resources/Views/sections/tests.php`، `docs/admin-test-center.md` و چهار فایل `docs/admin-test-manager-{addresses,contacts,media,music}.md` از سرور حذف شوند.

## ترتیب و بازگشت

کلاس، وابستگی یا migration تازه لازم نیست. ابتدا مسیرهای حذف‌شده و کنترلر آن‌ها را در یک تعویض اتمی نسخه منتشر کنید؛ سپس JavaScript، قالب تب‌ها، منو، نمای پنل و کنترلر پنل و سرویس ترجمه را جایگزین کنید. حذف فایل‌های قدیمی آخر انجام شود. درخواست‌های قبلاً آغازشده ممکن است تا پایان اجرا ادامه یابند. برای بازگشت، همین فایل‌ها و فایل‌های حذف‌شده را از نسخهٔ پشتیبان همان انتشار بازیابی کنید. تنظیمات محیط، رسانه و دیتابیس تغییر نکنند.

## اپلیکیشن

فایل‌های `lib/screens/Site/panel_navigation.dart` و `lib/screens/Site/site_panel_page.dart` تغییر کرده‌اند. آزمون مرتبط `test/panel_page_tabs_test.dart` است. انتقال انتخابی این سه فایل به شاخهٔ انتشار ممکن است؛ وابستگی تازه‌ای به قابلیت‌های آزمایشی ندارد. بستهٔ اپلیکیشن در این کار ساخته نمی‌شود.

## بررسی پس از آپلود

با مدیر سایت، وجود تنها یک منوی نوشته‌ها، بارگیری هر دو تب، جستجو و فرم‌های قبلی و لینک مستقیم دسته‌بندی‌ها را بررسی کنید. با کاربر عادی نبود دسترسی به API نوشته‌ها را کنترل کنید. مرکز تست‌ها نباید در منو یا راهنما ظاهر شود؛ مسیرهای قدیمی تست باید ۴۰۴ بدهند. این بررسی باید با نسخهٔ قدیمی و جدید اپلیکیشن و کش مرورگر نیز انجام شود.
