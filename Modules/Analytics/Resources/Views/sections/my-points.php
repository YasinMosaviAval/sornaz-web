<?php
$pointLocale = locale() === 'en';
$pointQuery = db()->prepare('SELECT * FROM user_points WHERE user_id=? AND deleted_at IS NULL ORDER BY user_point_id DESC');
$pointQuery->execute([(int) auth()->id()]);
$personalPoints = $pointQuery->fetchAll(\PDO::FETCH_ASSOC);
$pointText = fn ($fa, $en) => $pointLocale ? $en : $fa;
$pointFields = ['type'=>['نوع','Type'], 'points'=>['امتیاز','Points'], 'action'=>['عملیات','Action'], 'rule_id'=>['قانون امتیاز','Point rule'], 'updated_at'=>['تاریخ ویرایش','Updated']];
$columns = [['value' => $pointText("ردیف", 'Row'), 'attributes' => 'class="py-5 px-5 text-start font-medium"']];
foreach ($pointFields as [$fa, $en]) $columns[] = ['value' => $pointText($fa, $en), 'attributes' => 'class="py-5 px-5 text-start font-medium"'];
$rows = [];
foreach ($personalPoints as $index => $point) {
    $cells = [['value' => (string) ($index + 1), 'attributes' => 'class="py-4 px-5"']];
    foreach ($pointFields as $field => $labels) {
        $value = (string) ($point[$field] ?? '—');
        if ($field === 'action') $value = \Core\database\ActivityText::point($point, locale());
        if ($field === 'type') $value = match ($value) {
            'general' => $pointText('عمومی', 'General'),
            'professional' => $pointText('حرفه‌ای', 'Professional'),
            'specialized' => $pointText('تخصصی', 'Specialized'),
            default => $value,
        };
        $cells[] = ['value' => $value, 'attributes' => 'class="py-4 px-5"'];
    }
    $rows[] = ['cells' => $cells];
}
foreach ($columns as $index => &$column) {
    $column['html'] = '<button type="button" data-panel-sort="' . $index . '" data-panel-sort-type="' . (in_array($index, [0,2,4], true) ? 'number' : 'text') . '" class="flex items-center gap-2">' . e($column['value']) . ' <span data-panel-sort-icon aria-hidden="true">&#8597;</span></button>';
    $column['attributes'] .= ' aria-sort="none"';
}
unset($column);
$pointFilterOptions = [];
foreach ([1,3] as $index) {
    $pointFilterOptions[$index] = array_values(array_unique(array_map(static fn ($row) => (string) $row['cells'][$index]['value'], $rows)));
}
?>
<div id="my-points" class="mt-8">
    <div class="mb-6 grid grid-cols-1 gap-4 rounded-3xl bg-white p-5 shadow-sm dark:bg-slate-900 md:grid-cols-2">
        <?php foreach ([3 => ['عملیات', 'Action'], 1 => ['نوع امتیاز', 'Point type']] as $index => [$fa, $en]): ?>
        <label class="block text-sm font-medium">
            <?= e($pointText($fa, $en)) ?>
            <select id="<?= $index === 3 ? 'myPointsActionFilter' : 'myPointsTypeFilter' ?>" data-panel-filter="myPointsTable" data-panel-filter-column="<?= $index ?>" class="mt-2 w-full rounded-2xl border border-gray-300 px-4 py-3 dark:bg-slate-800">
                <option value=""><?= e($pointText('همه', 'All')) ?></option>
                <?php foreach ($pointFilterOptions[$index] as $option): ?>
                <option value="<?= e($option) ?>"><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php endforeach; ?>
    </div>
    <?php component('panel-table-template', [
        'attributes' => 'class="w-full min-w-[800px]" id="myPointsTable" data-no-identity-column',
        'headAttributes' => 'class="bg-gray-50 border-b dark:bg-slate-800"',
        'bodyAttributes' => 'class="divide-y text-sm"',
        'columns' => $columns, 'rows' => $rows,
        'pagination' => ['perPage' => 10, 'emptyMessage' => $pointText('هنوز امتیازی ثبت نشده است.', 'No points recorded yet.')],
    ]); ?>
</div>
