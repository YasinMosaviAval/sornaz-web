# تست راه‌های ارتباطی مدیران آموزشگاه

## هدف و توزیع

تست ساخت ۵۰ مدیر آموزشگاه برای هر کاربر بین ۱ تا ۱۰ راه ارتباطی در جدول `user_contacts` ایجاد می‌کند. تعداد رکورد کاربر از فرمول زیر به دست می‌آید:

```php
($userIndex % 10) + 1
```

هر تعداد از ۱ تا ۱۰ دقیقاً برای ۵ کاربر تکرار می‌شود و در مجموع ۲۷۵ راه ارتباطی ساخته می‌شود. اگر کاربر فقط یک راه ارتباطی داشته باشد، mode بر اساس شماره کاربر به‌صورت متناوب `phone` یا `email` خواهد بود.

## mode و platform

ساختار enumهای این مرحله از `docs/database/sornazco_maindb.sql` خوانده شده است.

| mode | platformهای استفاده‌شده | نوع مقدار ترجمه‌شده |
|---|---|---|
| `phone` | `other` | شماره موبایل ۱۱ رقمی ایرانی |
| `email` | `other` | نشانی Gmail |
| `social` | `instagram`, `telegram`, `website`, `whats-app`, `linkedin`, `google-meet` | URL متناسب با پلتفرم |

چون enum پلتفرم مقدار مستقلی برای تلفن و ایمیل ندارد، برای این دو mode از `other` استفاده شده است. platformهای شبکه اجتماعی دقیقاً مطابق نوع لینک انتخاب می‌شوند.

## ترجمه‌ها

مقدار راه ارتباطی در خود جدول `user_contacts` ستون مستقلی ندارد. برای هر `user_contact_id` دو رکورد فارسی در جدول `translations` ثبت می‌شود:

- `field=value`: شماره تلفن، ایمیل یا URL
- `field=note`: توضیح تکمیلی مانند ساعات پاسخ‌گویی، کاربرد اضطراری، پشتیبانی، امور اداری یا وضعیت قدیمی لینک

مقادیر مشترک ترجمه‌ها:

- `table_name=user_contacts`
- `table_id=user_contact_id`
- `locale=fa`
- `created_by=user_id`
- `updated_by=user_id`
- `approved_by=user_id`

## اولویت، وضعیت و زمان‌ها

- اولین رکورد هر mode برای هر کاربر `priority=primary` و `is_main=1` می‌شود.
- رکوردهای بعدی همان mode مقدار `is_main=0` دارند.
- اولویت‌های فرعی به‌شکل منطقی از `secondary`، `emergency`، `ledger`، `support` و `other` انتخاب می‌شوند.
- راه‌های اصلی و پراستفاده `active` هستند؛ لینک حرفه‌ای قدیمی `inactive` و لینک جلسه رزرو `deactive` است.
- `created_at` حداقل ۶ ساعت بعد از زمان ثبت‌نام کاربر است.
- `approved_at` بین ۱ تا ۱۲ ساعت بعد از `created_at` قرار می‌گیرد و زمان ثبت موفق OTP را شبیه‌سازی می‌کند.
- `last_called_at` بین ۱ تا ۱۴ روز بعد از `approved_at` است و آخرین تماس یا بازکردن لینک از داخل برنامه را شبیه‌سازی می‌کند.
- `updated_at` بعد از `last_called_at` قرار دارد.
- در `user_contacts` و هر دو ترجمه، `created_by`، `updated_by` و `approved_by` برابر `user_id` صاحب راه ارتباطی هستند.

## اجرای مجدد و حذف

- اجرای تست با استفاده از ترجمه `field=value` و تطبیق `user_id`، رکورد قبلی را پیدا و همگام می‌کند.
- اجرای مجدد راه ارتباطی تکراری نمی‌سازد.
- عملیات حذف مدیران آزمایشی ابتدا ترجمه‌های `user_contacts` و سپس خود contactها را حذف می‌کند.
- ساخت و حذف در transaction انجام می‌شوند.

## بررسی constraint مربوط به is_main

