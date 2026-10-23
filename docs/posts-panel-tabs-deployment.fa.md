# تغییر منوی نوشته‌ها و حذف مرکز تست‌ها

## آخرین وضعیت؛ جایگزین دستورهای قدیمی این سند

تغییرات جدیدتر تب‌های سوابق/گالری/سازمان/دسترسی، عنوان ثابت هدر نوشته‌ها و تغییر وضعیت جدول در [راهنمای فضاهای کاری پنل](panel-workspaces-deployment.fa.md) آمده‌اند؛ در این موارد راهنمای جدید مقدم است.

### صفحه‌بندی ثابت و چیدمان فیلترها

جدول‌های دسته‌بندی نوشته‌ها، نوشته‌ها، رسانه‌ها، دیدگاه‌ها و گزارش‌ها از رندر مشترک جدول و تابع مشترک صفحه‌بندی استفاده می‌کنند. هر صفحه ۱۰ سطر دارد؛ انتخاب تعداد سطر حذف شده و کل بخش صفحه‌بندی برای ۱۰ نتیجه یا کمتر پنهان است. در نتایج بیشتر، دکمه‌های اول، قبلی، شماره‌ها، بعدی و آخر نمایش داده می‌شوند. دسته‌بندی‌ها پیش از صفحه‌بندی روی کل نتیجه مرتب می‌شوند. حالت کارتی رسانه‌ها و دیدگاه‌ها نیز همان اندازهٔ صفحه را دارد.

جستجو در ابتدای سطر فیلتر رسانه‌ها و گالری نمودارها قرار دارد و فیلترهای بعدی در همان سطر دسکتاپ و بعد از آن هستند. چیدمان در نمایش کوچک مانند نوشته‌ها واکنش‌گرا است. رسانه‌ها از همان قالب کلید نمایش کارتی/جدولی دیدگاه‌ها استفاده می‌کنند.

فایل جدید Modules/Analytics/Resources/Views/sections/panel-table-pagination.php باید پیش از قالب‌های وابسته آپلود شود. فایل‌های این مرحله: قالب‌های posts.php، post-categories.php، media.php، comments.php، reports.php و chart-gallery.php و اسکریپت‌های posts.js، post-categories.js، media.js، comments.js و reports.js در مسیرهای قبلی همین راهنما. ابتدا اسکریپت‌ها و سپس قالب‌ها منتقل شوند؛ تعویض اتمی نسخه ترجیح دارد. handlerهای قبلی انتخاب اندازه برای سازگاری قالب‌های در حال اجرا حفظ شده‌اند ولی اندازه را تغییر نمی‌دهند. برای بازگشت همین فایل‌ها به نسخهٔ پیشین برگردند. migration و تغییر قرارداد API لازم نیست؛ مصرف‌کنندگان قدیمی API همچنان قرارداد اندازهٔ صفحهٔ خود را دارند.

آزمون scripts/posts_workspace_pagination_browser_test.cjs در فارسی و انگلیسی، آستانه‌های ۰/۱۰/۱۱/۲۵، پیمایش اول/آخر، مرتب‌سازی کامل دسته‌ها، حذف انتخاب اندازه، سطر فیلتر و تعویض نمایش بدون درخواست اضافه را بررسی می‌کند. پس از آپلود همین موارد با دادهٔ ساختگی و یک مسیر رد دسترسی روی سرور تست بررسی شوند. فایل‌های آزمون جزو بستهٔ عادی وب نیستند.

اعتبارسنجی نهایی این مجموعه: آزمون‌های اختصاصی صفحه‌بندی، ویرایشگر، تب‌ها، دیدگاه‌ها، بارگذاری HTTP واقعی با PNG/JPG/WebP و محدودسازی رسانه‌ها با SQLite موقت موفق‌اند. آزمون چهار تب اپلیکیشن نیز موفق است. آخرین اجرای مجموعهٔ کامل انتشار با مرورگر ۹۴ از ۹۸ آزمون موفق دارد؛ چهار شکست قبلی در mobile_panel_parity_test.php، mobile_panel_dialog_test.php، mobile_panel_presentation_test.php و term_batch_browser_test.cjs باقی مانده‌اند و نسخه به‌عنوان انتشار کاملاً سبز تأیید نشده است. بررسی نحوی فایل‌های تغییریافته و بررسی فاصله‌های diff پیش از تکمیل راهنما موفق بودند. بررسی MySQL و سرور اصلی انجام نشده است.

