# گزارش وضعیت دیتابیس و دسته‌بندی جدول‌ها

تاریخ: ۲۰۲۶-۱۰-۰۳. منبع: دیتابیس تنظیم‌شده محیط فعلی؛ وضعیت هاست اصلی مستقلاً تأیید نشده است. این کار فقط خواندنی بود؛ هیچ جدول یا داده‌ای تغییر نکرد.

## نتیجه

۱۳۳ جدول موجود، همگی InnoDB؛ 87 جدول فریمورک و 46 جدول پروژه. برای ۱۱۸ جدول ارجاع در کد پیدا شد. ۱۵ جدول ارجاع اجرایی ندارند: ۸ نامزد مستقل مشروط، ۶ جدول جغرافیایی وابسته و ۱ نگاشت مهاجرت. تعداد ردیف‌ها برآورد information_schema است؛ عدد صفر اثبات خالی‌بودن نیست.

## معیار دو دسته

فریمورک طبق تعریف کاربر شامل زیرساخت‌ها و قابلیت‌های عمومی قابل استفاده مجدد است: users و پروفایل عمومی، دسترسی، احراز هویت، تنظیمات، ترجمه، محتوا، رسانه، پیام‌رسانی، شبکه اجتماعی، رهگیری، جغرافیا و زیرساخت مالی. این دسته‌بندی به معنی استقلال فعلی پیاده‌سازی از سرناز نیست؛ مثلاً مالی و نقش‌ها وابستگی‌هایی به فاکتور شهریه و آموزشگاه دارند که برای انتقال به پروژه دیگر باید جدا شوند.

پروژه شامل آموزشگاه، شعبه، دوره، ترم، شهریه، بازار دوره، نت، ساز و سوابق موسیقی و نگاشت مهاجرت اختصاصی سرناز است. social_lesson_progress به دلیل اتصال به درس آموزشی پروژه محسوب شده؛ سایر social_* عمومی‌اند. نام‌گذاری بر اساس مفهوم است، نه صرفاً پیشوند. هیچ تغییر نام فیزیکی انجام نشده است.

## نامزدهای مستقل حذف مشروط

هیچ ارجاع اجرایی، مدل یا ابزار و هیچ FK ورودی/خروجی برای این جدول‌ها پیدا نشد:

| جدول | ردیف برآوردی |
|---|---:|
| financial_system_ledger_entries | 0 |
| financial_system_refunds | 0 |
| system_events | 0 |
| user_verifications | 0 |
| z_world_iran_cities | 1659 |
| z_world_iran_cities_filtered | 1195 |
| z_world_iran_districts | 1184 |
| z_world_iran_rurals | 1524 |

این فهرست تضمین حذف بی‌خطر نیست. financial_system_ledger_entries و financial_system_refunds ممکن است سوابق مالی تاریخی داشته باشند؛ نبود ارجاع فعلی مجوز پاک‌کردن آن سوابق نیست. پیش از حذف، COUNT(*) و داده واقعی در کپی پشتیبان بررسی شود. داده مرجع جغرافیایی بایگانی شود. cronها، ابزارهای خارج مخزن و گزارش‌های هاست بررسی نشده‌اند.

## شش جدول وابسته جغرافیایی

z_world_postcodes، z_world_cities، z_world_states، z_world_countries، z_world_subregions و z_world_regions در کد ارجاع ندارند ولی FK دارند. اگر کل این مجموعه حذف شود، ترتیب مطابق FK موجود: postcodes → cities → states → countries → subregions → regions، همگی با پیشوند z_world_. خاموش‌کردن FOREIGN_KEY_CHECKS لازم نیست. جزئیات FK پایین گزارش آمده است.

world_iran_provinces و world_iran_counties در ثبت‌نام، مدیریت شعب و دوره‌ها استفاده می‌شوند؛ حفظ شوند. ماژول World به worlds اشاره دارد و مصرف‌کننده این شش جدول نیست.

## نگاشت مهاجرت

legacy_import_map ارجاع اجرایی وب ندارد، اما docs/database/import-wordpress-content و docs/database/verify-wordpress-content از آن استفاده می‌کنند. برآورد ۵۳۰ ردیف دارد. حذف آن تطبیق شناسه وردپرس/مقصد و تکرار واردکردن داده را تحت تأثیر قرار می‌دهد؛ تا پایان مهاجرت و بایگانی نگاشت حفظ شود.

