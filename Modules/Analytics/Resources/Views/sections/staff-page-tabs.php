<?php $staffTabActive = $active ?? 'teachers'; ?>
<nav class="sticky top-0 z-20 mb-5 flex flex-wrap gap-3 bg-slate-50/95 backdrop-blur dark:bg-slate-950/95" aria-label="<?= e(locale() === 'en' ? 'Employee pages' : 'صفحه‌های کارمندها') ?>">
    <?php foreach (['teachers' => ['قراردادها', 'Contracts'], 'users' => ['نقش‌ها و دسترسی‌ها', 'Roles and permissions']] as $staffTabId => $staffTabLabels): ?>
    <button type="button" onclick="showSection('<?= e($staffTabId) ?>')" <?= $staffTabActive === $staffTabId ? 'aria-current="page"' : '' ?> class="rounded-2xl px-5 py-3 shadow-sm transition <?= $staffTabActive === $staffTabId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($staffTabLabels[locale() === 'en' ? 1 : 0]) ?></button>
    <?php endforeach; ?>
</nav>
