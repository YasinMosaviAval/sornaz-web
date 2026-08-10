# تست آدرس مدیران آموزشگاه

## هدف

این مرحله تست ساخت ۵۰ مدیر آموزشگاه را گسترش می‌دهد تا هر کاربر حداقل یک و حداکثر سه آدرس داشته باشد. آدرس‌ها در `user_addresses` و متن نشانی و توضیحات در `translations` ثبت می‌شوند.

تعداد آدرس هر کاربر از فرمول زیر به‌دست می‌آید:

```php
($userIndex % 3) + 1
```

بنابراین توزیع آدرس‌ها برای ۵۰ کاربر برابر است با:

- ۱۷ کاربر با یک آدرس
- ۱۷ کاربر با دو آدرس
- ۱۶ کاربر با سه آدرس
- مجموع: ۹۹ آدرس

## ساختار هر آدرس

برای هر رکورد `user_addresses` مقادیر زیر ثبت می‌شوند:

| ستون | روش مقداردهی |
|---|---|
| `user_id` | شناسه مدیر آموزشگاه آزمایشی |
| `country_id` | مقدار ثابت `0` برای ایران |
| `province_id` | جست‌وجو در `world_iran_provinces` با نام استان |
| `county_id` | جست‌وجو در `world_iran_counties` با نام شهرستان و `province_id` انتخاب‌شده |
| `is_main` | فقط آدرس اول `1` و سایر آدرس‌ها `0` |
| `latitude` / `longitude` | مختصات مکان عمومی واقعی انتخاب‌شده روی نقشه |
| `postal_code` | کد پستی آزمایشی ۱۰ رقمی با پیش‌شماره متناسب با محدوده جغرافیایی شهر |
| `created_by` / `updated_by` | برابر `user_id` صاحب آدرس |
| `created_at` | حداقل یک روز بعد از زمان ثبت‌نام کاربر |
| `updated_at` | بعد از `created_at` آدرس |

کدهای پستی برای تست ساختار و محدوده جغرافیایی تولید شده‌اند و نباید برای ارسال واقعی مرسوله استفاده شوند.

## ترجمه‌های آدرس

برای هر `address_id` دو رکورد فارسی در جدول `translations` ایجاد می‌شود:

1. `field=address`: متن کامل نشانی
2. `field=note`: توضیحات تکمیلی مانند محدودیت ساعت حضور، اجاره‌ای بودن محل، محدودیت تردد خودرو یا نیاز به هماهنگی

مشخصات مشترک ترجمه‌ها:

- `table_name=user_addresses`
- `table_id=address_id`
- `locale=fa`
- `created_by=user_id`
- `updated_by=user_id`
- زمان‌های ایجاد و به‌روزرسانی برابر زمان‌های آدرس و بعد از ثبت‌نام کاربر هستند.

## مکان‌های مرجع

مجموعه داده از مکان‌های عمومی واقعی استفاده می‌کند تا آدرس متنی و مختصات با یکدیگر سازگار باشند. لینک‌ها بر اساس مختصات، مکان را در Google Maps باز می‌کنند:

| استان / شهرستان | مکان | مختصات | نقشه |
|---|---|---|---|
| تهران / تهران | برج میلاد | `35.7448416, 51.3753212` | [Google Maps](https://www.google.com/maps/search/?api=1&query=35.7448416,51.3753212) |
| فارس / شیراز | آرامگاه حافظ | `29.6259365, 52.5585667` | [Google Maps](https://www.google.com/maps/search/?api=1&query=29.6259365,52.5585667) |
| اصفهان / اصفهان | میدان نقش جهان | `32.6573073, 51.6775612` | [Google Maps](https://www.google.com/maps/search/?api=1&query=32.6573073,51.6775612) |
| آذربایجان شرقی / تبریز | مقبره‌الشعرا | `38.0820297, 46.2919108` | [Google Maps](https://www.google.com/maps/search/?api=1&query=38.0820297,46.2919108) |
| خراسان رضوی / مشهد | ورودی باب‌الرضا | `36.2879029, 59.6157291` | [Google Maps](https://www.google.com/maps/search/?api=1&query=36.2879029,59.6157291) |
| گیلان / رشت | میدان شهرداری | `37.2759338, 49.5883064` | [Google Maps](https://www.google.com/maps/search/?api=1&query=37.2759338,49.5883064) |
| یزد / یزد | میدان امیرچخماق | `31.8972362, 54.3686977` | [Google Maps](https://www.google.com/maps/search/?api=1&query=31.8972362,54.3686977) |
| کرمان / کرمان | مجموعه گنجعلی‌خان | `30.2924087, 57.0671107` | [Google Maps](https://www.google.com/maps/search/?api=1&query=30.2924087,57.0671107) |
| همدان / همدان | آرامگاه بوعلی سینا | `34.7988575, 48.5146239` | [Google Maps](https://www.google.com/maps/search/?api=1&query=34.7988575,48.5146239) |
| خوزستان / اهواز | پل سفید | `31.3282914, 48.6706183` | [Google Maps](https://www.google.com/maps/search/?api=1&query=31.3282914,48.6706183) |

## کنترل تطابق استان و شهرستان

شناسه‌ها در کد ثابت نشده‌اند. ابتدا استان با `province_name` خوانده می‌شود؛ سپس شهرستان فقط در صورتی انتخاب می‌شود که هم نام آن صحیح باشد و هم `province_id` آن با استان انتخاب‌شده برابر باشد. پیدا نشدن استان یا شهرستان باعث exception و rollback کامل تست می‌شود.

## اجرای مجدد و پاک‌سازی

- هر آدرس با ترکیب `user_id` و `postal_code` شناسایی می‌شود.
- اجرای مجدد، رکورد موجود و ترجمه‌های `address` و `note` را همگام می‌کند و آدرس تکراری نمی‌سازد.
- عملیات «حذف مدیران آموزشگاه آزمایشی» ابتدا ترجمه‌های آدرس، سپس `user_addresses`، ترجمه نام کاربران و در پایان خود کاربران را حذف می‌کند.
- تمام مراحل ساخت و حذف در transaction اجرا می‌شوند.

## کوئری‌های کنترل نتیجه

```sql
SELECT COUNT(*) AS address_count
FROM user_addresses a
JOIN users u ON u.user_id = a.user_id
WHERE u.username LIKE 'test_academy_manager_%';

SELECT u.user_id, COUNT(a.address_id) AS address_count,
       SUM(a.is_main = 1) AS main_count
FROM users u
JOIN user_addresses a ON a.user_id = u.user_id
WHERE u.username LIKE 'test_academy_manager_%'
GROUP BY u.user_id;

SELECT a.address_id, p.province_name, c.county_name,
       a.latitude, a.longitude, a.postal_code,
       address_tr.value AS address,
       note_tr.value AS note
FROM user_addresses a
JOIN users u ON u.user_id = a.user_id
JOIN world_iran_provinces p ON p.province_id = a.province_id
JOIN world_iran_counties c
  ON c.county_id = a.county_id
 AND c.province_id = a.province_id
JOIN translations address_tr
  ON address_tr.table_name = 'user_addresses'
 AND address_tr.table_id = a.address_id
 AND address_tr.locale = 'fa'
 AND address_tr.field = 'address'
JOIN translations note_tr
  ON note_tr.table_name = 'user_addresses'
 AND note_tr.table_id = a.address_id
 AND note_tr.locale = 'fa'
 AND note_tr.field = 'note'
WHERE u.username LIKE 'test_academy_manager_%';
```

## کامیت پیشنهادی این مرحله

```text
feat(admin-tests): add realistic addresses for sample academy managers
```