## جدول‌های مفقود نسبت به کد

z_user_settings در دیتابیس وجود ندارد، اما Modules/Analytics/Services/AdminAccountService.php:274,282,285,287 و Modules/System/Services/UserService.php:176 آن را می‌خوانند/می‌نویسند. تنظیمات حساب و حریم خصوصی نمایش اطلاعات تماس در معرض خطا هستند. از روی این بررسی نمی‌توان گفت اخیراً حذف شده یا از ابتدا غایب بوده است. پیش از حذف بیشتر، ساختار پشتیبان را با قرارداد کد تطبیق دهید؛ هیچ جدول خودکار بازسازی نشده است.

ارجاع‌های دیگر به جدول‌های غایب در مدل/Repositoryهای اولیه: academys، cmss، communications، educations، enrollments، finances، homes، medias، pages، profiles، systems و worlds. این نام‌ها غالباً قالب اولیه‌اند؛ مسیرشان باید جدا بررسی شود. وجود نامشان اثبات حذف اخیر یا دلیل ساخت خودکار جدول نیست.

## روش و حدود اعتبار

فهرست فعلی از information_schema و تطبیق نام کامل جدول در PHP/JS، مدل، Repository، مسیر، هسته، تنظیمات، SQL و ابزار PHP بدون پسوند تهیه شد. مسیرهای table متغیر در پروفایل، دسترسی، رهگیری و پشتیبان آموزشگاه بررسی شدند؛ فهرست‌های ثابتشان در شواهد لحاظ شده است. شماره خطوط شامل تغییرات ثبت‌نشده قبلی است. ارجاع کد اثبات استفاده واقعی در همه مسیرها نیست و ممکن است فقط ثابت یا ابزار مدیریت باشد؛ بنابراین ۱۱۸ جدول دارای ارجاع را صرفاً به دلیل خالی‌بودن حذف نکنید.

view، trigger، routine و event در metadata قابل مشاهده یافت نشد؛ خطاهای metadata: 0. دیدپذیری تابع مجوز حساب دیتابیس است. این ممیزی ایستا است و تضمین پوشش نام‌های ساخته‌شده در زمان اجرا، کد خارجی و ارتباط‌های منطقی بدون FK نیست. اجرای تک‌تک قابلیت‌ها انجام نشده؛ snapshotهای قدیمی SQL موجودی امروز محسوب نشده‌اند و رکوردهای خصوصی صادر نشده‌اند.

## حذف و بازگشت

۱. فهرست مقصد را دوباره تطبیق دهید و از ساختار و داده جدول‌های هدف پشتیبان خصوصی بگیرید.
۲. حذف منتخب را ابتدا روی نسخه تست بررسی کنید. DROP TABLE در MySQL با rollback عادی برنمی‌گردد؛ بازگشت از پشتیبان است.
۳. ورود، تنظیمات حساب، پروفایل و حریم خصوصی تماس، ثبت‌نام آموزشگاه، شعبه و استان/شهرستان، دوره/ترم، پرداخت و callback تکراری، شبکه اجتماعی و پیام‌رسانی را بررسی کنید؛ مسیر رد دسترسی آموزشگاه/کاربر دیگر نیز آزموده شود.
۴. حذف روی مقصد پس از موفقیت تست و درخواست صریح انتشار انجام شود. در این کار SQL حذف ارائه یا اجرا نشده است.

## فایل‌ها و بازتولید

گزارش Markdown و CSV برای مرور هستند. scripts/database-code-audit.cjs تحلیلگر ایستا است و snapshot محلی metadata را از docs/database/current-inventory.local.json می‌خواند. برای بازتولید گزارش همین snapshot، node scripts/database-code-audit.cjs را اجرا کنید. دریافت snapshot جدید به اتصال خواندنی به information_schema نیاز دارد؛ ابزار دریافت snapshot در فایل‌های تحویلی موجود نیست. فایل‌های *.local.json محلی‌اند و جزو بسته انتشار نیستند.

تغییر برنامه، کلاس جدید، وابستگی یا migration وجود ندارد؛ آپلود به هاست لازم نیست. بازگشت این کار کنارگذاشتن فایل‌های گزارش و ابزار است؛ دیتابیس تغییر نکرده. کنترل syntax تحلیلگر و git diff --check انجام شد؛ تست انتشار کامل برای گزارش صرف اجرا نشده است.

## فهرست کامل دو دسته

### جدول‌های فریمورک

