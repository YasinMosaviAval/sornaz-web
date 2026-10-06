# گزارش نهایی اصلاح ذخیره‌سازی متن — ۲۰۲۶-۱۰-۰۶

**اصلاح بعدی با توضیح کاربر:** ستون `pinged` و هر ۸۷ مقدار غیرخالی آن بازیابی شد؛ ۱۱ ستون فنی نیز به TEXT/LONGTEXT اصلی برگشتند. متن‌های ترجمه‌پذیر همچنان در ترجمه هستند. نتیجه و فایل آغازین معتبر فعلی در [گزارش اصلاح نهایی](text-storage-url-restoration-2026-10-06.fa.md) آمده است. توضیح حذف ستون، BLOB و صفر TEXT در ادامهٔ این سند فقط وضعیت مرحلهٔ قبلی است.

کار روی دیتابیس **محلی** و کد پروژه کامل شد. سرور اصلی تغییر نکرده است. از ۳۳ ستون خانوادهٔ TEXT در ۲۶ جدول، اکنون هیچ‌کدام خارج از `f_translations` و `p_translations` باقی نمانده است. تمام ۴۵۶ پست و ۹۲ کامنت حفظ شدند؛ هیچ ردیف محتوایی در این migration حذف نشد. مشخصات ورود مدیر نیز تغییر نکرد.

## تصمیم برای هر ستون

### ۹ ستون با نوع VARCHAR

| جدول | ستون | نوع نهایی | دلیل |
|---|---|---|---|
| f_comments | author | VARCHAR(255) | نام تاریخی نویسنده؛ متن ترجمه‌پذیر نیست |
| f_conversation_messages | attachment_path | VARCHAR(2048) | مسیر پیوست |
| f_media_files | path | VARCHAR(2048) | مسیر فایل |
| f_media_files | thumbnail_path | VARCHAR(2048) | مسیر تصویر کوچک |
| f_tracking_user_consents | user_agent | VARCHAR(2048) | شناسهٔ فنی مرورگر |
| f_tracking_user_sessions | user_agent | VARCHAR(2048) | شناسهٔ فنی مرورگر |
| f_user_certificates | certificate_url | VARCHAR(2048) | نشانی گواهی |
| f_user_certificates | file_path | VARCHAR(2048) | مسیر گواهی |
| f_user_publications | url | VARCHAR(2048) | نشانی انتشار |

پیش از تبدیل، طول داده کنترل شد؛ کوتاه‌سازی انجام نشد. هر ۸۶ نام غیرخالی نویسندهٔ کامنت حفظ شد. اعتبارسنجی طول نشانی انتشار و مسیر گواهی نیز در کد اضافه شد.

### ۱۲ ستون منتقل‌شده به قرارداد ترجمه

| جدول قبلی | ستون | محل ذخیرهٔ جدید |
|---|---|---|
| f_conversation_messages | body | f_translations |
| f_social_comments | body | f_translations |
| f_social_posts | body | f_translations |
| f_social_profiles | bio | f_translations |
| f_user_merges | reason | f_translations |
| f_user_merges | admin_note | f_translations |
| f_user_point_rules | description | f_translations |
| f_user_publications | content | f_translations |
| p_creator_courses | description | p_translations |
| p_creator_course_questions | body | p_translations |
| p_creator_course_reports | body | p_translations |
| p_creator_course_reviews | body | p_translations |

ستون‌های قبلی پس از کنترل برابری متن حذف شدند. شناسهٔ موجودیت و نام فیلد در جدول ترجمه، نام منطقی بدون پیشوند است؛ مثلاً `conversation_messages / body`. در دادهٔ واقعیِ باقی‌مانده فقط **یک مقدار غیرخالی**، مربوط به توضیح قانون امتیاز، برای انتقال وجود داشت. این مقدار حفظ شد؛ سایر ۱۱ فیلد دادهٔ غیرخالی نداشتند. این اصلاح، مسیر ذخیره و خواندن داده‌های آینده را هم پوشش می‌دهد. تعداد ترجمه‌های فریمورک اکنون ۴۲۲۳ و ترجمه‌های پروژه ۴۷۴۹ است.

خواندن و نوشتن پیام، نظر، پروفایل، دوره، ادغام حساب، قوانین امتیاز و انتشار کاربر با ساختار جدید تطبیق داده شد. قرارداد فیلدهای خروجی API مانند `body` و `description` حفظ شده است. ثبت ردیف و ترجمه اتمی است؛ قفل ردیف والد از ثبت تکراری هنگام نوشتن هم‌زمان جلوگیری می‌کند. زبان فارسی/انگلیسی و نسخهٔ فعال ترجمه رعایت می‌شود؛ ترجمهٔ ماشینی یا متن فرضی ساخته نشد.

متن خصوصی پیام‌ها همچنان از مسیر دارای کنترل دسترسی گفتگو خوانده می‌شود. مسیرهای مستقیم مدیریت ترجمهٔ پروژه به مدیر سایت محدود شدند و عملیات نوشتنی CSRF دارند.

