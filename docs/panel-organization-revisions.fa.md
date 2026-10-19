# اصلاح پنل سازمان، درس‌ها و تقویم اپلیکیشن

## فایل‌های وب برای انتشار

1. کلاس جدید `Modules/Academy/Services/NationalHolidayPolicy.php` را ابتدا قرار دهید.
2. مصرف‌کننده‌های تنظیم شعبه را قرار دهید:
   `Modules/Academy/Services/AcademyTermService.php` و
   `Modules/Analytics/Services/AdminDashboardService.php`.
3. سپس `Modules/Academy/Services/NationalHolidayService.php`،
   `Modules/Academy/Controllers/Web/NationalHolidayController.php`،
   `Modules/Academy/Services/AcademyClassScheduleService.php`،
   `Modules/Academy/Services/AcademyBranchService.php` و
   `Modules/Analytics/Services/MobilePanelCatalog.php` را قرار دهید.
4. فایل‌های رابط:
   `Modules/Analytics/Resources/Views/sections/course-levels.php`،
   `Modules/Analytics/Resources/Views/sections/availability-exceptions.php`،
   `assets/Analytics/js/course-levels.js`، `assets/Analytics/js/lessons.js`،
   `assets/Analytics/js/lesson-templates.js`،
   `assets/Analytics/js/availability-exceptions.js`،
   `assets/Analytics/js/availability-exception-templates.js` و
   `assets/Analytics/js/national-holidays.js`.

کلاس جدید از PSR-4 موجود `Modules\` بارگذاری می‌شود. migration، وابستگی یا تنظیم
سرور جدیدی لازم نیست. تنظیم آموزشگاه در جدول فعلی باقی است؛ تنظیم مستقل شعبه
در `z_user_settings` با کلید `allow_classes_on_national_holidays` و مقدار ۰/۱ ذخیره
می‌شود. شعبه‌ای که مقدار مستقل ندارد از آموزشگاه ارث می‌برد. تنظیم شخصی شعبه
بر تنظیم آموزشگاه اولویت دارد و در زمان‌بندی، تشخیص تعطیلی و هشدارهای داشبورد
اعمال می‌شود. ذخیرهٔ گروهی تراکنشی است؛ رد دسترسی هر سازمان کل عملیات را برمی‌گرداند.

مسیر قدیمی ذخیرهٔ تنظیم آموزشگاه همچنان معتبر است. پاسخ فهرست، علاوه بر
`academies`، فیلد `organizations` دارد. اپ جدید را بعد از فایل‌های سرور منتقل
کنید تا فهرست شعبه‌ها و آیتم حضور و غیاب ترم در کاتالوگ در دسترس باشند.
حضور و غیاب از سرویس فعلی جلسه‌ها استفاده می‌کند؛ فیلتر `termId` در کنار
محدودهٔ دسترسی سازمان اعمال می‌شود. هیچ دادهٔ حضور و غیابی صرفاً محلی ذخیره نمی‌شود.

## فایل‌های اصلی اپلیکیشن

- `android/gradle.properties` و `tool/build_apk_offline.ps1`: سقف حافظهٔ Gradle
  چهار گیگابایت و تعداد worker/Cargo job برابر یک؛ نسخه‌ها و کش آفلاین حفظ شده‌اند.
- `lib/classes/my_app.dart`: رنگ مشترک حاشیه و نوشتهٔ راهنمای فیلدها.
- `lib/components/jalali_date_picker.dart`: همان انتخابگر Material با تقویم
  شمسی و تبدیل خروجی به تاریخ میلادی قرارداد API، بدون پکیج جدید.
- `lib/screens/Site/panel_form.dart` و `panel_section_card.dart`: دکمهٔ ذخیره
  بدون آیکن و ترکیب رنگ نیمه‌شفاف روی بگراند واقعی.
- `lib/screens/Site/panel_api.dart`، `panel_resource_page.dart` و
  `branches_page.dart`: بررسی تغییرات صفحهٔ فعال هر سه ثانیه و اعلان تغییر؛
  هنگام بازبودن دیالوگ، تغییر حساب یا رفتن به پس‌زمینه درخواست ادامه پیدا نمی‌کند.
- `lib/screens/Site/panel_access_badges.dart` و `panel_table_contract.dart`:
  نمایش نقش‌ها، مجوزها، عملیات و وضعیت مطابق جدول وب.
- `lib/screens/Site/panel_national_holiday_settings.dart` و
  `panel_term_attendance.dart`: تنظیم تعطیلات سازمان‌ها و حضور و غیاب جلسه‌های ترم.
- `lib/screens/Social/social_profile.dart` و `course_experience.dart`:
  استفاده از انتخابگر شمسی مشترک.

قابلیت «ساعت‌های آزاد» از عملیات قابل نمایش حذف شده؛ endpoint آن برای فرم‌های
زمان‌بندی که به آن نیاز دارند حفظ شده است. این تغییرات به OMR یا تحلیل عملکرد
آزمایشی وابسته نیستند و باید انتخابی، بدون AGENTS.md، به worktree انتشار منتقل شوند.

## آزمون و بازگشت

آزمون‌های مرتبط در `scripts/national_holiday_branch_test.php`،
`test/jalali_date_picker_test.dart` و `test/panel_requested_improvements_test.dart`
قرار دارند. همچنین تست‌های قرارداد پنل، دسترسی بین آموزشگاه‌ها، فرم‌ها و چیدمان
شعبه‌ها اجرا می‌شوند. این‌ها از fixture استفاده می‌کنند، نه دیتابیس اصلی.

پس از انتشار، با حساب آزمایشی آموزشگاه و شعبه، انتخاب مستقل تعطیلات، ذخیره،
تعیین تاریخ جلسه در تعطیل رسمی، حضور و غیاب و رد دسترسی به شعبهٔ دیگر را بررسی
کنید. با بازبودن یک جدول، تغییر آن از نشست دیگر باید در چند ثانیه دیده شود.
فونت مرخصی، نام سازمان و خروجی‌های سطح درس را هم بررسی کنید.

برای بازگشت، ابتدا رابط جدید را برگردانید. تنظیم‌های شعبه را حذف نکنید؛ تا
وقتی این تنظیم‌ها فعال‌اند، خواننده‌های `NationalHolidayPolicy` را حفظ کنید تا
بازگشت رابط باعث تغییر ناخواستهٔ قواعد زمان‌بندی نشود. بازگرداندن کامل منطق
قدیمی، رفتار را دوباره تابع تنظیم آموزشگاه می‌کند و نیازمند بررسی تنظیم‌های
شعبه‌هاست؛ هیچ داده‌ای نباید خودکار پاک یا بازنویسی شود.
## بازبینی تکمیلی API

نقش‌های مدیر و مالک شعبه از مجوز مدیریت تنظیم کل آموزشگاه جدا شده‌اند؛
مدیر شعبه فقط تنظیم شعبهٔ مجاز خود را می‌بیند و تغییر می‌دهد.
تست عدم دسترسی مدیر شعبه به شعبهٔ دیگر اضافه شده است.
