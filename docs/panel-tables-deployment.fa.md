# قالب مشترک جدول‌های پنل

تعریف HTML جدول، سرستون، ردیف و سلول فقط در `Modules/Analytics/Resources/Views/sections/panel-table-template.php` است. صفحات PHP با `component('panel-table-template', ['columns' => [...], 'rows' => [...]])` و صفحات JavaScript با `AdminPanelTable.tableHTML` / `rowHTML` / `cellsHTML` همین تعریف را مصرف می‌کنند. جدول‌های هفتگی، ردیف‌های ویرایش بازشونده و سلول‌های شرطی هم از همین ساختار استفاده می‌کنند. جدول‌های داخل متنِ ویرایشگر نوشته و قالب چاپ/PDF قرارداد جداگانهٔ خود را حفظ می‌کنند.

## قرارداد داده

هر ستون/سلول می‌تواند `{value: ...}` باشد که همیشه escape می‌شود، یا `{html: ... , attributes: ...}` برای HTML تولیدشده در قالب مورد اعتماد. دادهٔ ورودی کاربر را مستقیماً در `html` یا `attributes` قرار ندهید؛ مثل قالب‌های فعلی آن را با `e()` یا escapeHtml صفحه escape کنید. هر ردیف `{cells: [...], attributes: ...}` است. `tag: 'th'` برای سرستون محور جدول هفتگی قابل استفاده است. سلول ادغام‌شده با attributes شامل colspan تعریف می‌شود.

قالب‌ساز مرورگر در admin-ui-templates.js خواندن یا بازسازی DOM جدول‌های صفحه را برای استخراج داده انجام نمی‌دهد؛ دادهٔ ردیف‌ها پیش از ساخت DOM به آن داده می‌شود. فیلتر، مرتب‌سازی، صفحه‌بندی، مجوز و handlerهای هر صفحه در فایل فعلی همان صفحه باقی می‌مانند.

## انتشار و بازگشت

Migration لازم نیست. ابتدا کلاس جدید PanelTableRenderer.php و قالب مشترک را بارگذاری کنید، سپس admin-ui-templates.js و بعد سایر فایل‌های این فهرست را به‌عنوان یک مجموعهٔ انتشار بارگذاری کنید. قالب جدید جدول‌های نسخهٔ قبلی را هم پشتیبانی می‌کند. version فایل‌های JavaScript باید طبق filemtime در layout فعلی تازه شود؛ صفحهٔ باز را پس از انتشار بازخوانی کنید. اگر autoload هاست authoritative است، پیش از استفاده از کلاس تازه autoload همان انتشار را با Composer از composer.lock بازسازی کنید. فایل‌های مربوط به جدول‌ها را در میانهٔ انتشار به‌صورت مجزا rollback نکنید؛ همهٔ فایل‌های مجموعه را به نسخهٔ پشتیبان پیش از این تغییر برگردانید.

پس از بارگذاری با مدیر سایت و کاربر آموزشگاه، جدول‌های کاربران، کلاس‌ها، دوره‌ها و ترم‌ها را باز کنید؛ مرتب‌سازی، دکمهٔ ویرایش، ردیف بازشونده و حالت خالی را بررسی کنید. پنل شخصی را هم برای نمایش سوابق مالی متعلق به همان کاربر بررسی کنید. آزمون‌های خودکار این تغییر fixture محلی هستند و جای آزمون سرور را نمی‌گیرند.

## فایل‌های قابل بارگذاری

