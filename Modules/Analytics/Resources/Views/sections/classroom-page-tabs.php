<?php $classroomTabActive = $active ?? 'classrooms'; ?>
<nav class="sticky top-0 z-20 mb-5 flex flex-wrap gap-3 bg-slate-50/95 backdrop-blur dark:bg-slate-950/95" aria-label="<?= e(locale() === 'en' ? 'Classroom pages' : 'صفحه‌های کلاس') ?>">
    <?php foreach (['classrooms' => ['کلاس‌ها', 'Classrooms'], 'classroom-types' => ['انواع کلاس', 'Classroom types']] as $classroomTabId => $classroomTabLabels): ?>
    <button type="button" onclick="showSection('<?= e($classroomTabId) ?>')" <?= $classroomTabActive === $classroomTabId ? 'aria-current="page"' : '' ?> <?= $classroomTabId === 'classroom-types' ? 'data-classroom-types-tab' : '' ?> class="<?= $classroomTabId === 'classroom-types' ? 'hidden ' : '' ?>rounded-2xl px-5 py-3 shadow-sm transition <?= $classroomTabActive === $classroomTabId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50 dark:bg-slate-800 dark:text-white' ?>"><?= e($classroomTabLabels[locale() === 'en' ? 1 : 0]) ?></button>
    <?php endforeach; ?>
</nav>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-classroom-types-tab]').forEach(button => button.classList.toggle('hidden', !document.getElementById('classroom-types')));
});
</script>
