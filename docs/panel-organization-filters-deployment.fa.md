# فیلترهای مشترک پنل و سازمان کلاس‌ها — ۱۳ اکتبر ۲۰۲۶

## اصلاح گزینهٔ آموزشگاه در فرم کلاس

در افزودن و ویرایش کلاس، آموزشگاه مجاز پیش از شعبه‌ها نمایش داده می‌شود. تشخیص نقش‌های مالک، مدیر و منشی آموزشگاه، پسوند درجه/سطح را هم می‌پذیرد؛ نقش شعبه یا مدرس این دسترسی را نمی‌گیرد. عنوان آموزشگاه با زبان جاری خوانده می‌شود.

فایل‌های این اصلاح: `Modules/Academy/Services/ClassroomOrganizationScope.php`، `Modules/Academy/Services/AcademyClassroomService.php` و `assets/Analytics/js/classroom-templates.js`. ابتدا دو فایل سرور و سپس قالب جاوااسکریپت را منتقل کنید. کلاس یا migration تازه لازم نیست؛ برای ذخیرهٔ کلاس به نام خود آموزشگاه، migration موجود `docs/database/migrations/2026_10_13_classroom_organizations.sql` باید قبلاً موفق اجرا شده باشد. این اصلاح هیچ migration را خودکار اجرا نمی‌کند.

در ساختار قدیمی، نام آموزشگاه حذف نمی‌شود؛ گزینه تا زمان آماده‌شدن مالکیت آموزشگاه غیرفعال است و توضیح کوتاه نشان داده می‌شود. انتخاب پیش‌فرض روی سازمان قابل استفاده قرار می‌گیرد. ارسال مستقیم آموزشگاه به سرورِ دارای ساختار قدیمی نیز پیش از نوشتن با پیام روشن رد می‌شود.

آزمون MySQL با دیتابیس موقت، گزینهٔ آموزشگاه در ساختار قدیمی و جدید، نقش مدیر درجه‌بندی‌شده و رد مدیر شعبه/مدرس را بررسی می‌کند. آزمون مرورگر نیز نام آموزشگاه و انتخاب آن در هر دو فرم را می‌سنجد. پس از آپلود همین مسیرها را با حساب آزمایشی آموزشگاه بررسی کنید. برای بازگشت سه فایل همین اصلاح را به نسخهٔ قبلی برگردانید؛ دیتابیس و داده‌های کلاس‌ها تغییری نمی‌کنند.

کد ساخت و انتخاب فیلترهای سازمان در `assets/Analytics/js/panel-organization-filters.js` مشترک است. هر صفحه فقط دادهٔ سازمان و تابع فیلتر خودش را به آن می‌دهد. شناسهٔ داخلی بعضی بخش‌ها شناسهٔ شعبه و بعضی بخش‌ها شناسهٔ حساب سازمان است؛ تبدیل این شناسه‌ها در همان صفحه باقی می‌ماند. کنترل محدودهٔ دسترسی همچنان در سرور انجام می‌شود.

## فایل‌های قابل انتشار این تغییر

فایل جدید سرور:

- `Modules/Academy/Services/ClassroomOrganizationScope.php`

فایل‌های تغییرکردهٔ سرور:

- `Modules/Academy/Services/AcademyClassroomService.php`
- `Modules/Academy/Services/AcademyTermService.php`
- `Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php`
- `Modules/Analytics/Resources/Views/layouts/admin.php`
- `Modules/Analytics/Resources/Views/sections/panel-page-header-template.php`
- `Modules/Analytics/Resources/Views/sections/gallery.php`
- `Modules/Analytics/Resources/Views/sections/terms.php`
- `Modules/Analytics/Resources/Views/sections/sidebar.php`

فایل‌های مرورگر، همگی در `assets/Analytics/js/`:

- `panel-organization-filters.js` — فایل جدید مشترک
- `branches.js`
- `dashboard.js`
- `students.js`
- `teachers.js`
- `lessons.js`
- `classrooms.js`
- `classroom-templates.js`
- `courses.js`
- `terms.js`
- `finance.js`
- `member-schedules.js`
- `reports.js`
- `notifications.js`
- `instruments.js`
- `availabilities.js`
- `availability-exceptions.js`
- `scheduling-rules.js`
- `schedules.js`
- `profile-content.js`
- `points.js`
- `points.js`
- `gallery.js`
- `gallery-templates.js`

این فهرست مربوط به همین تغییر است؛ تغییرات قبلی نقش‌ها، تنظیمات و ثبت‌نام و migrationهای آن‌ها باید طبق راهنمای مربوط به همان تغییر منتشر شوند. ابزارهای آزمون و فایل‌های تنظیمات محلی جزو بستهٔ عادی انتشار نیستند.

## migration و ترتیب انتشار

