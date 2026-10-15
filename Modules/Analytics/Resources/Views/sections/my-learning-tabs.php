<?php
$learningTabActive = $active ?? 'dashboard';
$learningTabs = [
    'dashboard' => locale() === 'en' ? 'My dashboard' : 'داشبورد من',
    'my-terms' => locale() === 'en' ? 'My terms' : 'ترم‌های من',
    'my-classrooms' => locale() === 'en' ? 'My classes' : 'کلاس‌های من',
];
component('panel-page-tabs', [
    'active' => $learningTabActive,
    'label' => locale() === 'en' ? 'Learning pages' : 'صفحه‌های آموزشی',
    'tabs' => array_map(static fn (string $title): array => ['labels' => [$title, $title]], $learningTabs),
]); ?>