### ۱۱ ستون فنی با نوع BLOB یا LONGBLOB

| جدول | ستون | نوع نهایی |
|---|---|---|
| f_legacy_settings | value | LONGBLOB |
| f_social_account_settings | settings_json | BLOB |
| f_tracking_user_events | event_data | LONGBLOB |
| f_tracking_user_page_views | query_params | LONGBLOB |
| f_user_points | metadata | LONGBLOB |
| f_user_settings | value | BLOB |
| p_creator_courses | curriculum | LONGBLOB |
| p_creator_course_details | metadata | LONGBLOB |
| p_creator_course_lessons | media_json | BLOB |
| p_music_sheets | metadata | LONGBLOB |
| p_music_sheets | score | LONGBLOB |

این داده‌ها تنظیمات، JSON یا ساختار نت هستند؛ انتقال آن‌ها به ترجمه درست نیست. محتوای UTF-8 سریال‌شده بدون تغییر بایت و با ظرفیت متناظر قبلی نگهداری شد. در MariaDB محیط فعلی، نوع JSON در نهایت LONGTEXT است؛ برای رعایت شرط صریح نبودن ستون TEXT از ذخیرهٔ باینری استفاده شد. برای ۱۰ فیلد JSON، کنترل اعتبار JSON با تبدیل صریح به رشته وجود دارد؛ `f_user_settings.value` الزام JSON ندارد. جست‌وجوهای JSON مربوط نیز با نوع جدید هماهنگ شدند. پنج تنظیم واقعی `f_legacy_settings` حفظ شدند؛ این جدول هنوز کاربرد دارد.

### یک ستون بدون مصرف حذف شد

`f_posts.pinged` مربوط به نشانی‌های قدیمی trackback بود و در مسیر اجرایی فعلی مصرف نداشت. ستون همراه با **۸۷ مقدار غیرخالی** حذف شد. دیگر فیلدهای تمام پست‌ها و همهٔ فیلدهای کامنت‌ها با وضعیت پیش از migration تطبیق داده شدند. خالی‌بودن جدول به‌تنهایی مبنای حذف فیلد یا جدول قرار نگرفت.

جزئیات طول و تعداد مقدار هر ۳۳ ستون در [CSV تغییرات](text-storage-changes-2026-10-06.csv) است. ستون `old_type` در این CSV نوع هنگام **ادامهٔ اجرای نیمه‌تمام** را نشان می‌دهد؛ برخی تبدیل‌ها پیش از این ادامه انجام شده بودند. فهرست انواع اولیه در [گزارش تاریخی ۳۳ ستون](text-columns-detail-2026-10-05.fa.md) محفوظ است.

## نسخهٔ آغازین جدید

خروجی تازه در یک دیتابیس جدا پاک‌سازی شد؛ فعالیت جدید محیط محلی وارد نسخهٔ آغازین نشد. فایل واقعی SQL دوباره در دیتابیس دیگری وارد و بررسی شد: ۱۲۰ جدول، ۳۴ رابطهٔ خارجی معتبر، ۴۵۶ پست، ۹۲ کامنت، فقط کاربر مدیر ۱، مشخصات ورود یکسان و صفر ستون TEXT خارج از ترجمه. شمارش همهٔ جدول‌ها برابر خروجی بود. دیتابیس‌های آزمایشی حذف شدند.

فایل خصوصی قابل واردسازی در **دیتابیس جدید و خالی**:

`storage/backups/text-storage/initial-20261006-f8fa5af6/initial/initial-database.sql`

حجم: `13938319` بایت؛ SHA-256:

`1240c964566fb07433afb48d1a49c341a0c4fbb71a55807821862d635825e1f0`

[شمارش دقیق ۱۲۰ جدول نسخهٔ آغازین](initial-version-table-counts-2026-10-06.csv). دو مسیر قبلی فایل آغازین نیز با همین خروجی به‌روز شدند؛ نسخهٔ قبلی با پسوند `superseded-before-text-migration.sql` فقط پشتیبان خصوصی است. SQL شامل هش رمز مدیر و مشخصات تاریخی کامنت‌هاست؛ در Git یا مسیر عمومی منتشر نشود.

## فایل‌های انتشار و migration

فایل‌های اجرایی این مرحله:

- `config/translated-fields.php` و `config/text-storage-migration.php`؛ نگاشت موجود `config/table-names.php` نیز لازم است.
- `core/translation/EntityText.php`.
- `Modules/CourseMarket/Repositories/CourseRepository.php`، `Modules/CourseMarket/Services/CourseService.php` و `CourseExperienceService.php`.
- `Modules/Social/Services/SocialService.php` و `SocialInteractionService.php`.
- `Modules/Analytics/Services/ChatService.php`، `UserMergeService.php` و `UserPointService.php`.
- `Modules/Analytics/Services/AdminDashboardService.php` و `AdminProfileContentService.php`.
- `Modules/Translation/Routes/web.php` و `api.php`.
- `scripts/db-text-storage-cli.php`، `scripts/lib/TextStorageMigration.php` و وابستگی موجود `scripts/lib/DatabaseHardeningMigration.php` برای اجرای مدیریتی migration.
- تعریف‌های تازهٔ `Modules/Social/schema.sql`، `comments-schema.sql`، `profile-schema.sql`، `Modules/CourseMarket/schema.sql`، `course-experience-schema.sql` و `Modules/Notation/schema.sql`.

