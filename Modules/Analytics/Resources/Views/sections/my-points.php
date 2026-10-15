<?php
$pointLocale = locale() === 'en';
$pointQuery = db()->prepare('SELECT * FROM user_points WHERE user_id=? AND deleted_at IS NULL ORDER BY user_point_id DESC');
$pointQuery->execute([(int) auth()->id()]);
$personalPoints = $pointQuery->fetchAll(\PDO::FETCH_ASSOC);
$pointText = fn ($fa, $en) => $pointLocale ? $en : $fa;
$pointFields = ['user_point_id'=>['شناسه','ID'], 'type'=>['نوع','Type'], 'points'=>['امتیاز','Points'], 'action'=>['عملیات','Action'], 'reference_type'=>['نوع مرجع','Reference type'], 'reference_id'=>['شناسه مرجع','Reference ID'], 'created_at'=>['تاریخ ثبت','Created'], 'approved_at'=>['تأیید','Approval'], 'rule_id'=>['قانون امتیاز','Point rule'], 'award_key'=>['کلید ثبت','Award key'], 'metadata'=>['جزئیات','Details'], 'created_by'=>['ثبت‌کننده','Created by'], 'updated_at'=>['تاریخ ویرایش','Updated'], 'updated_by'=>['ویرایش‌کننده','Updated by'], 'approved_by'=>['تأییدکننده','Approved by']];
foreach (array_keys($personalPoints[0] ?? []) as $field) {
    if (!isset($pointFields[$field]) && !in_array($field, ['user_id','deleted_at','deleted_by'], true)) $pointFields[$field] = [$field,$field];
}
?>
<div id="my-points" class="mt-8">
    <div class="overflow-x-auto rounded-3xl bg-white p-4 shadow dark:bg-slate-900">
        <?php $panelTable0 = ['columns' => [], 'rows' => []];
$panelTable0['attributes'] = ' class="w-full min-w-[800px] text-sm"';
$panelTable0['headAttributes'] = ' class="border-b bg-gray-50 dark:bg-slate-800"';
$panelTable0['headerRowAttributes'] = '';
foreach ($pointFields as [$fa, $en]):
$panelTable0CellAttributes = ' class="p-4 text-start"';
$panelTable0['columns'][] = ['html' => e($pointText($fa, $en)), 'attributes' => $panelTable0CellAttributes, 'tag' => 'th'];
endforeach;
$panelTable0['bodyAttributes'] = '';
foreach ($personalPoints as $point):
$panelTable0Row = ['cells' => []];
$panelTable0Row['attributes'] = ' class="border-b dark:border-slate-700"';
foreach ($pointFields as $field => $labels):
$panelTable0CellAttributes = ' class="p-4"';
$panelTable0Row['cells'][] = ['html' => e((string) ($field === 'approved_at' ? ($point[$field] ?: $pointText('در انتظار تأیید', 'Pending approval')) : ($point[$field] ?? '—'))), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
endforeach;
$panelTable0['rows'][] = $panelTable0Row;
endforeach;
if (!$personalPoints):
$panelTable0Row = ['cells' => []];
ob_start();
$panelTable0Row['attributes'] = ob_get_clean();
ob_start(); ?> colspan="<?= count($pointFields) ?>" class="p-8 text-center text-gray-500"<?php $panelTable0CellAttributes = ob_get_clean();
$panelTable0Row['cells'][] = ['html' => e($pointText('هنوز امتیازی ثبت نشده است.', 'No points recorded yet.')), 'attributes' => $panelTable0CellAttributes, 'tag' => 'td'];
$panelTable0['rows'][] = $panelTable0Row;
endif;
component('panel-table-template', $panelTable0); unset($panelTable0); ?>
    </div>
</div>
