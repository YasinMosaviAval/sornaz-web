# نمایش عمومی بخش‌های غیرعمومی برای طراحی در فیگما

مسیر صفحه: `/analytics/public-ui-preview`؛ این نشانی بدون ورود باز می‌شود. لینک آن در هدر عمومی سایت و هدر پنل فقط برای حساب با `user_id=1` نمایش داده می‌شود. پنهان بودن لینک به معنی محافظت از نشانی نیست.

صفحه هدر اصلی سایت، سایدبار و هدر واقعی پنل را استفاده می‌کند. بیشتر بخش‌های مدیریتی با خود قالب‌های همان بخش‌ها رندر می‌شوند و جدول‌های خالی با ردیف نمونه پر می‌شوند. در نقش کاربر عادی، داشبورد و تب‌های یادگیری با دادهٔ ساختگی و بخش‌های درس‌ها، برنامهٔ زمانی و امور مالی با قالب اصلی پنل شخصی و دادهٔ ساختگی نمایش داده می‌شوند. برای بخش‌هایی که قالب اصلی آن‌ها به احراز هویت یا دادهٔ واقعی وابسته است، نمای فهرست و فرم از `MobilePanelCatalog` ساخته می‌شود. توضیحات اضافی بالای پنل وجود ندارد. دکمه‌های افزودن، خروجی اکسل و خروجی پی‌دی‌اف در بخش‌های مربوط دیده می‌شوند؛ کلیک در پیش‌نمایش به عملیات واقعی وصل نیست.

نقش از پارامتر `role` انتخاب می‌شود: `admin` (پیش‌فرض)، `academy`، `branch` و `user`. آدرس مرورگر بخش و حالت انتخاب‌شده را در `section` و `action` ثبت می‌کند؛ این آدرس مستقیم را می‌توان به طراح داد. برای نمونه: `/analytics/public-ui-preview?role=user&section=gallery-cover` و `/analytics/public-ui-preview?role=admin&section=finance`.

داده‌های جدول و هدر پنل ساختگی‌اند؛ فرم‌ها هیچ درخواست نوشتنی نمی‌فرستند و مسیرهای داخلی API و دادهٔ کاربران در خروجی آن قرار نمی‌گیرند. سه بخش محافظت‌شدهٔ «انواع آموزشی»، «انواع کلاس» و «تعطیلات رسمی» در `preview-protected-sections.php` با HTML مستقل و دادهٔ نمونه رندر می‌شوند؛ شرط دسترسی یا دادهٔ قالب اصلی آن‌ها تغییر نکرده است. برای طراحی نهایی، حالت‌های اعتبارسنجی، خطا، دادهٔ خالی و بارگذاری نیز باید در کنار این مرجع بررسی شوند.

## انتشار و بازگشت

فایل‌های لازم برای همین قابلیت: `Modules/Analytics/Controllers/Web/PublicUiPreviewController.php`، `Modules/Analytics/Resources/Views/public-ui-preview.php`، `Modules/Analytics/Resources/Views/sections/preview-protected-sections.php`، `Modules/Analytics/Resources/Views/sections/preview-student-learning.php`، `assets/Analytics/css/public-ui-preview-v2.css`، `assets/Analytics/js/public-ui-preview-v2.js` و تغییرهای `Modules/Analytics/Routes/routes.php`، `Modules/Page/Resources/Views/sections/main-header.php`، `Modules/Analytics/Resources/Views/sections/header.php`، `Modules/Analytics/Resources/Views/sections/sidebar.php`، `Modules/Analytics/Resources/Views/sections/notation.php` و `Modules/Analytics/Resources/Views/sections/personal-panel-sections.php`. ابتدا فایل‌های جدید و سپس قالب‌های مشترک، مسیر و لینک‌های هدر بارگذاری شوند تا لینک شکسته نمایش داده نشود. این قابلیت migration ندارد. برای بازگشت، لینک‌های هدر و مسیر را بردارید و قالب‌های مشترک را به نسخهٔ قبلی بازگردانید؛ سپس فایل‌های جدید را حذف کنید. بعد از بارگذاری، صفحه را بدون ورود و نمایش لینک را با حساب مدیر سایت و یک حساب عادی بررسی کنید.

این کار مجوز انتشار روی سرور اصلی نیست و در این تغییر انتشار انجام نشده است.