- `Modules/Analytics/Services/PanelTableRenderer.php`
- `Modules/Analytics/Resources/Views/sections/academy-enroll.php`
- `Modules/Analytics/Resources/Views/sections/academy-requests.php`
- `Modules/Analytics/Resources/Views/sections/approvals.php`
- `Modules/Analytics/Resources/Views/sections/availabilities.php`
- `Modules/Analytics/Resources/Views/sections/availability-exceptions.php`
- `Modules/Analytics/Resources/Views/sections/branch-types.php`
- `Modules/Analytics/Resources/Views/sections/branches.php`
- `Modules/Analytics/Resources/Views/sections/classroom-types.php`
- `Modules/Analytics/Resources/Views/sections/classrooms.php`
- `Modules/Analytics/Resources/Views/sections/comments.php`
- `Modules/Analytics/Resources/Views/sections/contact-us.php`
- `Modules/Analytics/Resources/Views/sections/contracts.php`
- `Modules/Analytics/Resources/Views/sections/course-levels.php`
- `Modules/Analytics/Resources/Views/sections/courses.php`
- `Modules/Analytics/Resources/Views/sections/favorites.php`
- `Modules/Analytics/Resources/Views/sections/finance.php`
- `Modules/Analytics/Resources/Views/sections/instruments.php`
- `Modules/Analytics/Resources/Views/sections/lessons.php`
- `Modules/Analytics/Resources/Views/sections/media.php`
- `Modules/Analytics/Resources/Views/sections/member-schedules.php`
- `Modules/Analytics/Resources/Views/sections/messages.php`
- `Modules/Analytics/Resources/Views/sections/my-points.php`
- `Modules/Analytics/Resources/Views/sections/national-holidays.php`
- `Modules/Analytics/Resources/Views/sections/notifications.php`
- `Modules/Analytics/Resources/Views/sections/pages.php`
- `Modules/Analytics/Resources/Views/sections/panel-table-template.php`
- `Modules/Analytics/Resources/Views/sections/permissions.php`
- `Modules/Analytics/Resources/Views/sections/personal-panel-sections.php`
- `Modules/Analytics/Resources/Views/sections/points.php`
- `Modules/Analytics/Resources/Views/sections/post-categories.php`
- `Modules/Analytics/Resources/Views/sections/posts.php`
- `Modules/Analytics/Resources/Views/sections/preview-protected-sections.php`
- `Modules/Analytics/Resources/Views/sections/preview-student-learning.php`
- `Modules/Analytics/Resources/Views/sections/profile-content-section.php`
- `Modules/Analytics/Resources/Views/sections/profiles.php`
- `Modules/Analytics/Resources/Views/sections/rating-summaries.php`
- `Modules/Analytics/Resources/Views/sections/ratings.php`
- `Modules/Analytics/Resources/Views/sections/reports.php`
- `Modules/Analytics/Resources/Views/sections/roles.php`
- `Modules/Analytics/Resources/Views/sections/schedules.php`
- `Modules/Analytics/Resources/Views/sections/scheduling-rules.php`
- `Modules/Analytics/Resources/Views/sections/students.php`
- `Modules/Analytics/Resources/Views/sections/teachers.php`
- `Modules/Analytics/Resources/Views/sections/terms.php`
- `Modules/Analytics/Resources/Views/sections/users.php`
- `assets/Analytics/js/academy-enroll.js`
- `assets/Analytics/js/academy-requests.js`
- `assets/Analytics/js/add-user-modal.js`
- `assets/Analytics/js/admin-ui-templates.js`
- `assets/Analytics/js/availabilities.js`
- `assets/Analytics/js/availability-exception-templates.js`
- `assets/Analytics/js/availability-exceptions.js`
- `assets/Analytics/js/availability-templates.js`
- `assets/Analytics/js/branch-templates.js`
- `assets/Analytics/js/branch-types.js`
- `assets/Analytics/js/branches.js`
- `assets/Analytics/js/classroom-templates.js`
- `assets/Analytics/js/classroom-types.js`
- `assets/Analytics/js/classrooms.js`
- `assets/Analytics/js/comments.js`
- `assets/Analytics/js/contact-us.js`
- `assets/Analytics/js/course-levels.js`
- `assets/Analytics/js/course-templates.js`
- `assets/Analytics/js/courses.js`
- `assets/Analytics/js/finance-templates.js`
- `assets/Analytics/js/finance.js`
- `assets/Analytics/js/instrument-templates.js`
- `assets/Analytics/js/instruments.js`
- `assets/Analytics/js/lesson-templates.js`
- `assets/Analytics/js/lessons.js`
- `assets/Analytics/js/media.js`
- `assets/Analytics/js/member-schedule-templates.js`
- `assets/Analytics/js/member-schedules.js`
- `assets/Analytics/js/message-templates.js`
- `assets/Analytics/js/messages.js`
- `assets/Analytics/js/national-holidays.js`
- `assets/Analytics/js/notification-templates.js`
- `assets/Analytics/js/notifications.js`
- `assets/Analytics/js/pages.js`
- `assets/Analytics/js/permission-templates.js`
- `assets/Analytics/js/permissions.js`
- `assets/Analytics/js/point-templates.js`
- `assets/Analytics/js/points.js`
- `assets/Analytics/js/post-categories.js`
- `assets/Analytics/js/posts.js`
- `assets/Analytics/js/profile-content.js`
- `assets/Analytics/js/report-templates.js`
- `assets/Analytics/js/reports.js`
- `assets/Analytics/js/role-templates.js`
- `assets/Analytics/js/roles.js`
- `assets/Analytics/js/schedule-templates.js`
- `assets/Analytics/js/schedules.js`
- `assets/Analytics/js/scheduling-rule-templates.js`
- `assets/Analytics/js/scheduling-rules.js`
- `assets/Analytics/js/student-templates.js`
- `assets/Analytics/js/students.js`
- `assets/Analytics/js/teacher-templates.js`
- `assets/Analytics/js/teachers.js`
- `assets/Analytics/js/term-templates.js`
- `assets/Analytics/js/terms.js`
- `assets/Analytics/js/tracking.js`
- `assets/Analytics/js/user-templates.js`
- `assets/Analytics/js/users.js`