منوی نوشته‌ها چهار تب با ترتیب «دسته‌بندی نوشته‌ها، نوشته‌ها، رسانه‌ها، دیدگاه‌ها» دارد. منوهای مستقل رسانه و دیدگاه و بخش مدیریت برگه‌ها حذف شده‌اند. صفحات عمومی و محتوای ذخیره‌شدهٔ آن‌ها حفظ می‌شوند.

دکمهٔ بازگشت به لیست داخل ویرایشگر است. تصویر شاخص از انتخاب فایل PNG/JPG/WebP و مسیر دارای site-admin و CSRF بارگذاری می‌شود؛ پسوند، MIME واقعی و ساختار تصویر در سرور کنترل می‌شوند. ذخیره و پیش‌نمایش منتظر پایان بارگذاری می‌مانند و شکست، تصویر قبلی را حفظ می‌کند.

دیدگاه‌ها از کلید مشترک نمایش مدیریت شعبه‌ها، حالت کارتی/جدولی و فیلتر وضعیت رنگی استفاده می‌کنند؛ انتخاب نوشته حذف شده است. رسانه‌ها فقط تصاویر شاخص و تصاویر داخل متن نوشته‌ها را نشان می‌دهند؛ تصاویر گالری، حساب کاربری، برگهٔ عمومی و فایل بدون استفاده نمایش داده نمی‌شوند. شمارش و ماه‌ها نیز همین محدوده را دارند. فایل‌ها و رکوردهای دیگر حذف نمی‌شوند. آپلود عمومی نشانی قابل کپی برای درج در متن نشان می‌دهد؛ تصویر پس از ذخیرهٔ نوشته در فهرست رسانه‌ها ظاهر می‌شود.

علاوه بر فایل‌های بخش نوشته‌ها و هدر در فهرست پیشین، این فایل‌ها باید از نسخهٔ فعلی منتقل شوند:

- Modules/Analytics/Controllers/Web/AdminMediaController.php
- Modules/Analytics/Controllers/Web/SitePageContentController.php
- Modules/Analytics/Services/AdminMediaService.php
- Modules/Analytics/Services/AdminCommentService.php
- Modules/Analytics/Services/AdminPostService.php
- Modules/Analytics/Services/MobilePanelCatalog.php
- Modules/Analytics/Services/SitePageContentService.php
- Modules/Analytics/Routes/routes.php
- Modules/Analytics/Routes/--web.php
- Modules/Analytics/Routes/----------web.php
- Modules/Analytics/Resources/mobile-panel-presentation.php
- Modules/Analytics/Resources/panel-page-guidance.php
- Modules/Analytics/Resources/Views/admin-panel.php
- Modules/Analytics/Resources/Views/public-ui-preview.php
- Modules/Analytics/Resources/Views/layouts/admin.php
- Modules/Analytics/Resources/Views/sections/sidebar.php
- Modules/Analytics/Resources/Views/sections/posts-page-tabs.php (جدید)
- Modules/Analytics/Resources/Views/sections/post-editor.php
- Modules/Analytics/Resources/Views/sections/media.php
- Modules/Analytics/Resources/Views/sections/comments.php
- assets/Analytics/js/media.js
- assets/Analytics/js/comments.js
- assets/theme/help-center.js

فایل‌های Modules/Analytics/Resources/Views/sections/pages.php و assets/Analytics/js/pages.js حذف شوند؛ دستورهای قدیمی آپلود این دو فایل در ادامهٔ سند دیگر معتبر نیستند. فایل عمومی Page::site-pages.js و منطق محتوای عمومی سایت حفظ شوند. scripts و fixtureها جزو فایل‌های زمان اجرای سایت نیستند و نباید روی ریشهٔ وب عمومی آپلود شوند.

