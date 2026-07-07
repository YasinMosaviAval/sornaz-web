<h1 class="page-title">
    مدیریت آموزشگاه‌ها
</h1>

<div class="page-toolbar">
    <?php
    component(
        'ui.button',
        [
            'url'=>'/dashboard/academies/create',
            'text'=>'ایجاد آموزشگاه',
            'type'=>'success'
        ]
    );
    ?>
</div>

<?php
ob_start();
?>

<table class="sn-table">
    <thead>
        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>وضعیت</th>
            <th>شهر</th>
            <th>تاریخ ثبت</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($academies['data'] as $academy): ?>
        <tr>
            <td><?= $academy->user_id ?></td>
            <td><?= e($academy->name) ?></td>
            <td>
                <?php
                component(
                    'ui.badge',
                    [
                        'type'=>$academy->status?'success':'danger',
                        'text'=>$academy->status?'فعال':'غیرفعال'
                    ]
                );
                ?>
            </td>
            <td><?= e($academy->city ?? '-') ?></td>
            <td><?= e($academy->created_at) ?></td>
            <td>
                <?php
                component(
                    'ui.button',
                    [
                        'url'=>"/dashboard/academies/{$academy->user_id}/edit",
                        'text'=>'ویرایش',
                        'type'=>'primary'
                    ]
                );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$table=ob_get_clean();
component(
    'ui.card',
    [
        'title'=>'لیست آموزشگاه‌ها',
        'slot'=>$table
    ]
);
?>