در dump فعلی، جدول `user_contacts` فقط primary key دارد و constraintای برای تضمین «حداکثر یک `is_main=1` برای هر user و mode» وجود ندارد. کد تست این قانون را رعایت می‌کند، ولی برای تضمین آن در تمام برنامه پیشنهاد می‌شود پس از بررسی و پاک‌سازی داده‌های فعلی، SQL زیر روی MySQL 8 اجرا شود.

### ۱. پیدا کردن مغایرت‌های فعلی

```sql
SELECT user_id, mode, COUNT(*) AS main_count
FROM user_contacts
WHERE is_main = 1
  AND deleted_at IS NULL
GROUP BY user_id, mode
HAVING COUNT(*) > 1;
```

### ۲. نگه‌داشتن بهترین رکورد اصلی و غیراصلی‌کردن موارد اضافه

```sql
UPDATE user_contacts AS contacts
JOIN (
    SELECT user_contact_id,
           ROW_NUMBER() OVER (
               PARTITION BY user_id, mode
               ORDER BY
                   (priority = 'primary') DESC,
                   (approved_at IS NOT NULL) DESC,
                   approved_at DESC,
                   user_contact_id ASC
           ) AS row_number_in_mode
    FROM user_contacts
    WHERE is_main = 1
      AND deleted_at IS NULL
) AS ranked
  ON ranked.user_contact_id = contacts.user_contact_id
SET contacts.is_main = 0
WHERE ranked.row_number_in_mode > 1;
```

### ۳. افزودن unique constraint شرطی

MySQL partial index ندارد؛ بنابراین یک generated column فقط برای رکورد اصلی و حذف‌نشده ایجاد می‌شود. وجود چند مقدار `NULL` در unique index مجاز است و رکوردهای غیراصلی را محدود نمی‌کند.

```sql
ALTER TABLE user_contacts
    ADD COLUMN active_main_guard TINYINT
        GENERATED ALWAYS AS (
            CASE
                WHEN is_main = 1 AND deleted_at IS NULL THEN 1
                ELSE NULL
            END
        ) STORED,
    ADD UNIQUE KEY uq_user_contacts_one_main_per_mode
        (user_id, mode, active_main_guard);
```

برای rollback تغییر schema:

```sql
ALTER TABLE user_contacts
    DROP INDEX uq_user_contacts_one_main_per_mode,
    DROP COLUMN active_main_guard;
```

## کوئری‌های کنترل تست

```sql
SELECT COUNT(*) AS contact_count
FROM user_contacts c
JOIN users u ON u.user_id = c.user_id
WHERE u.username LIKE 'test_academy_manager_%';

SELECT u.user_id, COUNT(c.user_contact_id) AS contact_count
FROM users u
JOIN user_contacts c ON c.user_id = u.user_id
WHERE u.username LIKE 'test_academy_manager_%'
GROUP BY u.user_id
HAVING COUNT(c.user_contact_id) BETWEEN 1 AND 10;

SELECT c.user_id, c.mode, SUM(c.is_main = 1) AS main_count
FROM user_contacts c
JOIN users u ON u.user_id = c.user_id
WHERE u.username LIKE 'test_academy_manager_%'
GROUP BY c.user_id, c.mode
HAVING SUM(c.is_main = 1) <> 1;

SELECT c.user_contact_id, c.mode, c.platform, c.priority,
       c.is_main, c.status, value_tr.value, note_tr.value AS note
FROM user_contacts c
JOIN users u ON u.user_id = c.user_id
JOIN translations value_tr
  ON value_tr.table_name = 'user_contacts'
 AND value_tr.table_id = c.user_contact_id
 AND value_tr.locale = 'fa'
 AND value_tr.field = 'value'
JOIN translations note_tr
  ON note_tr.table_name = 'user_contacts'
 AND note_tr.table_id = c.user_contact_id
 AND note_tr.locale = 'fa'
 AND note_tr.field = 'note'
WHERE u.username LIKE 'test_academy_manager_%';
```

## کامیت پیشنهادی این مرحله

```text
feat(admin-tests): add contact methods for sample academy managers
```

