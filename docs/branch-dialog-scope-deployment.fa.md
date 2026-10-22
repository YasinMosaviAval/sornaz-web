# انتشار اصلاح فرم‌های شعبه و تب انواع کلاس

این تغییر فقط در پروژه وب است. migration، تغییر تنظیمات یا ساخت APK لازم ندارد.

## فایل‌های قابل آپلود

مسیرها نسبت به ریشه فعال سایت هستند. فایل‌ها را در assets/Analytics/js واقعی سایت جایگزین کنید، نه نسخه قدیمی public/assets یا Resources/Assets.

- `Modules/Analytics/Resources/Views/admin-panel.php`
- `Modules/Analytics/Resources/Views/sections/classroom-types.php`
- `assets/Analytics/js/availability-exception-templates.js`
- `assets/Analytics/js/availability-templates.js`
- `assets/Analytics/js/branch-templates.js`
- `assets/Analytics/js/classroom-templates.js`
- `assets/Analytics/js/course-templates.js`
- `assets/Analytics/js/finance-templates.js`
- `assets/Analytics/js/gallery-templates.js`
- `assets/Analytics/js/instrument-templates.js`
- `assets/Analytics/js/lesson-templates.js`
- `assets/Analytics/js/member-schedule-templates.js`
- `assets/Analytics/js/panel-organization-filters.js`
- `assets/Analytics/js/permission-templates.js`
- `assets/Analytics/js/point-templates.js`
- `assets/Analytics/js/role-templates.js`
- `assets/Analytics/js/scheduling-rule-templates.js`
- `assets/Analytics/js/student-templates.js`
- `assets/Analytics/js/teacher-templates.js`
- `assets/Analytics/js/term-templates.js`
- `assets/Analytics/js/terms.js`
- `assets/Analytics/js/user-templates.js`

## ترتیب انتشار و بازگشت

۱. ابتدا assets/Analytics/js/panel-organization-filters.js، سپس قالب‌های JavaScript و terms.js، و در پایان دو قالب PHP بالا را آپلود کنید. نام و بزرگی/کوچکی حروف مسیرها را حفظ کنید. هر فایل را با جایگزینی اتمی منتشر کنید.
۲. اصلاح نسخه‌گذاری دارایی‌ها در docs/panel-assets-online-deployment.fa.md نیز جزو تغییرات آماده انتشار همین نسخه است؛ ترتیب AssetManager سپس JavaScript و در آخر helpers را رعایت کنید. فایل docs/panel-assets-upload.sha256 فهرست کنترل محتوای تمام فایل‌های پنل و قالب‌های PHP این اصلاح است.
۳. از فایل‌های نسخه فعال پشتیبان بگیرید؛ برای بازگشت این اصلاح، ابتدا دو قالب PHP و سپس فایل‌های JavaScript را از همان نسخه پشتیبان بازیابی کنید. نیازی به بازگردانی داده نیست. برای بازگشت اصلاح cache، راهنمای جداگانه را دنبال کنید.

## آزمون پس از آپلود

- با حساب شعبه و یک کارمند شعبه، ثبت و ویرایش تراکنش، هنرجو، کارمند، کلاس و ترم را باز کنید: عنوان و کنترل سازمان/شعبه دیده نشود. ثبت آزمایشی فقط در محیط تست و با داده ساختگی انجام شود.
- با مدیر آموزشگاه، انتخاب سازمان/شعبه همچنان نمایش داده شود.
- در منوی کلاس‌ها، تب انواع کلاس برای شعبه و کارمند شعبه دیده شود؛ مجوز ایجاد، ویرایش و حذف طبق کنترل‌های قبلی باقی می‌ماند.
- یک درخواست دستکاری‌شده با سازمان شعبه دیگر باید رد شود. داده شعبه دیگر نباید در فهرست اختصاصی شعبه دیده شود.

هیچ فایل روی سرور اصلی منتشر نشده است. آزمون روی حساب‌های واقعی هاست پس از انتشار باید جداگانه انجام شود.
