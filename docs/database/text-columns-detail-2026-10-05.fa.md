# فهرست دقیق ستون‌های TEXT خارج از ترجمه

**آخرین تصمیم:** طبق توضیح کاربر، TEXT فنی مجاز است؛ اکنون ۱۱ ستون فنی و ستون URLهای تاریخی `pinged` متنی‌اند. [وضعیت نهایی](text-storage-url-restoration-2026-10-06.fa.md) جایگزین ادعای صفر بودن TEXT در یادداشت قبلی است.

**این فهرست تاریخی است.** اصلاح هر ۳۳ ستون در ۲۰۲۶-۱۰-۰۶ کامل شد؛ اکنون تعداد ستون TEXT خارج از ترجمه صفر است. نتیجهٔ دقیق در [گزارش نهایی](text-storage-changes-2026-10-06.fa.md) است. شمارش‌های پایین وضعیت مرحلهٔ قبلی را توصیف می‌کنند.

در ساختار فعلی **۳۳ ستون در ۲۶ جدول** خارج از `p_translations` و `f_translations` از خانوادهٔ TEXT هستند: **۲۴ TEXT، ۸ LONGTEXT و یک TINYTEXT**. ستون VARCHAR در این فهرست نیست. پاک‌سازی و بازیابی ردیف‌ها نوع این ستون‌ها را تغییر نداده است.

عدد کنار ستون در CSV، تعداد مقدار غیرخالی **پیش از پاک‌سازی اولیه** است؛ تعداد فعلی نیست. ستون با صفر مقدار نیز از نظر قاعدهٔ نوع داده همچنان نیازمند بررسی است. این گزارش هیچ مقدار خصوصی را نمایش نمی‌دهد.

| جدول | ستون | نوع | مقدار غیرخالی پیش از پاک‌سازی |
|---|---|---|---:|
| `f_comments` | `author` | TINYTEXT | ۸۶ |
| `f_conversation_messages` | `body` | TEXT | ۱۸ |
| `f_conversation_messages` | `attachment_path` | TEXT | ۲ |
| `f_legacy_settings` | `value` | LONGTEXT | ۵ |
| `f_media_files` | `path` | TEXT | ۰ |
| `f_media_files` | `thumbnail_path` | TEXT | ۰ |
| `f_posts` | `pinged` | TEXT | ۸۷ |
| `f_social_account_settings` | `settings_json` | TEXT | ۰ |
| `f_social_comments` | `body` | TEXT | ۰ |
| `f_social_posts` | `body` | TEXT | ۰ |
| `f_social_profiles` | `bio` | TEXT | ۰ |
| `f_tracking_user_consents` | `user_agent` | TEXT | ۰ |
| `f_tracking_user_events` | `event_data` | LONGTEXT | ۴۰۷۸۲ |
| `f_tracking_user_page_views` | `query_params` | LONGTEXT | ۱۹۲۶ |
| `f_tracking_user_sessions` | `user_agent` | TEXT | ۱۳۱ |
| `f_user_certificates` | `certificate_url` | TEXT | ۰ |
| `f_user_certificates` | `file_path` | TEXT | ۰ |
| `f_user_merges` | `reason` | TEXT | ۰ |
| `f_user_merges` | `admin_note` | TEXT | ۰ |
| `f_user_points` | `metadata` | LONGTEXT | ۰ |
| `f_user_point_rules` | `description` | TEXT | ۱ |
| `f_user_publications` | `url` | TEXT | ۰ |
| `f_user_publications` | `content` | TEXT | ۰ |
| `f_user_settings` | `value` | TEXT | ۰ |
| `p_creator_courses` | `description` | TEXT | ۰ |
| `p_creator_courses` | `curriculum` | LONGTEXT | ۰ |
| `p_creator_course_details` | `metadata` | LONGTEXT | ۰ |
| `p_creator_course_lessons` | `media_json` | TEXT | ۰ |
| `p_creator_course_questions` | `body` | TEXT | ۰ |
| `p_creator_course_reports` | `body` | TEXT | ۰ |
| `p_creator_course_reviews` | `body` | TEXT | ۰ |
| `p_music_sheets` | `metadata` | LONGTEXT | ۰ |
| `p_music_sheets` | `score` | LONGTEXT | ۰ |

## روش اصلاح

متن انسانی مانند `body`، `bio`، `description` و `content` باید با قرارداد زبان و مسیر خواندن/نوشتن به ترجمه منتقل شود. `author` نام نویسندهٔ نظر است، نه متن خود نظر؛ حفظ کامنت‌ها شامل نگهداری این مشخصات هم می‌شود.

مسیر فایل، URL و user-agent معمولاً قابل ترجمه نیستند؛ در صورت تناسب طول می‌توان نوع VARCHAR مناسب را برایشان بررسی کرد. JSON، metadata، curriculum و score نیازمند بررسی ساختار و مصرف‌کننده‌اند؛ تبدیل کورکورانه به VARCHAR می‌تواند محدودیت اندازهٔ ردیف یا از دست‌رفتن داده ایجاد کند. برای رعایت کامل قاعدهٔ اعلام‌شده، هر ۳۳ ستون نیازمند تصمیم و migration قابل بازبینی هستند؛ این مرحله فقط گزارش است و هیچ تبدیل نوعی اجرا نشده است.

[CSV قابل پردازش](text-columns-outside-translations-2026-10-05.csv) · [گزارش منابع متن و تکرار ترجمه‌ها](content-and-translations-review-2026-10-05.fa.md)