کلاس جدید باید در autoload همان انتشار حاضر باشد؛ روی نسخهٔ PHP سازگار `composer dump-autoload --no-scripts` اجرا شود. کتابخانهٔ تازه اضافه نشد. ابزار آزمون `scripts/db-text-storage-mysql-test.php` و `scripts/lib/TranslatedTextFixture.php` جزو الزام اجرای وب نیستند.

برای دیتابیس موجود، ابتدا پشتیبان کامل و توقف نوشتن؛ سپس انتقال هم‌زمان کد و تنظیمات بالا و اجرای CLI با اتصال مقصدِ کنترل‌شده از متغیرهای `SORNAZ_TEXT_DSN`، `SORNAZ_TEXT_USER` و `SORNAZ_TEXT_PASSWORD`. حالت عادی فقط گزارش است؛ اعمال با `--apply --maintenance` انجام می‌شود. DDL در درخواست وب اجرا نمی‌شود. روی سرور اصلی در این کار چیزی اجرا نشده است.

DDL دارای commit ضمنی است؛ بازگشت تراکنشی کل migration ادعا نمی‌شود. پیش‌بررسی، مقایسهٔ بایتی، پشتیبان و ادامهٔ امن اجرای نیمه‌تمام وجود دارد. برای بازگشت، **پشتیبان کامل پیش از اولین تبدیل** در `storage/backups/text-storage/20261005-172755-2a54972b/before.sql` را در دیتابیس جدا و خالی بازیابی و همراه کد قبلی به آن متصل کنید. پشتیبان اجرای ادامه در `storage/backups/text-storage/20261006-142220-cdab67e1/before.sql` ساختار میانی دارد و جای پشتیبان اولیه نیست. روی دادهٔ تازهٔ محصول فعال، فایل آغازین یا پشتیبان قدیمی وارد نشود.

پس از انتقال روی سرور تست: ورود مدیر، نمایش و ویرایش دوره و نظر، ارسال و ویرایش پیام فارسی/انگلیسی، رد دسترسی کاربر دیگر به گفتگو، و رد مهمان از مسیر مدیریت ترجمه بررسی شود. مسیر خصوصی SQL باید از وب مسدود بماند.

## آزمون‌ها و محدودیت‌ها

- ۴۵ بررسی MySQL واقعی روی دیتابیس موقت: انتقال متن، حفظ بایت‌ها، طول بیش از حد، تعارض ترجمه، تکرار عملیات و بازگشت ثبت ردیف/ترجمه موفق بود؛ پس از migration واقعی نیز دوباره اجرا شد.
- مجموعهٔ انتشار شامل آزمون مرورگر: **۳۱ از ۳۱ مجموعه موفق**؛ شامل ۴۱ بررسی گفتگو، ۱۷ بررسی رشتهٔ کامنت و آزمون‌های دسترسی، CSRF و محتوای خصوصی.
- بازیابی **خود خروجی واقعی** و مقایسهٔ تمام جدول‌ها، حساب مدیر و روابط موفق بود.
- بررسی نگهداشت‌پذیری هنوز موفق نیست: روش‌های بزرگ موجود در آموزشگاه و مدیریت، و چند روش تغییرکردهٔ مدیریت پروفایل و بازار دوره از حد تعیین‌شده فراترند. این نتیجه جدا از آزمون رفتاری موفق گزارش می‌شود؛ بازآرایی گسترده در این migration انجام نشد.
- بررسی نحوی فایل‌های PHP تغییرکرده و `git diff --check` موفق بود.
- در جریان ادامه، جدول سیستمی محلی `mysql.db` با پشتیبان خصوصی تعمیر شد؛ بررسی آن موفق و MySQL با احراز هویت عادی دوباره راه‌اندازی شد. تنظیمات ورود برنامه تغییر نکرد. اطلاعات این تعمیر جزو بستهٔ انتشار نیست.

این مرحله قانون **نوع ستون‌های دیتابیس** را برای این ۳۳ مورد کامل می‌کند. متن‌های نمایشی مستقیم در فایل‌های PHP/JavaScript هنوز موضوع گزارش قبلی هستند؛ ادعا نمی‌شود که تمام متن‌های رابط سایت اکنون از دیتابیس خوانده می‌شوند. درگاه، پیامک، ایمیل و سرور آنلاین در این مرحله تغییر یا آزمون نشدند.
