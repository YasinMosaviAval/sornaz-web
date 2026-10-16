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
        if ($field === 'action') $value = \Core\database\ActivityText::action($value, locale());
        if ($field === 'type') $value = $pointText(
            in_array($value, ['professional','specialized'], true) ? 'حرفه‌ای' : 'عمومی',
            in_array($value, ['professional','specialized'], true) ? 'Professional' : 'General');
        $cells[] = ['value' => $value, 'attributes' => 'class="py-4 px-5"'];
    }
    $rows[] = ['cells' => $cells];
}
?>
<div id="my-points" class="mt-8">
    <?php component('panel-table-template', [
        'attributes' => 'class="w-full min-w-[800px]" id="myPointsTable" data-no-identity-column',
        'headAttributes' => 'class="bg-gray-50 border-b dark:bg-slate-800"',
        'bodyAttributes' => 'class="divide-y text-sm"',
        'columns' => $columns, 'rows' => $rows,
        'pagination' => ['perPage' => 10, 'emptyMessage' => $pointText('هنوز امتیازی ثبت نشده است.', 'No points recorded yet.')],
    ]); ?>
</div>
