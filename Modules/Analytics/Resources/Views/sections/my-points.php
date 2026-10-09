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
        <table class="w-full min-w-[800px] text-sm">
            <thead class="border-b bg-gray-50 dark:bg-slate-800"><tr>
                <?php foreach ($pointFields as [$fa, $en]): ?>
                    <th class="p-4 text-start"><?= e($pointText($fa, $en)) ?></th>
                <?php endforeach; ?>
            </tr></thead>
            <tbody>
                <?php foreach ($personalPoints as $point): ?>
                    <tr class="border-b dark:border-slate-700">
                        <?php foreach ($pointFields as $field => $labels): ?>
                            <td class="p-4"><?= e((string) ($field === 'approved_at' ? ($point[$field] ?: $pointText('در انتظار تأیید', 'Pending approval')) : ($point[$field] ?? '—'))) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$personalPoints): ?><tr><td colspan="<?= count($pointFields) ?>" class="p-8 text-center text-gray-500"><?= e($pointText('هنوز امتیازی ثبت نشده است.', 'No points recorded yet.')) ?></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
