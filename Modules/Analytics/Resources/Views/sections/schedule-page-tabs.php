<?php
if (!\Modules\System\Services\SiteAdminAccess::allows(auth()->user())) return;
component('panel-page-tabs', [
    'active' => $active ?? 'member-schedules',
    'label' => locale() === 'en' ? 'Scheduling' : 'برنامه زمانی',
    'tabs' => [
        'member-schedules' => ['labels' => ['زمان‌بندی اعضا', 'Member schedules']],
        'schedules' => ['labels' => ['برنامه کلاس‌ها', 'Class schedules']],
        'availabilities' => ['labels' => ['زمان‌های در دسترس', 'Availability']],
        'availability-exceptions' => ['labels' => ['استثناها', 'Exceptions']],
        'scheduling-rules' => ['labels' => ['قوانین زمان‌بندی', 'Scheduling rules']],
    ],
]);
