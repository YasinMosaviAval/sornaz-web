# بررسی تطابق پنل اپ و وب — ۲۰۲۶/۱۰/۲۲

مرجع این بررسی شاخهٔ فعلی پروژه وب پس از fetch و همگام‌سازی، رجیستری مسیرهای Analytics و Academy، کاتالوگ `MobilePanelCatalog` و مصرف‌کننده‌های Flutter است؛ حساب و دادهٔ واقعی سرور بررسی نشده است.

کاتالوگ شامل ۶۴ بخش است. آزمون parity تمام actionهای کاتالوگ را با مسیر واقعی و متد HTTP تطبیق می‌دهد و عملیات مدیریتی/شخصی وب را که نگاشت ندارند رد می‌کند. استثناها اکنون به پوسته و فرم HTML، مسیرهای نسخهٔ realtime و دو مسیر تماس با جایگزین اختصاصی امن موبایل محدودند. مسیرهای realtime جایگزین خواندن زنده و polling موجود اپ نیستند و برای نمایش امکانات به HTML نیاز نیست.

شکاف‌های رفع‌شده:

| API موبایل | مسیر وبِ بازاستفاده‌شده | رابط اپ |
|---|---|---|
| `students/user-lookup` و `members/user-lookup` | `POST /academy/admin/member-users/lookup` | جستجوی دقیق کاربر و انتخاب حساب موجود در ثبت هنرجو/کارمند |
| `terms/schedule-preview?id=…` | `POST /academy/admin/terms/{id}/student-schedule-preview` | تولید و ویرایش تاریخ جلسه‌ها، نمایش تداخل و جلوگیری از ثبت پیش‌نمایش دارای تداخل |
| `branches/register-account` | `POST /academy/admin/branches/registration` | فرم ثبت حساب شعبه با نام کاربری، گذرواژه، روش ثبت‌نام و تأیید قوانین |
| `branches/registration-otp` | `POST /academy/admin/branches/registration/send-otp` | ارسال کد و نگهداری اطلاعات فرم تا ذخیرهٔ نهایی |

انتخاب کاربر، اطلاعات هویتی خصوصی را در پاسخ جستجو افشا نمی‌کند؛ فرم فقط وجود فیلدهای ثبت‌شده را می‌فهمد و سرور با توکن مبهم، کاربر و سازمان مجاز را دوباره بررسی می‌کند. انتخاب‌های موبایل و OTP شعبه به تفکیک کاربر bearer، جدا از نشست مرورگر و OTP کارمند ذخیره می‌شوند. قانون OTP، احراز هویت و سرویس‌های اصلی سایت تغییر نکرده‌اند.

در اپ، عنوان تب اول سازمان به «مدیریت شعبه‌ها» و رنگ مرز شعبهٔ فرعی به `colorScheme.outline` تغییر کرده است. پس‌زمینهٔ کارت هم از تم مشترک خوانده می‌شود.

فایل‌های همین تغییر در وب: `MobilePanelCatalog.php`، `MobilePanelController.php`، `scripts/mobile_panel_parity_test.php`، `scripts/mobile_panel_test.php` و همین گزارش. دو فایل PHP زیر `Modules/Analytics/Services` و `Modules/Analytics/Controllers/Api` قرار دارند؛ scripts فقط برای تست‌اند.

فایل‌های اپ: `lib/screens/Site/panel_navigation.dart`، `branches_page.dart`، `panel_form.dart`، `panel_resource_page.dart` و `test/panel_new_web_workflows_test.dart`. فایل `panel_resource_page.dart` از قبل تغییرات جریان کلاس ترم دارد؛ انتقال آن باید انتخابی و همراه با بررسی تغییرات قبلی باشد. وابستگی تازه‌ای به OMR یا تحلیل تجربی اضافه نشده است.

