<?php
$pageTabs = array_map(static fn (array $labels): array => ['labels' => $labels], ['teachers' => ['قراردادها', 'Contracts'], 'users' => ['نقش‌ها و دسترسی‌ها', 'Roles and permissions'], 'member-schedules' => ['برنامه زمانی اعضا', 'Member schedules'], 'employee-leaves' => ['مرخصی‌ها', 'Leaves']]);
if (!empty($personal)) $pageTabs = array_intersect_key($pageTabs, ['member-schedules' => true]);
component('panel-page-tabs', [
    'active' => $active ?? 'teachers',
    'label' => locale() === 'en' ? 'Employee pages' : 'صفحه‌های کارمندها',
    'tabs' => $pageTabs,
    'onSelect' => 'showStaffPage',
]); ?>