تعویض اتمی نسخه ترجیح دارد. در انتشار مرحله‌ای ابتدا سرویس‌ها، سپس کنترلر و مسیر آپلود، سپس posts-page-tabs.php و اسکریپت مشترک جدول‌ها منتقل شوند. قالب و اسکریپت صفحه با هم تعویض شوند. همهٔ فراخوانی‌های بخش برگه‌ها پیش از حذف دو فایل قدیمی حذف شوند. درخواست‌های در حال اجرا باید بتوانند نسخهٔ قبلی را تا پایان بخوانند. برای بازگشت همین فایل‌ها و دو فایل حذف‌شده از پشتیبان همان نسخه بازیابی شوند؛ تنظیمات، رسانه و دیتابیس واقعی بازنویسی نشوند. کلاس، وابستگی یا migration تازه لازم نیست.

اپلیکیشن: lib/screens/Site/panel_navigation.dart و test/panel_page_tabs_test.dart ترتیب چهار تب و حذف ورودی‌های تکراری/برگه‌ها را هماهنگ می‌کنند؛ انتقال انتخابی ممکن است و وابستگی تازه‌ای به امکانات آزمایشی ندارند. APK ساخته نشده است.

پس از آپلود با نوشته و تصویر ساختگی، چهار تب، هر دو نمایش دیدگاه، جستجو/وضعیت/صفحه‌بندی/پاسخ دیدگاه، تصویر شاخص و تصویر متن و شمارش رسانه‌ها را بررسی کنید. گالری/حساب کاربری نباید در رسانه‌ها ظاهر شوند. PNG/JPG/WebP معتبر باید قبول و فایل تغییرپسوندی رد شود. بازگشت باید داخل ویرایشگر باشد. مسیر قدیمی admin-site-pages باید ۴۰۴ بدهد و صفحات عمومی باز شوند. کاربر عادی باید از فهرست رسانه و آپلود تصویر رد دسترسی شود. آزمون HTTP واقعی با فایل ساختگی و SQLite موقت انجام شده است؛ این نتیجه تأیید MySQL و محدودیت آپلود هاست نیست. انتشار یا نوشتن روی سرور اصلی انجام نشده است.

## اصلاح عنوان، جدول و سرعت نوشته‌ها

هدر هر صفحه از عنوان تب فعال خوانده می‌شود؛ عنوان و توضیح تکراری داخل صفحه حذف شده است. فیلتر وضعیت از رنگ اصلی تم استفاده می‌کند. شناسه‌های جدول در همان رندر پاسخ API تکمیل می‌شوند و پاسخ یکسان دیگر جدول را بازسازی نمی‌کند.

درخواست‌های هم‌زمان یکسان ادغام می‌شوند؛ فیلترها و صفحات تا ۱۵ ثانیه و حداکثر ۲۰ نتیجه در حافظهٔ همان صفحه ذخیره می‌شوند. پاسخ دیرهنگام انتخاب قبلی نادیده گرفته می‌شود. تغییر نوشته یا دسته‌بندی، کش مرتبط را باطل می‌کند. این کش در مرورگر ماندگار نیست.

فایل‌های این اصلاح: دو قالب `sections/posts.php` و `sections/post-categories.php` و چهار فایل `assets/Analytics/js/{admin,admin-ui-templates,posts,post-categories}.js`. ابتدا `admin-ui-templates.js`، سپس دو اسکریپت نوشته‌ها، سپس `admin.js` و دو قالب آپلود شوند. برای بازگشت همین شش فایل از نسخهٔ قبل بازیابی شوند. migration یا تغییر قرارداد API لازم نیست. آزمون مرورگر `scripts/posts_panel_performance_browser_test.cjs` به مجموعهٔ انتشار افزوده شده است. پس از آپلود، عنوان دو تب، شناسه‌ها، رنگ فیلتر، تعویض سریع فیلتر و صفحه و تازه‌شدن نتیجه پس از ویرایش بررسی شود. سرعت روی دیتابیس و شبکهٔ سرور اصلی باید جداگانه سنجیده شود.

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

