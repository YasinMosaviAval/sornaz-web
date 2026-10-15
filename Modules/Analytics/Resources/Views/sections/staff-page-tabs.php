<?php
$pageTabs = array_map(static fn (array $labels): array => ['labels' => $labels], ['teachers' => ['قراردادها', 'Contracts'], 'users' => ['نقش‌ها و دسترسی‌ها', 'Roles and permissions']]);
component('panel-page-tabs', [
    'active' => $active ?? 'teachers',
    'label' => locale() === 'en' ? 'Employee pages' : 'صفحه‌های کارمندها',
    'tabs' => $pageTabs,
]); ?>
