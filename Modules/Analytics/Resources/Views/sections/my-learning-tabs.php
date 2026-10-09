<?php
$learningTabActive = $active ?? 'dashboard';
$learningTabs = [
    'dashboard' => locale() === 'en' ? 'My dashboard' : 'داشبورد من',
    'my-terms' => locale() === 'en' ? 'My terms' : 'ترم‌های من',
    'my-classrooms' => locale() === 'en' ? 'My classes' : 'کلاس‌های من',
];
?>
<nav class="flex flex-wrap gap-3" aria-label="<?= e(locale() === 'en' ? 'Learning pages' : 'صفحه‌های آموزشی') ?>">
    <?php foreach ($learningTabs as $learningTabId => $learningTabLabel): ?>
        <a href="#<?= e($learningTabId) ?>" onclick="showSection('<?= e($learningTabId) ?>')" <?= $learningTabActive === $learningTabId ? 'aria-current="page"' : '' ?> class="rounded-2xl px-5 py-3 shadow-sm transition <?= $learningTabActive === $learningTabId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($learningTabLabel) ?></a>
    <?php endforeach; ?>
</nav>
