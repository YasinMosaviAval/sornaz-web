<?php
$pageTabs = array_map(static fn (array $labels): array => ['labels' => $labels], ['classroom-types' => ['انواع کلاس', 'Classroom types'], 'classrooms' => ['کلاس‌ها', 'Classrooms']]);
if (isset($pageTabs['classroom-types'])) { $pageTabs['classroom-types']['hidden'] = true; $pageTabs['classroom-types']['attributes'] = 'data-classroom-types-tab'; }
component('panel-page-tabs', [
    'active' => $active ?? 'classrooms',
    'label' => locale() === 'en' ? 'Classroom pages' : 'صفحه‌های کلاس',
    'tabs' => $pageTabs,
]); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-classroom-types-tab]').forEach(button => button.classList.toggle('hidden', !document.getElementById('classroom-types')));
});
</script>
