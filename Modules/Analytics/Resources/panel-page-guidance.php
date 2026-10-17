<?php
/** Canonical plain-text guidance shared by website views and authenticated API. */
return (static function (): array {
$result = [];
foreach (['fa', 'en'] as $language) {
    $pages = [
        'lessons' => [
        'title' => $language === 'en' ? 'The first step in managing your academy and branches' : 'اولین مرحله مدیریت امور آموزشگاه و شعبه ها',
        'paragraphs' => array_map(static fn (array $note): string => $note[$language === 'en' ? 1 : 0], [
                ['پیش از شروع این مرحله، اگر آموزشگاه شما چند شعبه دارد، باید تمام شعبه های اصلی و فرعی آموزشگاه را ثبت کنید.', 'Before starting, register all main and subsidiary branches if your academy has more than one branch.'],
                ['پس از ثبت آموزشگاه و شعبه های اصلی و فرعی آن، قبل از هر کار دیگر، فهرست تمام درس های ارائه شده در آموزشگاه و شعبه ها را به تفکیک وارد کنید.', 'After registering the academy and its branches, enter the complete lesson list for each organization before continuing.'],
                ['اگر درسی را برای آموزشگاه انتخاب کنید، نیازی نیست آن درس را برای شعبه ها ثبت کنید و به صورت خودکار امکان انتخاب درس های آموزشگاه برای شعبه های اصلی و فرعی آموزشگاه وجود دارد.', 'Academy lessons are automatically available to its branches; you do not need to register duplicate lessons for each branch.'],
                ['درس هایی که برای شعبه ها تعریف می کنید، در بخش های دیگر فقط برای شعبه مورد نظر قابل انتخاب خواهند بود.', 'Lessons defined for a branch are available only to that branch in other sections.'],
                ['درس ذخیره‌شده باید در فهرست و فرم‌های وابسته قابل انتخاب باشد. در صورت مشاهده‌نشدن، فیلتر شعبه و وضعیت "فعال" بودن یا نبودن درس را بررسی کنید.', 'Saved lessons should appear in the list and related forms. If a lesson is missing, check the branch filter and whether the lesson is active.'],
                ['ویرایش درسی که در دوره یا قرارداد یکی از اساتید استفاده شده فقط برای مدیر یا کاربر آموزشگاه و شعبهٔ مجاز امکان‌پذیر است.', 'Only the authorized academy or branch account or manager can edit a lesson used in a course or teacher contract.'],
                ['حذف درسی که در دوره یا قرارداد یکی از اساتید استفاده شده مجاز نیست.', 'A lesson used in any course or teacher contract cannot be deleted.'],
            ]),
    ],
        'teachers' => [
        'title' => $language === 'en' ? 'Before accepting students for a new teacher' : 'پیش از پذیرش هنرجو برای مدرس جدید',
        'paragraphs' => [$language === 'en' ? 'After registering a teacher, enter all available teaching days and times in Member schedules. Then create an open term for each available slot without a student or date, specifying the teacher, classroom, session count and time pattern. Set the first session date when activating enrollment.' : 'پس از ثبت استاد، همهٔ روزها و ساعت‌های قابل تدریس او را در «زمان‌بندی اعضا» ثبت کنید. سپس در «مدیریت ترم‌ها» برای هر ساعت قابل ارائه، ترم بازی بدون هنرجو و تاریخ با مدرس، کلاس برگزاری، تعداد جلسات و الگوی ساعت بسازید. تاریخ جلسهٔ اول هنگام فعال‌سازی ثبت‌نام تعیین می‌شود.'],
    ],
        'member-schedules' => [
        'title' => $language === 'en' ? 'Teacher availability and terms are separate steps' : 'برنامهٔ مدرس و ترم دو مرحلهٔ جدا هستند',
        'paragraphs' => [$language === 'en' ? 'Record the available days and times for each teacher here. Then create an open term for each available slot without a student or date, specifying the teacher, classroom, session count and time pattern. Set the first session date after agreeing with the student.' : 'روزها و ساعت‌های ممکن هر استاد را اینجا ثبت کنید. سپس در «مدیریت ترم‌ها» برای هر زمان قابل ارائه، ترم بازی بدون هنرجو و بدون تاریخ با مدرس، کلاس برگزاری، تعداد جلسات و الگوی ساعت بسازید. تاریخ جلسهٔ اول پس از توافق با هنرجو تعیین می‌شود.'],
    ],
        'terms' => [
        'title' => $language === 'en' ? 'Prepare terms before accepting enrollment requests' : 'ترم‌ها را پیش از درخواست ثبت‌نام آماده کنید',
        'paragraphs' => [$language === 'en' ? 'Create an open term for each available teaching slot without a student or date. Specify the teacher, physical or online classroom, number of sessions, recurrence and time pattern. Once agreed with the student, select the first session date during activation.' : 'برای ساعت‌های قابل ارائهٔ هر مدرس، ترم «باز» را بدون هنرجو و بدون تاریخ بسازید. مدرس، کلاس حضوری یا آنلاین، تعداد جلسات، دورهٔ تکرار و یک الگوی ساعت را مشخص کنید. پس از توافق با هنرجو، تاریخ جلسهٔ اول را هنگام فعال‌سازی انتخاب کنید.'],
    ],
        'pages' => [
        'title' => $language === 'en' ? 'How to edit' : 'روش ویرایش:',
        'paragraphs' => [$language === 'en' ? 'Select Edit content to open the actual page in a new tab. Click any text, image or icon and save its new value. Changes are saved directly to the database and shown to visitors.' : 'روی «ویرایش محتوا» بزنید؛ صفحه واقعی در تب جدید باز می‌شود. سپس روی هر متن، تصویر یا آیکن کلیک کنید و مقدار جدید را ثبت کنید. تغییرات مستقیماً در دیتابیس ذخیره و برای بازدیدکنندگان اعمال می‌شوند.'],
    ],
    ];
    foreach ($pages as $key => $guide) {
        $result[$key]['title'][$language] = $guide['title'];
        $result[$key]['body'][$language] = implode("\n\n", $guide['paragraphs']);
    }
}
return $result;
})();
