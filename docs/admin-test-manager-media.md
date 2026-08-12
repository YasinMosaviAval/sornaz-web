# رسانه‌های پیش‌فرض مدیران آزمایشی

دارایی‌های ثابت در `assets/media/users` نگهداری می‌شوند:

- `profiles`: تعداد ۵۰ تصویر مربع `720x720`
- `covers`: تعداد ۵۰ تصویر افقی `1280x720` با نسبت ۱۶:۹
- `gallery`: سه تصویر `1200x900` برای هر کاربر، مجموع ۱۵۰ تصویر
- `intro-videos`: یک ویدیوی کم‌حجم برای ۲۰ کاربر اول، مجموع ۲۰ ویدیو

کاربران فرد در داده تست مرد و کاربران زوج زن هستند. تصویر و ویدیوی هر گروه از کلیپ موسیقایی همان جنسیت ساخته شده است. منابع و مجوزها در `ATTRIBUTION.json` و `VIDEO-SOURCES.json` ثبت شده‌اند. دارایی‌ها تحت Mixkit Video Free License دریافت شده‌اند.

## همگام‌سازی دیتابیس

تست مدیران برای فایل‌ها رکورد idempotent در `media_files` می‌سازد. مسیر فایل کلید شناسایی است و اندازه، checksum، نوع، collection و ابعاد در هر اجرا همگام می‌شوند. آواتار در `users.avatar_file_id` نیز ثبت می‌شود.

هنگام حذف مدیران تست، فایل فیزیکی و رکورد `media_files` حذف نمی‌شود؛ فقط `user_id` و `fileable_id` آن موقتاً `NULL` می‌شوند. اجرای بعدی تست همان رکوردها را دوباره به کاربران تازه متصل می‌کند.

## بازسازی دارایی‌ها

```powershell
powershell.exe -ExecutionPolicy Bypass -File scripts/download-user-media.ps1
```

## کامیت پیشنهادی

```text
feat(admin-tests): add persistent user media fixtures
```
