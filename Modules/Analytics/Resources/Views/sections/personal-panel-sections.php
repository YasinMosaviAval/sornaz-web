<?php
$personalUserId = (int) auth()->id();
$personalLabel = static fn (string $fa, string $en): string => locale() === 'en' ? $en : $fa;
$personalTextMap = [];
$personalText = static function (string $table, int $id, string $field, string $fallback = '') use (&$personalTextMap): string {
    return (string) ($personalTextMap[$table][$id][$field] ?? $fallback);
};
$personalPreview = ($previewPanelMode ?? null) === 'user';
if ($personalPreview) {
    $personalLessonData = ['items' => [['user_lesson_id' => 1, 'lesson_id' => 1, 'level_id' => 1, 'is_primary' => 1, 'status' => 'active', 'start_date' => '2026-10-09']], 'lessons' => [['lesson_id' => 1]], 'levels' => [['level_id' => 1]]];
    $personalScheduleData = ['items' => [['user_availability_id' => 1, 'repeat_period' => 'week', 'date' => null, 'day_of_week' => 'saturday', 'timezone_id' => 1, 'start_time' => '10:00', 'end_time' => '12:00', 'status' => 'available']], 'timezones' => [['timezone_id' => 1, 'timezone' => 'Asia/Tehran']]];
    $personalFinanceData = ['invoices' => [['term_invoice_id' => 1, 'term_id' => 1, 'payable_amount' => 2500000, 'status' => 'issued', 'due_date' => '2026-10-30']], 'installments' => [['invoice_id' => 1, 'installment_number' => 1, 'amount' => 1250000, 'status' => 'pending', 'due_date' => '2026-10-30']], 'payments' => [['invoice_id' => 1, 'amount' => 500000, 'method' => 'card', 'status' => 'approved', 'paid_at' => '2026-10-09']]];
} else {
    $personalData = new \Modules\Analytics\Services\PersonalPanelDataService();
    $personalLessonData = $personalData->lessons($personalUserId, false);
    $personalScheduleData = $personalData->schedules($personalUserId, false);
    $personalFinanceData = $personalData->finance($personalUserId, false);
}
$personalLessons = $personalLessonData['items'];
$personalSchedules = $personalScheduleData['items'];
$personalInvoices = $personalFinanceData['invoices'];
$personalInvoiceIds = array_map(static fn (array $row): int => (int) $row['term_invoice_id'], $personalInvoices);
$personalInstallments = $personalFinanceData['installments'];
$personalPayments = $personalFinanceData['payments'];
$personalDays = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه', 'thursday' => 'پنجشنبه', 'friday' => 'جمعه'];
$personalRepeats = ['week' => 'هفتگی', '2-week' => 'دو هفته', '3-week' => 'سه هفته', '4-week' => 'چهار هفته', 'month' => 'ماهانه', 'year' => 'سالانه', 'none' => 'بی‌تکرار'];
$personalTimezoneCatalog = $personalScheduleData['timezones'];
$personalTimezoneMap = [];
foreach ($personalTimezoneCatalog as $timezone) $personalTimezoneMap[(int) $timezone['timezone_id']] = (string) $timezone['timezone'];
$personalScheduleGroups = [];
foreach ($personalSchedules as $schedule) {
    $key = implode('|', [(string) ($schedule['repeat_period'] ?? 'week'), (string) ($schedule['date'] ?? ''), (string) ($schedule['day_of_week'] ?? '')]);
    if (!isset($personalScheduleGroups[$key])) $personalScheduleGroups[$key] = ['id' => (int) $schedule['user_availability_id'], 'ranges' => []];
    $personalScheduleGroups[$key]['ranges'][] = ['start' => substr((string) ($schedule['start_time'] ?? ''), 0, 5), 'end' => substr((string) ($schedule['end_time'] ?? ''), 0, 5), 'status' => ($schedule['status'] ?? '') === 'unavailable' ? 'غیرفعال' : 'فعال'];
}
$personalLessonCatalog = $personalLessonData['lessons'];
$personalLevelCatalog = $personalLessonData['levels'];
$personalTranslationIds = [
    'lessons' => array_column($personalLessonCatalog, 'lesson_id'),
    'levels' => array_column($personalLevelCatalog, 'level_id'),
    'user_lessons' => array_column($personalLessons, 'user_lesson_id'),
    'user_availabilities' => array_column($personalSchedules, 'user_availability_id'),
    'academy_branch_course_terms' => array_column($personalInvoices, 'term_id'),
    'academy_branch_course_term_invoices' => $personalInvoiceIds,
];
if ($personalPreview) $personalTextMap = ['lessons' => [1 => ['title' => 'پیانو']], 'levels' => [1 => ['title' => 'مقدماتی']], 'user_lessons' => [1 => ['summary' => 'تمرین مقدماتی']], 'user_availabilities' => [1 => ['summary' => 'تمرین هفتگی']], 'academy_branch_course_terms' => [1 => ['title' => 'ترم نمونه']], 'academy_branch_course_term_invoices' => [1 => ['title' => 'فاکتور نمونه']]];
foreach ($personalPreview ? [] : $personalTranslationIds as $table => $ids) {
    if (!$ids) continue;
    foreach (\Core\database\DB::table('translations')->where('table_name', $table)->whereIn('table_id', array_values(array_unique(array_map('intval', $ids))))->where('locale', locale() === 'en' ? 'en' : 'fa')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->get() as $translation) {
        $id = (int) $translation['table_id'];
        $field = (string) $translation['field'];
        if (!isset($personalTextMap[$table][$id][$field])) $personalTextMap[$table][$id][$field] = (string) $translation['value'];
    }
}
?>
<section id="lessons" class="section hidden space-y-5" data-personal-section="1">
    <h1 class="hidden"><?= e($personalLabel('درس‌ها', 'Lessons')) ?></h1>
    <?php component('lesson-page-tabs', ['active' => 'lessons']); ?>
    <div class="flex flex-wrap gap-3"><button type="button" data-personal-open="lesson" class="rounded-2xl bg-indigo-600 px-5 py-3 text-white"><?= e($personalLabel('افزودن درس', 'Add lesson')) ?></button><button type="button" data-personal-export="lessons" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-excel text-green-600"></i> <?= e($personalLabel('خروجی اکسل', 'Export Excel')) ?></button><button type="button" data-personal-print="lessons" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-pdf text-red-600"></i> <?= e($personalLabel('خروجی PDF', 'Export PDF')) ?></button></div>
    <form id="personalLessonForm" class="hidden grid gap-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900 md:grid-cols-3"><input type="hidden" name="id" value="0"><label class="text-sm"><?= e($personalLabel('درس', 'Lesson')) ?><select name="lesson_id" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><?php foreach ($personalLessonCatalog as $item): ?><option value="<?= (int) $item['lesson_id'] ?>"><?= e($personalText('lessons', (int) $item['lesson_id'], 'title', 'درس ' . (int) $item['lesson_id'])) ?></option><?php endforeach; ?></select></label><label class="text-sm"><?= e($personalLabel('سطح', 'Level')) ?><select name="level_id" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><?php foreach ($personalLevelCatalog as $item): ?><option value="<?= (int) $item['level_id'] ?>"><?= e($personalText('levels', (int) $item['level_id'], 'title', 'سطح ' . (int) $item['level_id'])) ?></option><?php endforeach; ?></select></label><label class="text-sm"><?= e($personalLabel('تاریخ شروع', 'Start date')) ?><input type="date" name="start_date" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_primary" value="1"><?= e($personalLabel('درس اصلی', 'Primary lesson')) ?></label><label class="text-sm md:col-span-2"><?= e($personalLabel('خلاصه', 'Summary')) ?><input name="summary" maxlength="500" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="text-sm md:col-span-3"><?= e($personalLabel('توضیحات', 'Description')) ?><textarea name="description" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></textarea></label><div class="flex gap-2 md:col-span-3"><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><?= e($personalLabel('ذخیره', 'Save')) ?></button><button type="button" data-personal-cancel="lesson" class="rounded-xl border px-5 py-3"><?= e($personalLabel('انصراف', 'Cancel')) ?></button></div><p class="hidden text-sm text-red-600 md:col-span-3" data-personal-error></p></form>
    <div class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-900 md:grid-cols-2"><input data-personal-filter="lessons" type="search" placeholder="<?= e($personalLabel('جست‌وجوی درس', 'Search lessons')) ?>" class="rounded-xl border p-3 dark:bg-slate-800"><select data-personal-status="lessons" class="rounded-xl border p-3 dark:bg-slate-800"><option value=""><?= e($personalLabel('همه وضعیت‌ها', 'All statuses')) ?></option><option value="active">active</option><option value="pending">pending</option><option value="inactive">inactive</option></select></div>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm dark:bg-slate-900"><?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' class="w-full min-w-[800px] text-sm"';