| جدول | وضعیت | ردیف برآوردی | نمونه شاهد کد |
|---|---|---:|---|
| access_system_permissions | ارجاع کد؛ حفظ شود | 311 | Modules/Academy/Services/AcademyClassroomService.php:42؛ Modules/Academy/Services/AcademyClassroomService.php:46؛ Modules/Academy/Services/AcademyClassroomService.php:49 |
| access_system_roles | ارجاع کد؛ حفظ شود | 115 | Modules/Academy/Controllers/Web/AcademyTermController.php:185؛ Modules/Academy/Controllers/Web/AcademyTermController.php:186؛ Modules/Academy/Controllers/Web/AcademyTermController.php:187 |
| access_system_role_permissions | ارجاع کد؛ حفظ شود | 3245 | Modules/Analytics/Services/AdminAccessCatalogService.php:14؛ Modules/Analytics/Services/AdminAccessCatalogService.php:81؛ Modules/Analytics/Services/AdminAccessCatalogService.php:92 |
| access_system_setting_permissions | ارجاع کد؛ حفظ شود | 523 | Modules/System/Services/AccessControl.php:24 |
| article_comment_receipts | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/ArticleApiService.php:171؛ Modules/Analytics/Services/ArticleApiService.php:184؛ Modules/Analytics/Services/ArticleApiService.php:194 |
| auth_rate_limits | ارجاع کد؛ حفظ شود | 24 | Modules/System/Services/AccountSecurityStore.php:12؛ Modules/System/Services/AccountSecurityStore.php:13؛ Modules/System/Services/AccountSecurityStore.php:15 |
| auth_remember_tokens | ارجاع کد؛ حفظ شود | 0 | Modules/System/Services/AccountSecurityStore.php:26؛ Modules/System/Services/AccountSecurityStore.php:27؛ Modules/System/Services/AccountSecurityStore.php:33 |
| categories | ارجاع کد؛ حفظ شود | 9 | Modules/Academy/Routes/web.php:63؛ Modules/Academy/Routes/web.php:64؛ Modules/Academy/Routes/web.php:65 |
| comments | ارجاع کد؛ حفظ شود | 92 | Modules/Analytics/Controllers/Api/ArticleController.php:63؛ Modules/Analytics/Controllers/Api/ArticleController.php:65؛ Modules/Analytics/Controllers/Api/ArticleController.php:90 |
| conversations | ارجاع کد؛ حفظ شود | 9 | Modules/Academy/Services/PublicAcademyEnrollmentService.php:178؛ Modules/Academy/Services/PublicAcademyEnrollmentService.php:183؛ Modules/Academy/Services/PublicAcademyEnrollmentService.php:192 |
| conversation_members | ارجاع کد؛ حفظ شود | 54 | Modules/Academy/Services/PublicAcademyEnrollmentService.php:136؛ Modules/Academy/Services/PublicAcademyEnrollmentService.php:180؛ Modules/Analytics/Services/ChatService.php:12 |
| conversation_messages | ارجاع کد؛ حفظ شود | 20 | Modules/Academy/Services/PublicAcademyEnrollmentService.php:182؛ Modules/Analytics/Services/AdminDashboardService.php:347؛ Modules/Analytics/Services/ChatService.php:23 |
| conversation_message_reactions | ارجاع کد؛ حفظ شود | 3 | Modules/Analytics/Services/ChatService.php:160؛ Modules/Analytics/Services/ChatService.php:354؛ Modules/Analytics/Services/ChatService.php:356 |
| financial_system_accounts | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyRegistrationService.php:53؛ Modules/Academy/Services/AcademyRegistrationService.php:283؛ Modules/Academy/Services/AcademyRegistrationService.php:344 |
| financial_system_currency | ارجاع کد؛ حفظ شود | 4 | Modules/Academy/Services/AcademyBranchService.php:558؛ Modules/Academy/Services/AcademyBranchService.php:563؛ Modules/Academy/Services/AcademyBranchService.php:664 |
| financial_system_discounts | ارجاع کد؛ حفظ شود | 1 | Modules/Academy/Services/AcademyTermService.php:399؛ Modules/Academy/Services/AcademyTermService.php:400؛ Modules/Academy/Services/AcademyTermService.php:1159 |
| financial_system_ledger_entries | نامزد حذف مشروط | 0 | — |
| financial_system_payments | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminDashboardService.php:46؛ Modules/Analytics/Services/UserPointService.php:168؛ Modules/Analytics/Services/UserPointService.php:169 |
| financial_system_refunds | نامزد حذف مشروط | 0 | — |
| financial_system_transactions | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/UserPointService.php:170 |
| f_settings | ارجاع کد؛ حفظ شود | 372 | Modules/Analytics/Services/AdminGuideService.php:157؛ Modules/Analytics/Services/AdminGuideService.php:161؛ Modules/Analytics/Services/AdminGuideService.php:177 |
| f_timezone | ارجاع کد؛ حفظ شود | 13 | Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php:40؛ Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php:47؛ Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php:84 |
| f_translations | ارجاع کد؛ حفظ شود | 4147 | Modules/Academy/Services/AcademyBranchOfferingService.php:47؛ Modules/Academy/Services/AcademyBranchService.php:579؛ Modules/Academy/Services/AcademyBranchService.php:581 |
| media_files | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyRegistrationService.php:939؛ Modules/Academy/Services/AcademyRegistrationService.php:947؛ Modules/Academy/Services/AcademyRegistrationService.php:970 |
| posts | ارجاع کد؛ حفظ شود | 456 | Modules/Analytics/Controllers/Web/AnalyticsController.php:17؛ Modules/Analytics/Controllers/Web/AnalyticsController.php:23؛ Modules/Analytics/Controllers/Web/AnalyticsController.php:29 |
| public_ratings | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/PublicRatingService.php:15؛ Modules/Analytics/Services/PublicRatingService.php:41؛ Modules/Analytics/Services/PublicRatingService.php:43 |
| settings | ارجاع کد؛ حفظ شود | 15 | Modules/Academy/Routes/web.php:83؛ Modules/Academy/Services/AcademyRegistrationService.php:1056؛ Modules/Academy/Services/NationalHolidayService.php:15 |
| social_account_settings | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/LearningService.php:119؛ Modules/Social/Services/LearningService.php:148؛ Modules/Social/Services/SocialService.php:53 |
| social_bookmarks | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:42؛ Modules/Social/Services/LearningService.php:34؛ Modules/Social/Services/LearningService.php:65 |
| social_comments | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialInteractionService.php:47؛ Modules/Social/Services/SocialInteractionService.php:65؛ Modules/Social/Services/SocialInteractionService.php:70 |
| social_comment_likes | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialInteractionService.php:45؛ Modules/Social/Services/SocialInteractionService.php:46؛ Modules/Social/Services/SocialInteractionService.php:90 |
| social_follows | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:44؛ Modules/Social/Services/SocialService.php:45؛ Modules/Social/Services/SocialService.php:48 |
| social_highlights | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:84؛ Modules/Social/Services/SocialService.php:89؛ Modules/Social/Services/SocialService.php:94 |
| social_highlight_stories | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:84؛ Modules/Social/Services/SocialService.php:94؛ Modules/Social/Services/SocialService.php:139 |
| social_media | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/ChatService.php:489؛ Modules/Social/Services/SocialService.php:74؛ Modules/Social/Services/SocialService.php:94 |
| social_notifications | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:369؛ Modules/Social/Services/SocialService.php:374؛ Modules/Social/Services/SocialService.php:385 |
| social_posts | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/ChatService.php:475؛ Modules/Social/Services/LearningService.php:25؛ Modules/Social/Services/SocialService.php:46 |
| social_profiles | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:30؛ Modules/Social/Services/SocialService.php:25؛ Modules/Social/Services/SocialService.php:163 |
| social_reactions | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:232؛ Modules/Social/Services/SocialService.php:254؛ Modules/Social/Services/SocialService.php:255 |
| social_story_mentions | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/SocialService.php:262؛ Modules/Social/Services/SocialService.php:319؛ scripts/chat_media_revision_test.php:32 |
| system_events | نامزد حذف مشروط | 0 | — |
| tracking_ingestion_batches | ارجاع کد؛ حفظ شود | 58779 | Modules/Analytics/Services/AdminTrackingService.php:94؛ Modules/Analytics/Services/UserPointService.php:74؛ Modules/System/Services/UserTrackingService.php:31 |
| tracking_user_activity_intervals | ارجاع کد؛ حفظ شود | 11022 | Modules/Analytics/Services/AdminTrackingService.php:29؛ Modules/Analytics/Services/AdminTrackingService.php:53؛ Modules/Analytics/Services/UserPointService.php:74 |
| tracking_user_consents | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminTrackingService.php:31؛ Modules/Analytics/Services/AdminTrackingService.php:53؛ docs/database/_extract_sync.php:233 |
| tracking_user_content_engagements | ارجاع کد؛ حفظ شود | 2065 | Modules/Analytics/Services/AdminTrackingService.php:25؛ Modules/Analytics/Services/AdminTrackingService.php:53؛ Modules/Analytics/Services/UserPointService.php:74 |
| tracking_user_events | ارجاع کد؛ حفظ شود | 39939 | Modules/Analytics/Services/AdminAccountService.php:266؛ Modules/Analytics/Services/AdminAccountService.php:306؛ Modules/Analytics/Services/AdminAccountService.php:308 |
| tracking_user_page_views | ارجاع کد؛ حفظ شود | 1646 | Modules/Analytics/Services/AdminAccountService.php:302؛ Modules/Analytics/Services/AdminTrackingService.php:19؛ Modules/Analytics/Services/AdminTrackingService.php:22 |
| tracking_user_sessions | ارجاع کد؛ حفظ شود | 126 | Modules/Analytics/Services/AdminAccountService.php:218؛ Modules/Analytics/Services/AdminAccountService.php:222؛ Modules/Analytics/Services/AdminAccountService.php:253 |
| translations | ارجاع کد؛ حفظ شود | 13702 | Modules/Academy/Resources/Views/layouts/main.php:34؛ Modules/Academy/Services/AcademyBranchOfferingService.php:21؛ Modules/Academy/Services/AcademyBranchOfferingService.php:24 |
| users | ارجاع کد؛ حفظ شود | 1 | Modules/Academy/Controllers/Web/AcademyRegistrationController.php:294؛ Modules/Academy/Controllers/Web/AcademyRegistrationController.php:310؛ Modules/Academy/Controllers/Web/AcademyTermController.php:199 |
| user_addresses | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:694؛ Modules/Academy/Services/AcademyBranchService.php:697؛ Modules/Academy/Services/AcademyBranchService.php:1393 |
| user_availabilities | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchOfferingService.php:146؛ Modules/Academy/Services/AcademyBranchOfferingService.php:152؛ Modules/Academy/Services/AcademyBranchOfferingService.php:290 |
| user_awards | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:11 |
| user_badges | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:12 |
| user_certificates | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:14 |
| user_contacts | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:1373؛ Modules/Academy/Services/AcademyBranchService.php:1377؛ Modules/Academy/Services/AcademyBranchService.php:1381 |
| user_educations | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:15 |
| user_events | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:16 |
| user_experiences | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:13 |
| user_merges | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminDashboardService.php:146؛ Modules/Analytics/Services/UserMergeService.php:15؛ Modules/Analytics/Services/UserMergeService.php:51 |
| user_messages | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/NationalHolidayService.php:111؛ Modules/Academy/Services/NationalHolidayService.php:134؛ Modules/Academy/Services/NationalHolidayService.php:137 |
| user_permissions | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminAccessCatalogService.php:87؛ Modules/Analytics/Services/AdminUserAccessService.php:59؛ Modules/Analytics/Services/AdminUserAccessService.php:181 |
| user_points | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Resources/Views/sections/header.php:22؛ Modules/Analytics/Services/AdminDashboardService.php:38؛ Modules/Analytics/Services/UserPointService.php:21 |
| user_point_rules | ارجاع کد؛ حفظ شود | 57 | Modules/Analytics/Services/UserPointService.php:20؛ Modules/Analytics/Services/UserPointService.php:44؛ Modules/Analytics/Services/UserPointService.php:59 |
| user_polls | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminProfileContentService.php:17؛ Modules/Analytics/Services/AdminProfileContentService.php:156؛ Modules/Analytics/Services/AdminProfileContentService.php:216 |
| user_poll_options | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminProfileContentService.php:167؛ Modules/Analytics/Services/AdminProfileContentService.php:189؛ Modules/Analytics/Services/AdminProfileContentService.php:190 |
| user_poll_votes | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AdminProfileContentService.php:171؛ Modules/Analytics/Services/AdminProfileContentService.php:174؛ Modules/Analytics/Services/AdminProfileContentService.php:177 |
| user_publications | ارجاع کد؛ حفظ شود | 1 | Modules/Analytics/Services/AdminProfileContentService.php:18 |
| user_referrals | ارجاع کد؛ حفظ شود | 1 | Modules/System/Controllers/Api/UserController.php:194؛ Modules/System/Controllers/Web/UserController.php:104؛ Modules/System/Services/UserReferralService.php:12 |
| user_roles | ارجاع کد؛ حفظ شود | 1 | Modules/Academy/Services/AcademyBranchService.php:821؛ Modules/Academy/Services/AcademyBranchService.php:974؛ Modules/Academy/Services/AcademyBranchService.php:977 |
| user_sessions | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyRegistrationService.php:285؛ Modules/Academy/Services/AcademyRegistrationService.php:346 |
| user_verifications | نامزد حذف مشروط | 0 | — |
| verification_levels | ارجاع کد؛ حفظ شود | 8 | Modules/Analytics/Services/AdminProfileContentService.php:60؛ Modules/Analytics/Services/AdminProfileContentService.php:61 |
| world_iran_counties | ارجاع کد؛ حفظ شود | 482 | Modules/Academy/Services/AcademyBranchService.php:48؛ Modules/Academy/Services/AcademyBranchService.php:84؛ Modules/Academy/Services/AcademyBranchService.php:696 |
| world_iran_provinces | ارجاع کد؛ حفظ شود | 31 | Modules/Academy/Services/AcademyBranchService.php:47؛ Modules/Academy/Services/AcademyBranchService.php:83؛ Modules/Academy/Services/AcademyBranchService.php:695 |
| z_settings | ارجاع کد؛ حفظ شود | 5 | Modules/Analytics/Controllers/Web/AdminSettingController.php:30؛ Modules/Analytics/Services/AdminSettingService.php:83؛ Modules/Analytics/Services/AdminSettingService.php:94 |
| z_user_profiles | ارجاع کد؛ حفظ شود | 1 | Modules/Academy/Services/AcademyRegistrationService.php:282؛ Modules/Academy/Services/AcademyRegistrationService.php:343؛ Modules/Academy/Services/AcademyRegistrationService.php:414 |
| z_world_cities | بدون ارجاع؛ حذف وابسته | 0 | — |
| z_world_countries | بدون ارجاع؛ حذف وابسته | 250 | — |
| z_world_iran_cities | نامزد حذف مشروط | 1659 | — |
| z_world_iran_cities_filtered | نامزد حذف مشروط | 1195 | — |
| z_world_iran_districts | نامزد حذف مشروط | 1184 | — |
| z_world_iran_rurals | نامزد حذف مشروط | 1524 | — |
| z_world_postcodes | بدون ارجاع؛ حذف وابسته | 0 | — |
| z_world_regions | بدون ارجاع؛ حذف وابسته | 6 | — |
| z_world_states | بدون ارجاع؛ حذف وابسته | 4934 | — |
| z_world_subregions | بدون ارجاع؛ حذف وابسته | 22 | — |