1. از نسخهٔ فعلی فایل‌ها و دیتابیس پشتیبان بگیرید. روی سرور تست، `docs/database/migrations/2026_10_13_classroom_organizations.sql` را اجرا کنید. آپلود فایل SQL آن را اجرا نمی‌کند.
2. در phpMyAdmin ابتدا دیتابیس سایت را انتخاب و کل فایل را در یک نشست Import کنید. migration جدول‌های با پیشوند `p_` یا بدون پیشوند را تشخیص می‌دهد، نوع فعلی شناسه‌ها را از metadata می‌خواند و ستون nullable `academy_id`، index و کلید خارجی آموزشگاه را اضافه و `branch_id` را nullable می‌کند. کلاس‌های موجود شعبه بازنویسی یا جابه‌جا نمی‌شوند. نتیجهٔ نهایی باید `APPLIED` یا `ALREADY_APPLIED` باشد. در حالت `NOT_APPLIED` هیچ تغییری اجرا نشده و ستون `migration_error` علت انتخاب دیتابیس، نبود جدول/ستون یا نام‌های مبهم را مشخص می‌کند. اگر هر دو ساختار در یک دیتابیس وجود داشته باشند، فایل برای جلوگیری از انتخاب اشتباه، تغییری اعمال نمی‌کند.
3. ابتدا کلاس جدید `ClassroomOrganizationScope.php` و فایل مشترک `panel-organization-filters.js` را قرار دهید. سپس layout پنل که فایل مشترک را بارگذاری می‌کند، بعد سرویس‌ها و کنترلر و فایل‌های مرورگر و در پایان سایر قالب‌ها را منتشر کنید. هرگز layout پیش از خود فایل مشترک منتشر نشود. برای HTML کش‌شدهٔ قدیمی، `branches.js` فایل مشترک را در صورت نیاز بارگذاری و فیلترهای منتظر را دوباره اجرا می‌کند.
4. autoload باید از namespace `Modules\` پشتیبانی کند. در بسته‌ای که autoload به‌صورت authoritative ساخته شده، آن را با `composer dump-autoload --optimize` از همین نسخه دوباره بسازید. وابستگی تازه‌ای اضافه نشده است.
5. کد جدید قبل از اجرای migration، کلاس‌های شعبه را با ساختار قدیمی می‌خواند؛ گزینهٔ مالکیت آموزشگاه فقط پس از وجود ستون جدید ارائه می‌شود. قرارداد قدیمی `branchId` برای کلاس شعبه همچنان پذیرفته می‌شود. کد سمت مرورگر از version فایل‌های موجود در layout استفاده می‌کند؛ پس از انتشار OPcache و کش قالب را طبق تنظیمات هاست تازه‌سازی کنید.

migration اصلی و انتشار روی سرور اصلی در این کار اجرا نشده‌اند.

## بازگشت

برای بازگشت معمول، فایل‌ها و autoload نسخهٔ پیشین را برگردانید و schema افزوده‌شده را نگه دارید. دادهٔ کلاس‌های آموزشگاه حفظ می‌شود، ولی نسخهٔ قدیمی آن‌ها را مدیریت نمی‌کند.

فایل `2026_10_13_classroom_organizations_rollback.sql` فقط وقتی مجاز به حذف ستون است که هیچ کلاس متعلق به آموزشگاه، حتی حذف‌شده، وجود نداشته باشد. در غیر این صورت با خطای صریح متوقف می‌شود؛ هیچ کلاسی را خودکار به شعبه منتقل یا حذف نمی‌کند.

## آزمون پس از بارگذاری

- آموزشگاه دارای شعبه: گالری، ترم‌ها و امور مالی دقیقاً یک «همه»، یک «آموزشگاه» و یک تب برای هر شعبه داشته باشند. «آموزشگاه» در گالری فقط رسانهٔ حساب آموزشگاه را نشان دهد.
- آموزشگاه بدون شعبه و حساب شعبه: باکس فیلتر سازمان نمایش داده نشود. حساب شعبه نتواند با شناسهٔ دستکاری‌شده کلاس آموزشگاه یا شعبهٔ دیگر را تغییر دهد.
- افزودن رسانه داخل گالری و در سمت لینک برگشت به سایت باشد؛ نوع رسانه و فایل در دو ستون کنار هم نمایش داده شوند.
- کلاس جدید برای خود آموزشگاه و برای شعبه ثبت و ویرایش شود. یک ترم باز با کلاس آموزشگاه ساخته شود. تغییر سازمان کلاس دارای جلسه باید رد شود.
- جدول ترمِ خالی «هیچ ترمی یافت نشد» نشان دهد؛ صفحه‌بندی برای ۰ تا ۱۰ ردیف پنهان و برای ۱۱ ردیف نمایان باشد.
- در سایدبار مدیر سایت، کلاس‌ها پس از شعبه‌ها فقط یک‌بار نمایش داده شوند.

آزمون‌های خودکار مرتبط:

```sh
node scripts/panel_organization_filters_browser_test.cjs
node scripts/classroom_organization_browser_test.cjs
node scripts/academy_organization_tabs_browser_test.cjs
node scripts/gallery_overview_browser_test.cjs
node scripts/classroom_organization_mysql_test.cjs
node scripts/run-release-tests.cjs --browser
```

آزمون MySQL، سرویس واقعی ثبت/ویرایش کلاس و ساخت ترم باز را با دادهٔ ساختگی در دیتابیس تصادفیِ جدا اجرا و در پایان فقط همان دیتابیس را پاک می‌کند. برای سرویس تست غیرمحلی باید متغیرهای `SORNAZ_TEST_MYSQL_DSN`، `SORNAZ_TEST_MYSQL_USER` و `SORNAZ_TEST_MYSQL_PASSWORD` را به محیط اختصاصی آزمون بدهید. آزمون‌های مرورگر از APIهای ساختگی استفاده می‌کنند و جای آزمون روی سرور تست را نمی‌گیرند.
