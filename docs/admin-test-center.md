# مرکز تست‌های پنل ادمین

## هدف

این مرحله یک صفحه مرکزی برای ابزارهای تست پنل ادمین ایجاد می‌کند. اولین تست این صفحه، ۵۰ کاربر انسانی را به‌عنوان داده آزمایشی مدیران آموزشگاه در جدول‌های `users` و `translations` ایجاد یا همگام می‌کند.

ساختار ستون‌ها و enumها از آخرین فایل دیتابیس پروژه در مسیر `docs/database/sornazco_maindb.sql` خوانده شده است. جدول `users` دارای کلید اصلی `user_id` و کلیدهای یکتای `username`، `email`، `phone` و `national_code` است. نام کامل کاربر در جدول `translations` ذخیره می‌شود.

## دسترسی و ایمنی

- صفحه «مرکز تست‌ها» فقط برای مدیر سایت نمایش داده می‌شود.
- endpoint تست با middlewareهای `site-admin` و `csrf` محافظت شده است.
- ابزار فقط در `APP_ENV=local` اجرا می‌شود و در محیط‌های دیگر پاسخ 404 می‌دهد.
- ایجاد ۵۰ رکورد در یک transaction انجام می‌شود؛ شکست هر بخش کل عملیات را rollback می‌کند.
- اجرای مجدد idempotent است. کاربران از روی الگوی `test_academy_manager_XX` پیدا و به‌روزرسانی می‌شوند، بنابراین اجرای مجدد رکورد تکراری نمی‌سازد.
- عملیات حذف فقط کاربران دارای پیشوند `test_academy_manager_` و ترجمه‌های متصل به شناسه همان کاربران را در یک transaction حذف می‌کند.
- رمز خام در دیتابیس ذخیره نمی‌شود. مقدار `123456789` با `password_hash` هش می‌شود.

## مسیرها و اجزای اصلی

- صفحه پنل: `/analytics/admin-panel#tests`
- اجرای تست: `POST /analytics/_test/seed-academy-managers`
- حذف مدیران آزمایشی: `POST /analytics/_test/delete-academy-managers`
- سرویس تولید داده: `Modules/Analytics/Services/AdminTestDataService.php`
- کنترلر تست: `Modules/Analytics/Controllers/Web/AdminTestController.php`
- نمای صفحه: `Modules/Analytics/Resources/Views/sections/tests.php`

ابزارهای قبلی ایجاد و حذف آموزشگاه‌های نمونه نیز از نوار کناری به همین صفحه منتقل شده‌اند تا تمام تست‌های ادمین در یک محل باشند.

## مشخصات داده‌های تولیدشده

| ویژگی | مقدار یا توزیع |
|---|---|
| تعداد | دقیقاً ۵۰ کاربر |
| نام کاربری | `test_academy_manager_01` تا `test_academy_manager_50` |
| نام کامل | ۲۵ نام مرد و ۲۵ نام زن فارسی در `translations` با `table_name=users`، `locale=fa` و `field=full_name` |
| ایمیل | آدرس یکتای Gmail با الگوی `sornaz.academy.managerXX@gmail.com` |
| تلفن | شماره یکتای ۱۱ رقمی ایرانی با پیش‌شماره `0991` |
| رمز ورود | `123456789` به‌صورت هش‌شده |
| نوع کاربر | `human` |
| جنسیت | مطابق نام فارسی، `male` یا `female` |
| کد ملی | کد ۱۰ رقمی یکتا با رقم کنترل معتبر الگوریتم کد ملی ایران |
| locale | `fa` |
| timezone | `Asia/Tehran` |
| created_by / updated_by | در `users` و ترجمه نام برابر `user_id` همان کاربر |
| زمان تأیید تلفن | بین ۱ تا ۴۸ ساعت بعد از زمان ثبت |
| آخرین ورود | بعد از زمان ثبت و حداکثر برابر زمان جاری |
| IP آخرین ورود | IPv4 آزمایشی یکتا |
| register_time | برابر `created_at` |
| زمان به‌روزرسانی | بعد از زمان ایجاد |

### توزیع وضعیت

| status | تعداد |
|---|---:|
| pending | ۲۰ |
| approved | ۲۰ |
| rejected | ۴ |
| inactive | ۳ |
| banned | ۳ |

در نتیجه ۴۰ کاربر بین `pending` و `approved` و ۱۰ کاربر در سه وضعیت دیگر قرار دارند. از میان کاربران approved، تعداد ۱۲ کاربر توسط مدیر سایت با `approved_by=1` تأیید می‌شوند. `approved_at` آن‌ها بین ۵ ساعت تا کمتر از ۱۰ روز بعد از `created_at` است.

### توزیع visibility

| visibility | تعداد |
|---|---:|
| public | ۳۰ |
| private | ۱۰ |
| unlisted | ۱۰ |

### توزیع روش ثبت‌نام

روش‌ها به‌صورت چرخشی انتخاب می‌شوند:

- `phone`: تعداد ۱۷
- `email`: تعداد ۱۷
- `google`: تعداد ۱۶

### توزیع سن

- ۴۰ کاربر در بازه ۳۰ تا ۶۰ سال قرار دارند.
- ۱۰ کاربر باقی‌مانده در محدوده کلی ۲۵ تا ۸۰ سال توزیع شده‌اند.
- `birthday` با نوع تاریخ سازگار با ستون `date` تولید می‌شود.

## روش اجرا

1. مقدار `APP_ENV` را در محیط توسعه روی `local` قرار دهید.
2. با حساب مدیر سایت وارد پنل ادمین شوید.
3. از نوار کناری «مرکز تست‌ها» را باز کنید.
4. دکمه «اجرای تست مدیران آموزشگاه» را انتخاب و تأیید کنید.
5. پس از redirect، تعداد کاربران و توزیع وضعیت‌ها روی کارت تست نمایش داده می‌شود.

## کوئری‌های پیشنهادی برای کنترل نتیجه

```sql
SELECT COUNT(*)
FROM users
WHERE username LIKE 'test_academy_manager_%';

SELECT status, COUNT(*)
FROM users
WHERE username LIKE 'test_academy_manager_%'
GROUP BY status;

SELECT visibility, COUNT(*)
FROM users
WHERE username LIKE 'test_academy_manager_%'
GROUP BY visibility;

SELECT register_method, COUNT(*)
FROM users
WHERE username LIKE 'test_academy_manager_%'
GROUP BY register_method;

SELECT u.user_id, u.username, u.gender, t.value AS full_name,
       u.created_by, u.updated_by, t.created_by AS translation_created_by,
       t.updated_by AS translation_updated_by
FROM users u
JOIN translations t
  ON t.table_name = 'users'
 AND t.table_id = u.user_id
 AND t.locale = 'fa'
 AND t.field = 'full_name'
WHERE u.username LIKE 'test_academy_manager_%';
```

## کامیت پیشنهادی این مرحله

```text
feat(admin): add test center and academy manager data generator
```
