<?php
$pageTabs = array_map(static fn (array $labels): array => ['labels' => $labels], ['course-levels' => ['سطح درس‌ها', 'Lesson levels'], 'lessons' => ['درس‌ها', 'Lessons'], 'courses' => ['دوره‌ها', 'Courses']]);
if (($includeCourses ?? true) === false) unset($pageTabs['courses']);
component('panel-page-tabs', [
    'active' => $active ?? 'lessons',
    'label' => locale() === 'en' ? 'Lesson pages' : 'صفحه‌های درس',
    'tabs' => $pageTabs,
]); ?>