$panelTable0['headAttributes'] = '';
$panelTable0['headerRowAttributes'] = ' class="border-b dark:border-slate-700"';
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('درس', 'Lesson')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('سطح', 'Level')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('اصلی', 'Primary')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('خلاصه', 'Summary')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('وضعیت', 'Status')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('تاریخ شروع', 'Start date')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0CellAttributes = ' class="p-4 text-right"';
$panelTable0['columns'][] = ['html' => e($personalLabel('عملیات', 'Actions')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
$panelTable0['bodyAttributes'] = '';
foreach ($personalLessons as $lesson):
$panelTable0Row = ['cells' => []];
ob_start(); ?> class="border-b last:border-0 dark:border-slate-700" data-personal-status-value="<?= e((string) ($lesson['status'] ?? '')) ?>"<?php $panelTable0Row['attributes'] = ob_get_clean();
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($personalText('lessons', (int) $lesson['lesson_id'], 'title', 'درس ' . (int) $lesson['lesson_id'])), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($personalText('levels', (int) $lesson['level_id'], 'title', '—')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => !empty($lesson['is_primary']) ? '✓' : '—', 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e($personalText('user_lessons', (int) $lesson['user_lesson_id'], 'summary', '—')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e((string) ($lesson['status'] ?? '—')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e((string) ($lesson['start_date'] ?? '—')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0CellAttributes = ' class="p-4"';
ob_start(); ?><button type="button" data-personal-edit-lesson='<?= e(json_encode(['id' => (int) $lesson['user_lesson_id'], 'lesson_id' => (int) $lesson['lesson_id'], 'level_id' => (int) $lesson['level_id'], 'start_date' => (string) ($lesson['start_date'] ?? ''), 'is_primary' => (int) ($lesson['is_primary'] ?? 0), 'summary' => $personalText('user_lessons', (int) $lesson['user_lesson_id'], 'summary'), 'description' => $personalText('user_lessons', (int) $lesson['user_lesson_id'], 'description')], JSON_UNESCAPED_UNICODE)) ?>' class="text-indigo-600"><?= e($personalLabel('ویرایش', 'Edit')) ?></button><?php $panelTable0Row['cells'][] = ['html' => ob_get_clean(), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
endforeach;
if (!$personalLessons):
$panelTable0Row = ['cells' => []];
ob_start();
$panelTable0Row['attributes'] = ob_get_clean();
$panelTable0CellAttributes = ' colspan="7" class="p-6 text-center text-gray-500"';
$panelTable0Row['cells'][] = ['html' => e($personalLabel('هنوز درسی برای شما ثبت نشده است.', 'No lessons have been recorded for you.')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
endif;
component('panel-table-template', $panelTable0); unset($panelTable0); ?></div>
</section>
<section id="course-levels" class="section hidden space-y-5" data-personal-section="1">
    <h1 class="hidden"><?= e($personalLabel('سطح درس‌ها', 'Lesson levels')) ?></h1>
    <?php component('lesson-page-tabs', ['active' => 'course-levels']); ?>
    <div class="rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900">
        <?php if ($personalLevelCatalog): ?>
        <ul class="divide-y dark:divide-slate-700"><?php foreach ($personalLevelCatalog as $level): ?><li class="py-3"><?= e($personalText('levels', (int) $level['level_id'], 'title', 'سطح ' . (int) $level['level_id'])) ?></li><?php endforeach; ?></ul>
        <?php else: ?><p class="text-gray-500"><?= e($personalLabel('سطحی ثبت نشده است.', 'No levels available.')) ?></p><?php endif; ?>
    </div>
</section>
<section id="member-schedules" class="section hidden space-y-5" data-personal-section="1">
    <h1 class="hidden"><?= e($personalLabel('برنامه زمانی', 'Schedule')) ?></h1>
    <div class="flex flex-wrap gap-3"><button type="button" data-personal-open="schedule" class="rounded-2xl bg-indigo-600 px-5 py-3 text-white"><?= e($personalLabel('افزودن زمان‌بندی', 'Add schedule')) ?></button><button type="button" data-personal-export="member-schedules" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-excel text-green-600"></i> <?= e($personalLabel('خروجی اکسل', 'Export Excel')) ?></button><button type="button" data-personal-print="member-schedules" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-pdf text-red-600"></i> <?= e($personalLabel('خروجی PDF', 'Export PDF')) ?></button></div>
    <form id="personalScheduleForm" class="hidden grid gap-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900 md:grid-cols-3">
        <input type="hidden" name="id" value="0">
        <label class="text-sm"><?= e($personalLabel('دوره تکرار', 'Repeat period')) ?><select name="repeatPeriod" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><?php foreach ($personalRepeats as $repeat): ?><option value="<?= e($repeat) ?>"><?= e($repeat) ?></option><?php endforeach; ?></select></label>
        <label class="text-sm" data-personal-day><?= e($personalLabel('روز هفته', 'Weekday')) ?><select name="day" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><?php foreach ($personalDays as $key => $day): ?><option value="<?= e($day) ?>"><?= e($day) ?></option><?php endforeach; ?></select></label>
        <label class="text-sm hidden" data-personal-date><?= e($personalLabel('اولین تاریخ', 'First date')) ?><input type="date" name="repeatDate" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label>
        <label class="text-sm"><?= e($personalLabel('منطقه زمانی', 'Timezone')) ?><select name="timezone" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><?php foreach ($personalTimezoneCatalog as $timezone): ?><option value="<?= e((string) $timezone['timezone']) ?>" <?= $timezone['timezone'] === 'Asia/Tehran' ? 'selected' : '' ?>><?= e((string) $timezone['timezone']) ?></option><?php endforeach; ?></select></label>
        <div class="md:col-span-3"><div data-personal-ranges class="space-y-3"></div><button type="button" data-personal-add-range class="mt-3 rounded-xl border px-4 py-2 text-indigo-600"><?= e($personalLabel('افزودن بازه زمانی', 'Add time range')) ?></button></div>
        <label class="text-sm md:col-span-3"><?= e($personalLabel('خلاصه', 'Summary')) ?><input name="summary" maxlength="500" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label>
        <label class="text-sm md:col-span-3"><?= e($personalLabel('توضیحات', 'Description')) ?><textarea name="description" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></textarea></label>
        <div class="flex gap-2 md:col-span-3"><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><?= e($personalLabel('ذخیره', 'Save')) ?></button><button type="button" data-personal-cancel="schedule" class="rounded-xl border px-5 py-3"><?= e($personalLabel('انصراف', 'Cancel')) ?></button></div>
        <p class="hidden text-sm text-red-600 md:col-span-3" data-personal-error></p>
    </form>
    <div class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-900 md:grid-cols-2"><input data-personal-filter="member-schedules" type="search" placeholder="<?= e($personalLabel('جست‌وجوی برنامه زمانی', 'Search schedules')) ?>" class="rounded-xl border p-3 dark:bg-slate-800"><select data-personal-status="member-schedules" class="rounded-xl border p-3 dark:bg-slate-800"><option value=""><?= e($personalLabel('همه وضعیت‌ها', 'All statuses')) ?></option><option value="available">available</option><option value="unavailable">unavailable</option><option value="pending">pending</option></select></div>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm dark:bg-slate-900"><?php $panelTable1 = ['columns' => [], 'rows' => []];
$panelTable1['attributes'] = ' class="w-full min-w-[850px] text-sm"';
$panelTable1['headAttributes'] = '';
$panelTable1['headerRowAttributes'] = ' class="border-b dark:border-slate-700"';
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('روز یا تاریخ', 'Day or date')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('شروع', 'Start')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('پایان', 'End')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('تکرار', 'Repeat')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('خلاصه', 'Summary')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('وضعیت', 'Status')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1CellAttributes = ' class="p-4 text-right"';
$panelTable1['columns'][] = ['html' => e($personalLabel('عملیات', 'Actions')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'th'];
$panelTable1['bodyAttributes'] = '';
foreach ($personalSchedules as $schedule):
        $scheduleId = (int) $schedule['user_availability_id'];
        $groupKey = implode('|', [(string) ($schedule['repeat_period'] ?? 'week'), (string) ($schedule['date'] ?? ''), (string) ($schedule['day_of_week'] ?? '')]);
        $scheduleEdit = [
            'id' => $personalScheduleGroups[$groupKey]['id'],
            'day' => $personalDays[$schedule['day_of_week'] ?? ''] ?? 'شنبه',
            'repeatPeriod' => $personalRepeats[$schedule['repeat_period'] ?? 'week'] ?? 'هفتگی',
            'repeatDate' => (string) ($schedule['date'] ?? ''),
            'timezone' => $personalTimezoneMap[(int) ($schedule['timezone_id'] ?? 0)] ?? 'Asia/Tehran',
            'ranges' => $personalScheduleGroups[$groupKey]['ranges'],
            'summary' => $personalText('user_availabilities', $scheduleId, 'summary'),
            'description' => $personalText('user_availabilities', $scheduleId, 'description'),
        ];

$panelTable1Row = ['cells' => []];
ob_start(); ?> class="border-b last:border-0 dark:border-slate-700" data-personal-status-value="<?= e((string) ($schedule['status'] ?? '')) ?>"<?php $panelTable1Row['attributes'] = ob_get_clean();
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => e((string) (($schedule['date'] ?? '') ?: ($personalDays[$schedule['day_of_week'] ?? ''] ?? '—'))), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4" dir="ltr"';
$panelTable1Row['cells'][] = ['html' => e(substr((string) ($schedule['start_time'] ?? ''), 0, 5)), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4" dir="ltr"';
$panelTable1Row['cells'][] = ['html' => e(substr((string) ($schedule['end_time'] ?? ''), 0, 5)), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => e((string) ($schedule['repeat_period'] ?? '—')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => e($personalText('user_availabilities', $scheduleId, 'summary', '—')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
$panelTable1Row['cells'][] = ['html' => e((string) ($schedule['status'] ?? '—')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1CellAttributes = ' class="p-4"';
ob_start(); ?><button type="button" data-personal-edit-schedule='<?= e(json_encode($scheduleEdit, JSON_UNESCAPED_UNICODE)) ?>' class="text-indigo-600"><?= e($personalLabel('ویرایش', 'Edit')) ?></button><?php $panelTable1Row['cells'][] = ['html' => ob_get_clean(), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1['rows'][] = $panelTable1Row;
endforeach;
if (!$personalSchedules):
$panelTable1Row = ['cells' => []];
ob_start();
$panelTable1Row['attributes'] = ob_get_clean();
$panelTable1CellAttributes = ' colspan="7" class="p-6 text-center text-gray-500"';
$panelTable1Row['cells'][] = ['html' => e($personalLabel('هنوز برنامه زمانی برای شما ثبت نشده است.', 'No schedule has been recorded for you.')), 'attributes' => $panelTable1CellAttributes, 'tag' => 'td'];
$panelTable1['rows'][] = $panelTable1Row;
endif;
component('panel-table-template', $panelTable1); unset($panelTable1); ?></div>
</section>
<section id="finance" class="section hidden space-y-5" data-personal-section="1">
    <h1 class="hidden"><?= e($personalLabel('امور مالی', 'Finance')) ?></h1>
    <div class="flex flex-wrap gap-3"><button type="button" data-personal-export="finance" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-excel text-green-600"></i> <?= e($personalLabel('خروجی اکسل', 'Export Excel')) ?></button><button type="button" data-personal-print="finance" class="rounded-2xl border px-5 py-3"><i class="fas fa-file-pdf text-red-600"></i> <?= e($personalLabel('خروجی PDF', 'Export PDF')) ?></button></div>
    <form id="personalInvoiceForm" class="hidden grid gap-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900 md:grid-cols-3"><input type="hidden" name="id" value="0"><label class="text-sm"><?= e($personalLabel('مبلغ قابل پرداخت', 'Amount due')) ?><input name="amount" type="number" min="0" step="0.01" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="text-sm"><?= e($personalLabel('وضعیت', 'Status')) ?><select name="statusCode" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"><option value="draft">draft</option><option value="issued">issued</option><option value="canceled">canceled</option></select></label><label class="text-sm"><?= e($personalLabel('سررسید', 'Due date')) ?><input name="dueDate" type="date" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="text-sm md:col-span-3"><?= e($personalLabel('شرح تراکنش', 'Invoice title')) ?><input name="title" required class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="text-sm md:col-span-3"><?= e($personalLabel('خلاصه', 'Summary')) ?><input name="summary" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></label><label class="text-sm md:col-span-3"><?= e($personalLabel('توضیحات', 'Description')) ?><textarea name="description" class="mt-2 w-full rounded-xl border p-3 dark:bg-slate-800"></textarea></label><div class="flex gap-2 md:col-span-3"><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"><?= e($personalLabel('ذخیره', 'Save')) ?></button><button type="button" data-personal-cancel="invoice" class="rounded-xl border px-5 py-3"><?= e($personalLabel('انصراف', 'Cancel')) ?></button></div><p class="hidden text-sm text-red-600 md:col-span-3" data-personal-error></p></form>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm dark:bg-slate-900"><?php $panelTable2 = ['columns' => [], 'rows' => []];
$panelTable2['attributes'] = ' class="w-full min-w-[650px] text-sm"';
$panelTable2['headAttributes'] = '';
$panelTable2['headerRowAttributes'] = ' class="border-b dark:border-slate-700"';
$panelTable2CellAttributes = ' class="p-4 text-right"';
$panelTable2['columns'][] = ['html' => e($personalLabel('ترم', 'Term')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'th'];
$panelTable2CellAttributes = ' class="p-4 text-right"';
$panelTable2['columns'][] = ['html' => e($personalLabel('مبلغ قابل پرداخت', 'Amount due')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'th'];
$panelTable2CellAttributes = ' class="p-4 text-right"';
$panelTable2['columns'][] = ['html' => e($personalLabel('وضعیت', 'Status')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'th'];
$panelTable2CellAttributes = ' class="p-4 text-right"';
$panelTable2['columns'][] = ['html' => e($personalLabel('سررسید', 'Due date')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'th'];
$panelTable2CellAttributes = ' class="p-4 text-right"';
$panelTable2['columns'][] = ['html' => e($personalLabel('عملیات', 'Actions')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'th'];
$panelTable2['bodyAttributes'] = '';
foreach ($personalInvoices as $invoice): $invoiceId = (int) $invoice['term_invoice_id']; $invoiceLocked = (bool) array_filter($personalPayments, static fn (array $payment): bool => (int) $payment['invoice_id'] === $invoiceId) || (bool) array_filter($personalInstallments, static fn (array $installment): bool => (int) $installment['invoice_id'] === $invoiceId && in_array((string) ($installment['status'] ?? ''), ['paid', 'partially_paid'], true));
$panelTable2Row = ['cells' => []];
$panelTable2Row['attributes'] = ' class="border-b last:border-0 dark:border-slate-700"';
$panelTable2CellAttributes = ' class="p-4"';
$panelTable2Row['cells'][] = ['html' => e($personalText('academy_branch_course_terms', (int) $invoice['term_id'], 'title', 'ترم ' . (int) $invoice['term_id'])), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-4"';
$panelTable2Row['cells'][] = ['html' => e(number_format((float) ($invoice['payable_amount'] ?? 0))), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-4"';
$panelTable2Row['cells'][] = ['html' => e((string) ($invoice['status'] ?? '—')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-4"';
$panelTable2Row['cells'][] = ['html' => e((string) ($invoice['due_date'] ?? '—')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2CellAttributes = ' class="p-4"';
ob_start();
if (!$invoiceLocked): ?><button type="button" data-personal-edit-invoice='<?= e(json_encode(['id' => $invoiceId, 'amount' => (string) ($invoice['payable_amount'] ?? ''), 'statusCode' => (string) ($invoice['status'] ?? 'draft'), 'dueDate' => (string) ($invoice['due_date'] ?? ''), 'title' => $personalText('academy_branch_course_term_invoices', $invoiceId, 'title', 'فاکتور ' . $invoiceId), 'summary' => $personalText('academy_branch_course_term_invoices', $invoiceId, 'summary'), 'description' => $personalText('academy_branch_course_term_invoices', $invoiceId, 'description')], JSON_UNESCAPED_UNICODE)) ?>' class="text-indigo-600"><?= e($personalLabel('ویرایش', 'Edit')) ?></button><?php else: ?><?= e($personalLabel('به‌دلیل سابقهٔ پرداخت قابل ویرایش نیست', 'Locked by payment history'));
endif;
$panelTable2Row['cells'][] = ['html' => ob_get_clean(), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2['rows'][] = $panelTable2Row;
endforeach;
if (!$personalInvoices):
$panelTable2Row = ['cells' => []];
ob_start();
$panelTable2Row['attributes'] = ob_get_clean();
$panelTable2CellAttributes = ' colspan="5" class="p-6 text-center text-gray-500"';
$panelTable2Row['cells'][] = ['html' => e($personalLabel('هنوز فاکتوری برای شما ثبت نشده است.', 'No invoices have been recorded for you.')), 'attributes' => $panelTable2CellAttributes, 'tag' => 'td'];
$panelTable2['rows'][] = $panelTable2Row;
endif;
component('panel-table-template', $panelTable2); unset($panelTable2); ?></div>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm dark:bg-slate-900"><?php $panelTable3 = ['columns' => [], 'rows' => []];
$panelTable3['attributes'] = ' class="w-full min-w-[650px] text-sm"';
$panelTable3['headAttributes'] = '';
$panelTable3['headerRowAttributes'] = ' class="border-b dark:border-slate-700"';
$panelTable3CellAttributes = ' class="p-4 text-right"';
$panelTable3['columns'][] = ['html' => e($personalLabel('فاکتور', 'Invoice')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'th'];
$panelTable3CellAttributes = ' class="p-4 text-right"';
$panelTable3['columns'][] = ['html' => e($personalLabel('شماره قسط', 'Installment')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'th'];
$panelTable3CellAttributes = ' class="p-4 text-right"';
$panelTable3['columns'][] = ['html' => e($personalLabel('مبلغ', 'Amount')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'th'];
$panelTable3CellAttributes = ' class="p-4 text-right"';
$panelTable3['columns'][] = ['html' => e($personalLabel('وضعیت', 'Status')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'th'];
$panelTable3CellAttributes = ' class="p-4 text-right"';
$panelTable3['columns'][] = ['html' => e($personalLabel('سررسید', 'Due date')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'th'];
$panelTable3['bodyAttributes'] = '';
foreach ($personalInstallments as $installment):
$panelTable3Row = ['cells' => []];
$panelTable3Row['attributes'] = ' class="border-b last:border-0 dark:border-slate-700"';
$panelTable3CellAttributes = ' class="p-4"';
$panelTable3Row['cells'][] = ['html' => (int) $installment['invoice_id'], 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3CellAttributes = ' class="p-4"';
$panelTable3Row['cells'][] = ['html' => (int) ($installment['installment_number'] ?? 0), 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3CellAttributes = ' class="p-4"';
$panelTable3Row['cells'][] = ['html' => e(number_format((float) ($installment['amount'] ?? 0))), 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3CellAttributes = ' class="p-4"';
$panelTable3Row['cells'][] = ['html' => e((string) ($installment['status'] ?? '—')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3CellAttributes = ' class="p-4"';
$panelTable3Row['cells'][] = ['html' => e((string) ($installment['due_date'] ?? '—')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3['rows'][] = $panelTable3Row;
endforeach;
if (!$personalInstallments):
$panelTable3Row = ['cells' => []];
ob_start();
$panelTable3Row['attributes'] = ob_get_clean();
$panelTable3CellAttributes = ' colspan="5" class="p-6 text-center text-gray-500"';
$panelTable3Row['cells'][] = ['html' => e($personalLabel('قسطی ثبت نشده است.', 'No installments recorded.')), 'attributes' => $panelTable3CellAttributes, 'tag' => 'td'];
$panelTable3['rows'][] = $panelTable3Row;
endif;
component('panel-table-template', $panelTable3); unset($panelTable3); ?></div>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm dark:bg-slate-900"><?php $panelTable4 = ['columns' => [], 'rows' => []];
$panelTable4['attributes'] = ' class="w-full min-w-[650px] text-sm"';
$panelTable4['headAttributes'] = '';
$panelTable4['headerRowAttributes'] = ' class="border-b dark:border-slate-700"';
$panelTable4CellAttributes = ' class="p-4 text-right"';
$panelTable4['columns'][] = ['html' => e($personalLabel('فاکتور', 'Invoice')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'th'];
$panelTable4CellAttributes = ' class="p-4 text-right"';
$panelTable4['columns'][] = ['html' => e($personalLabel('مبلغ پرداخت', 'Payment amount')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'th'];
$panelTable4CellAttributes = ' class="p-4 text-right"';
$panelTable4['columns'][] = ['html' => e($personalLabel('روش', 'Method')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'th'];
$panelTable4CellAttributes = ' class="p-4 text-right"';
$panelTable4['columns'][] = ['html' => e($personalLabel('وضعیت', 'Status')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'th'];
$panelTable4CellAttributes = ' class="p-4 text-right"';
$panelTable4['columns'][] = ['html' => e($personalLabel('تاریخ پرداخت', 'Paid at')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'th'];
$panelTable4['bodyAttributes'] = '';
foreach ($personalPayments as $payment):
$panelTable4Row = ['cells' => []];
$panelTable4Row['attributes'] = ' class="border-b last:border-0 dark:border-slate-700"';
$panelTable4CellAttributes = ' class="p-4"';
$panelTable4Row['cells'][] = ['html' => (int) $payment['invoice_id'], 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4CellAttributes = ' class="p-4"';
$panelTable4Row['cells'][] = ['html' => e(number_format((float) ($payment['amount'] ?? 0))), 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4CellAttributes = ' class="p-4"';
$panelTable4Row['cells'][] = ['html' => e((string) ($payment['method'] ?? '—')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4CellAttributes = ' class="p-4"';
$panelTable4Row['cells'][] = ['html' => e((string) ($payment['status'] ?? '—')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4CellAttributes = ' class="p-4"';
$panelTable4Row['cells'][] = ['html' => e((string) ($payment['paid_at'] ?? '—')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4['rows'][] = $panelTable4Row;
endforeach;
if (!$personalPayments):
$panelTable4Row = ['cells' => []];
ob_start();
$panelTable4Row['attributes'] = ob_get_clean();
$panelTable4CellAttributes = ' colspan="5" class="p-6 text-center text-gray-500"';
$panelTable4Row['cells'][] = ['html' => e($personalLabel('پرداختی ثبت نشده است.', 'No payments recorded.')), 'attributes' => $panelTable4CellAttributes, 'tag' => 'td'];
$panelTable4['rows'][] = $panelTable4Row;
endif;
component('panel-table-template', $panelTable4); unset($panelTable4); ?></div>
</section>