### جدول‌های پروژه

| جدول | وضعیت | ردیف برآوردی | نمونه شاهد کد |
|---|---|---:|---|
| academies | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Api/RegistrationController.php:104؛ Modules/Academy/Controllers/Web/AcademyRegistrationController.php:63؛ Modules/Academy/Controllers/Web/AcademyRegistrationController.php:113 |
| academy_branches | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermController.php:28؛ Modules/Academy/Middleware/AcademyPanelMiddleware.php:49؛ Modules/Academy/Services/AcademyBranchOfferingService.php:21 |
| academy_branch_bookings | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php:36؛ Modules/Academy/Services/AcademyClassScheduleService.php:60؛ Modules/Academy/Services/AcademyClassScheduleService.php:62 |
| academy_branch_classrooms | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:1399؛ Modules/Academy/Services/AcademyClassroomService.php:295؛ Modules/Academy/Services/AcademyClassroomService.php:314 |
| academy_branch_classroom_assets | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyClassroomService.php:311؛ Modules/Academy/Services/AcademyClassroomService.php:316؛ Modules/Academy/Services/AcademyClassroomService.php:376 |
| academy_branch_courses | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermController.php:163؛ Modules/Academy/Services/AcademyBranchService.php:901؛ Modules/Academy/Services/AcademyBranchService.php:1152 |
| academy_branch_course_terms | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:673؛ Modules/Academy/Services/AcademyBranchService.php:678؛ Modules/Academy/Services/AcademyBranchService.php:679 |
| academy_branch_course_term_enrollments | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:168؛ Modules/Academy/Services/AcademyBranchService.php:673؛ Modules/Academy/Services/AcademyBranchService.php:910 |
| academy_branch_course_term_invoices | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:995؛ Modules/Academy/Services/AcademyCourseService.php:312؛ Modules/Academy/Services/AcademyCourseService.php:314 |
| academy_branch_course_term_invoice_installments | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyCourseService.php:315؛ Modules/Academy/Services/AcademyCourseService.php:320؛ Modules/Academy/Services/AcademyTermService.php:1108 |
| academy_branch_course_term_schedule_skips | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyTermService.php:1084؛ Modules/Academy/Services/AcademyTermService.php:1100 |
| academy_branch_course_term_sessions | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermAvailabilityController.php:36؛ Modules/Academy/Services/AcademyClassroomService.php:342؛ Modules/Academy/Services/AcademyClassScheduleService.php:60 |
| academy_branch_course_term_session_attendances | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyClassScheduleService.php:188؛ Modules/Academy/Services/AcademyClassScheduleService.php:192؛ Modules/Academy/Services/AcademyClassScheduleService.php:194 |
| academy_branch_course_term_waiting_list | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchService.php:165؛ Modules/Academy/Services/AcademyBranchService.php:679؛ Modules/Academy/Services/AcademyCourseService.php:289 |
| academy_branch_members | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermController.php:183؛ Modules/Academy/Controllers/Web/AcademyTermController.php:184؛ Modules/Academy/Controllers/Web/AcademyTermController.php:186 |
| academy_branch_member_contracts | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermController.php:192؛ Modules/Academy/Controllers/Web/AcademyTermController.php:193؛ Modules/Academy/Controllers/Web/AcademyTermController.php:194 |
| academy_branch_member_permissions | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyRegistrationService.php:266؛ Modules/Analytics/Services/AcademyScopedBackupService.php:39؛ Modules/Analytics/Services/AdminUserAccessService.php:63 |
| academy_branch_member_roles | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Controllers/Web/AcademyTermController.php:184؛ Modules/Academy/Controllers/Web/AcademyTermController.php:185؛ Modules/Academy/Controllers/Web/AcademyTermController.php:187 |
| academy_branch_scheduling_rules | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AcademyScopedBackupService.php:43؛ Modules/Analytics/Services/AdminSchedulingRuleService.php:19؛ Modules/Analytics/Services/AdminSchedulingRuleService.php:22 |
| academy_branch_types | ارجاع کد؛ حفظ شود | 2 | Modules/Academy/Controllers/Web/AcademyRegistrationController.php:88؛ Modules/Academy/Services/AcademyBranchService.php:1183؛ Modules/Academy/Services/AcademyBranchService.php:1191 |
| academy_documents | ارجاع کد؛ حفظ شود | 0 | Modules/Analytics/Services/AcademyScopedBackupService.php:43؛ Modules/Analytics/Services/AdminAccountService.php:169؛ Modules/Analytics/Services/AdminAccountService.php:200 |
| academy_national_holiday_settings | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyTermService.php:759؛ Modules/Academy/Services/NationalHolidayService.php:17؛ Modules/Academy/Services/NationalHolidayService.php:95 |
| academy_subscription_payments | ارجاع کد؛ حفظ شود | 1 | Modules/Academy/Services/AcademySubscriptionService.php:83؛ Modules/Academy/Services/AcademySubscriptionService.php:90؛ Modules/Academy/Services/AcademySubscriptionService.php:97 |
| academy_subscription_periods | ارجاع کد؛ حفظ شود | 2 | Modules/Academy/Services/AcademySubscriptionService.php:17؛ Modules/Academy/Services/AcademySubscriptionService.php:45؛ Modules/Academy/Services/AcademySubscriptionService.php:47 |
| academy_term_invoice_payments | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/InvoiceLedger.php:89؛ Modules/Academy/Services/OfflineInstallmentPaymentService.php:64؛ Modules/Academy/Services/OfflineInstallmentPaymentService.php:68 |
| classroom_types | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyClassroomService.php:11؛ Modules/Academy/Services/AcademyClassroomService.php:12؛ Modules/Academy/Services/AcademyClassroomService.php:13 |
| creator_courses | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:159؛ Modules/CourseMarket/Services/CourseService.php:18؛ Modules/CourseMarket/Services/CourseService.php:21 |
| creator_course_details | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:17؛ Modules/CourseMarket/Services/CourseExperienceService.php:44؛ Modules/CourseMarket/Services/CourseExperienceService.php:158 |
| creator_course_lessons | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Repositories/LessonRepository.php:15؛ Modules/CourseMarket/Repositories/LessonRepository.php:31؛ Modules/CourseMarket/Repositories/LessonRepository.php:34 |
| creator_course_media | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:51؛ Modules/CourseMarket/Services/CourseExperienceService.php:134؛ Modules/CourseMarket/Services/CourseExperienceService.php:153 |
| creator_course_orders | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:29؛ Modules/CourseMarket/Services/CourseService.php:21؛ Modules/CourseMarket/Services/CourseService.php:48 |
| creator_course_questions | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:39؛ Modules/CourseMarket/Services/CourseExperienceService.php:94؛ Modules/CourseMarket/Services/CourseExperienceService.php:97 |
| creator_course_reactions | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:40؛ Modules/CourseMarket/Services/CourseExperienceService.php:41؛ Modules/CourseMarket/Services/CourseExperienceService.php:103 |
| creator_course_reports | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:105 |
| creator_course_reviews | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:28؛ Modules/CourseMarket/Services/CourseExperienceService.php:38؛ Modules/CourseMarket/Services/CourseExperienceService.php:68 |
| creator_course_schedules | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Services/CourseExperienceService.php:43؛ Modules/CourseMarket/Services/CourseExperienceService.php:111 |
| creator_lesson_access | ارجاع کد؛ حفظ شود | 0 | Modules/CourseMarket/Repositories/LessonRepository.php:88؛ Modules/CourseMarket/Repositories/LessonRepository.php:100 |
| instruments | ارجاع کد؛ حفظ شود | 55 | Modules/Academy/Controllers/Api/PublicAcademyController.php:33؛ Modules/Academy/Services/AcademyBranchOfferingService.php:32؛ Modules/Academy/Services/AcademyBranchOfferingService.php:84 |
| legacy_import_map | ابزار مهاجرت؛ حفظ شود | 530 | docs/database/import-wordpress-content:15؛ docs/database/import-wordpress-content:30؛ docs/database/verify-wordpress-content:7 |
| lessons | ارجاع کد؛ حفظ شود | 89 | Modules/Academy/Routes/web.php:84؛ Modules/Academy/Routes/web.php:85؛ Modules/Academy/Routes/web.php:86 |
| levels | ارجاع کد؛ حفظ شود | 10 | Modules/Academy/Routes/web.php:101؛ Modules/Academy/Routes/web.php:102؛ Modules/Academy/Routes/web.php:103 |
| music_sheets | ارجاع کد؛ حفظ شود | 0 | Modules/Notation/Services/NotationService.php:48؛ Modules/Notation/Services/NotationService.php:69؛ Modules/Notation/Services/NotationService.php:187 |
| music_sheet_bookmarks | ارجاع کد؛ حفظ شود | 0 | Modules/Notation/Services/NotationService.php:44؛ Modules/Notation/Services/NotationService.php:63؛ Modules/Notation/Services/NotationService.php:234 |
| social_lesson_progress | ارجاع کد؛ حفظ شود | 0 | Modules/Social/Services/LearningService.php:93؛ Modules/Social/Services/LearningService.php:95؛ Modules/Social/Services/LearningService.php:105 |
| user_instruments | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchOfferingService.php:129؛ Modules/Academy/Services/AcademyBranchOfferingService.php:582؛ Modules/Academy/Services/AcademyRegistrationService.php:202 |
| user_lessons | ارجاع کد؛ حفظ شود | 0 | Modules/Academy/Services/AcademyBranchOfferingService.php:138؛ Modules/Academy/Services/AcademyBranchOfferingService.php:219؛ Modules/Academy/Services/AcademyBranchOfferingService.php:226 |

## FKهای مجموعه جغرافیایی بدون ارجاع

| مبدأ | مقصد |
|---|---|
| z_world_cities.state_id | z_world_states.id |
| z_world_cities.country_id | z_world_countries.id |
| z_world_countries.region_id | z_world_regions.id |
| z_world_countries.subregion_id | z_world_subregions.id |
| z_world_postcodes.city_id | z_world_cities.id |
| z_world_postcodes.country_id | z_world_countries.id |
| z_world_postcodes.state_id | z_world_states.id |
| z_world_states.country_id | z_world_countries.id |
| z_world_subregions.region_id | z_world_regions.id |