انتشار: ابتدا دو فایل PHP وب را با هم منتشر کنید، سپس نسخهٔ اپ را منتشر کنید. migration یا کتابخانهٔ جدید لازم نیست. نسخهٔ قدیمی اپ با API قبلی سازگار می‌ماند؛ اپ جدید برای قابلیت‌های تازه به وب جدید نیاز دارد. برای بازگشت، همین فایل‌ها را به نسخهٔ قبلی برگردانید؛ دادهٔ تجاری یا schema تغییر نکرده است. نشست‌های موقت موبایل قابل انقضا هستند. هیچ انتشار، بیلد، commit یا push انجام نشده است.

آزمون‌های موفق: parity رجیستری ۶۴ بخش، قرارداد دیالوگ‌های وب، تست API با `--api-only` و جداسازی نشست/مجوز، fixture انتخاب کاربران موجود روی SQLite، تست‌های جدید Flutter و قرارداد فرم‌ها. آزمون کامل MySQL، ارسال واقعی پیامک/ایمیل و مرورگر/دستگاه روی سرور اصلی انجام نشده‌اند.

موارد باز: تست عمومی preview وب و چند تست گستردهٔ تب‌های قدیمی هنوز شکست دارند؛ بنابراین عبور از تمام چک‌های انتشار یا تطابق صددرصد رفتار رابط تأیید نشده است. تست تب تعطیلات، درخواست تکمیلی `/national-holidays/list` را در انتظار «آخرین درخواست» لحاظ نکرده است. ثبت شعبه و انتخاب حساب موجود پیش از انتشار باید با حساب مدیر و حساب فاقد مجوز در محیط تست بررسی شوند.

## موجودی بخش‌ها و عملیات کاتالوگ

این جدول فقط قراردادهای قابل ارائه را نشان می‌دهد؛ مجوزهای سرور در زمان اجرا آن را برای هر کاربر محدود می‌کنند.