## Shared content tables and header actions

Posts, post categories, site pages and reports now render dynamic rows through `AdminPanelTable.setRows`, including empty and report error states. Report cell definitions remain reusable by sorting and exports. Existing filters, actions and pagination are preserved.

The Posts, Post categories and Media add buttons move into `#panelPageActions` next to Back to site. Only the active page action is shown; compact screens retain an accessible icon button. Internal headings and introductory descriptions are removed from Pages, Media, Comments, Chart gallery and Reports. The global page title remains.

Additional upload files for this revision:
- `Modules/Analytics/Resources/Views/sections/header.php`
- `Modules/Analytics/Resources/Views/sections/pages.php`
- `Modules/Analytics/Resources/Views/sections/media.php`
- `Modules/Analytics/Resources/Views/sections/comments.php`
- `Modules/Analytics/Resources/Views/sections/chart-gallery.php`
- `Modules/Analytics/Resources/Views/sections/reports.php`
- `assets/Analytics/js/pages.js`
- `assets/Analytics/js/report-templates.js`
- `assets/Analytics/js/reports.js`

Upload `report-templates.js` before `reports.js`, and `header.php` before the updated action-bearing templates. Keep the shared table script before page scripts. Restore these files together with the preceding six files to roll back. No new class, dependency, migration or API change is required. After upload, verify the three header actions, global titles, four table bodies, sorting, empty results, report pagination and exports on desktop/mobile in both languages. Server deployment and authenticated live smoke checks remain pending.

Regression coverage: `scripts/panel_content_tables_browser_test.cjs`, `scripts/posts_panel_performance_browser_test.cjs`, and the existing shared table, sorting, header and report-source suites.

Validation for this revision: PHP lint, JavaScript syntax checks, diff whitespace checks, asset checksums, and the focused content-table/header/post-performance/sorting/report-source tests passed. The full release command completed with 90/94 suites passing. Failures: mobile_panel_parity_test.php, mobile_panel_dialog_test.php and mobile_panel_presentation_test.php report an unmapped POST /academy/admin/terms/{id}/set-status operation; term_batch_browser_test.cjs expects classroom option 1001 but receives no options. These are outside the content-table and header changes. Live deployment and authenticated server checks have not been performed.

## Post editor revision

The editor has one publication box with Draft, Save publication status and Preview actions. Saving publication status retains the selected status instead of forcing published. The trash action and duplicate top actions are removed. Back to list uses the global header action slot. Summary and description use five and ten rows.

Categories and related posts use independent checkbox selections; related posts are searched by title. Existing unavailable selections survive option-loading failures. Related-post options use the authenticated, opt-in GET /analytics/admin-posts?editorOptions=1 response containing only IDs and titles. Existing list/detail responses are unchanged. Unsaved preview reuses the sandboxed preview renderer. The featured-image URL updates a rectangular preview; the media ID input is removed while existing metadata is preserved until the URL changes.

Upload files: Modules/Analytics/Services/AdminPostService.php, assets/Analytics/js/posts.js and Modules/Analytics/Resources/Views/sections/post-editor.php. Upload the service first, then switch the JavaScript and template together (prefer an atomic release); keep the existing header/action-slot and shared scripts from this revision. Asset URLs use file timestamps. No migration, new class or dependency is required. Restore these three files together to roll back; stored post data is unchanged by deployment.

Focused checks: scripts/post_editor_options_test.php uses an isolated SQLite fixture for lightweight titles and denied access; scripts/post_editor_browser_test.cjs checks selected-status/draft payloads, duplicate-save suppression, multiple categories/related posts, image preview, metadata retention, safe unsaved preview, failed-option preservation and the header action in fa/en. These fixtures do not establish live MySQL/network success. After upload, test these operations with a test post and confirm a non-admin receives denied access to editorOptions. No production write test or deployment was performed.
