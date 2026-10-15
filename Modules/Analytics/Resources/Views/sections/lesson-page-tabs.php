<?php $lessonTabActive = $active ?? 'lessons'; ?>
<nav class="sticky top-0 z-20 mb-5 flex flex-wrap gap-3 bg-slate-50/95 backdrop-blur dark:bg-slate-950/95" style="margin-top:0" aria-label="<?= e(locale() === 'en' ? 'Lesson pages' : 'صفحه‌های درس') ?>">
    <?php foreach (['lessons' => ['درس‌ها', 'Lessons'], 'course-levels' => ['سطح درس‌ها', 'Lesson levels']] as $lessonTabId => $lessonTabLabels): ?>
    <button type="button" onclick="showSection('<?= e($lessonTabId) ?>')" <?= $lessonTabActive === $lessonTabId ? 'aria-current="page"' : '' ?> class="rounded-2xl px-5 py-3 shadow-sm transition <?= $lessonTabActive === $lessonTabId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($lessonTabLabels[locale() === 'en' ? 1 : 0]) ?></button>
    <?php endforeach; ?>
</nav>
