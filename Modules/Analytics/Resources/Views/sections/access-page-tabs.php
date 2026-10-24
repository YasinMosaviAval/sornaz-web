<?php
$tabs = [];
if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())) {
    $tabs['roles'] = ['labels' => ['نقش‌ها', 'Roles']];
    $tabs['permissions'] = ['labels' => ['دسترسی‌ها', 'Permissions']];
}
$tabs['users'] = ['labels' => ['نقش‌ها و دسترسی‌ها', 'Roles and permissions']];
$tabs += [
    'teachers' => ['labels' => ['قراردادها', 'Contracts']],
    'member-schedules' => ['labels' => ['برنامه زمانی اعضا', 'Member schedules']],
    'employee-leaves' => ['labels' => ['مرخصی‌ها', 'Leaves']],
];
component('panel-page-tabs', ['active' => $active ?? 'users', 'tabs' => $tabs, 'onSelect' => 'showStaffPage', 'label' => locale() === 'en' ? 'Users' : 'کاربران']);