| بخش | عنوان | عملیات |
|---|---|---|
| `dashboard` | داشبورد | `list` |
| `my-courses` | دوره‌های من | `list` |
| `my-terms` | ترم‌های من | `list` |
| `my-classrooms` | کلاس‌های من | `list` |
| `account` | حساب کاربری | `list`, `profile`, `bio`, `privacy`, `security`, `avatar`, `cover`, `document`, `backup`, `merge`, `end-session`, `delete-media`, `download-media`, `download-backup`, `cancel-merge`, `decide-merge`, `contact-send`, `contact-verify`, `contact-commit` |
| `chat` | گفتگوها | `list`, `create`, `messages`, `details`, `send`, `rename`, `avatar`, `members`, `remove-member`, `leave`, `delete`, `like`, `edit-message`, `delete-message`, `forward`, `file` |
| `messages` | پیام‌ها | `list`, `create`, `delete`, `read`, `unread` |
| `notifications` | اعلان‌ها | `list`, `create`, `delete`, `read`, `publish`, `expire` |
| `branches` | شعبه‌ها | `list`, `create`, `update`, `delete`, `register-account`, `registration-otp`, `status` |
| `branch-types` | انواع شعبه | `list`, `create`, `update`, `delete` |
| `members` | اساتید و کارکنان | `list`, `create`, `update`, `delete`, `registration-otp`, `user-lookup`, `status` |
| `students` | هنرجویان | `list`, `create`, `options`, `update`, `delete`, `status`, `user-lookup` |
| `classrooms` | کلاس‌ها | `list`, `create`, `update`, `delete`, `status` |
| `classroom-types` | انواع کلاس | `list`, `create`, `update`, `delete`, `create-from-classroom`, `status` |
| `courses` | دوره‌ها | `list`, `create`, `update`, `delete`, `status` |
| `course-levels` | سطوح دوره | `list`, `create`, `update`, `delete` |
| `terms` | ترم‌ها | `list`, `create`, `update`, `delete`, `cancel`, `cancel-approve`, `cancel-reject`, `restore`, `discounts`, `discount`, `attendance`, `available-times`, `batch`, `assign-student`, `classroom`, `schedule-preview`, `status` |
| `finance` | امور مالی و اقساط | `list`, `update`, `pay`, `offline`, `subscriptions`, `subscription-pay` |
| `subscriptions` | اشتراک آموزشگاه | `list`, `pay` |
| `schedules` | برنامه کلاس‌ها و حضور و غیاب | `list`, `attendance`, `week-bounds` |
| `member-schedules` | زمان‌های حضور اعضا | `list`, `create`, `update`, `delete`, `status` |
| `availability-exceptions` | استثناهای حضور | `list`, `create`, `update`, `delete`, `status` |
| `scheduling-rules` | قوانین زمان‌بندی | `list`, `create`, `update`, `delete`, `status` |
| `lessons` | سازها و درس‌ها | `list`, `create`, `update`, `delete`, `usage`, `create-lesson`, `status` |
| `gallery` | گالری | `list`, `create`, `update`, `delete` |
| `points` | امتیازها | `list`, `create`, `update`, `delete` |
| `users` | کاربران و دسترسی‌ها | `list`, `update`, `roles`, `permissions` |
| `awards` | پاداش‌ها و جوایز | `list`, `create`, `update`, `delete`, `status` |
| `certificates` | گواهی‌ها | `list`, `create`, `update`, `delete`, `status` |
| `experiences` | سوابق کاری | `list`, `create`, `update`, `delete`, `status` |
| `educations` | تحصیلات | `list`, `create`, `update`, `delete`, `status` |
| `events` | رویدادها | `list`, `create`, `update`, `delete`, `status` |
| `publications` | آثار منتشرشده | `list`, `create`, `update`, `delete`, `status` |
| `badges` | نشان‌ها | `list`, `create`, `update`, `delete`, `status` |
| `polls` | نظرسنجی‌ها | `list`, `create`, `update`, `delete`, `status`, `vote` |
| `posts` | نوشته‌ها | `list`, `create`, `update`, `delete`, `trash`, `restore`, `details` |
| `post-categories` | دسته‌بندی نوشته‌ها | `list`, `create`, `update`, `delete` |
| `comments` | دیدگاه‌ها | `list`, `update`, `delete`, `reply` |
| `media` | رسانه‌ها | `list`, `create`, `update`, `delete` |
| `tracking` | گزارش فعالیت‌ها | `list` |
| `national-holidays` | تعطیلات رسمی | `list`, `create`, `update`, `delete`, `status`, `academy-setting` |
| `roles` | نقش‌ها | `list`, `create`, `update`, `delete` |
| `permissions` | مجوزها | `list`, `create`, `update`, `delete` |
| `settings` | تنظیمات سایت | `list`, `save` |
| `reports` | گزارش‌ها و نمودارها | `list` |
| `guides` | راهنمای عملکردها | `list`, `update` |
| `pages` | برگه‌ها | `list` |
| `page-content` | محتوای برگه | `list`, `update` |
| `classroom-categories` | دسته‌بندی انواع کلاس | `list`, `create`, `update`, `delete` |
| `availabilities` | برنامه زمانی سازمان | `list`, `create`, `update`, `delete` |
| `my-points` | امتیازهای من | `list` |
| `my-settings` | تنظیمات من | `list`, `save` |
| `my-gallery` | گالری من | `list`, `create`, `update`, `delete` |
| `my-lessons` | درس‌های من | `list`, `create`, `update`, `delete` |
| `my-schedule` | برنامه زمانی من | `list`, `create`, `update`, `delete` |
| `my-finance` | امور مالی من | `list`, `update` |
| `inline-translations` | ترجمه‌های رابط سایت | `list`, `update`, `create` |
| `waiting-approvals` | تأیید ثبت‌نام هنرجویان | `list`, `chat`, `approve` |
| `waiting-schedules` | نهایی‌کردن ثبت‌نام هنرجویان | `list`, `chat`, `finalize` |
| `my-waiting` | درخواست‌های ثبت‌نام من | `list`, `update` |
| `organization-holidays` | تعطیلات | `list`, `create`, `update`, `delete`, `status` |
| `employee-leaves` | مرخصی‌ها | `list`, `create`, `update`, `delete`, `status` |
| `page-guidance` | توضیحات صفحات | `list`, `save` |
| `term-discounts` | تخفیف‌ها | `list`, `create`, `update`, `delete` |
