<div class="page-toolbar">
    <?php
        component(
            'ui.button',
            [
                'url'=>'/academy/create',
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
            <td><?= $academy['id'] ?></td>
            <td><?= e($academy['username']) ?></td>
            <td>
                <?php
                component(
                    'ui.badge',
                    [
                        'type'=>$academy['status']?'success':'danger',
                        'text'=>$academy['status']?'فعال':'غیرفعال'
                    ]
                );
                ?>
            </td>
            <td><?= e($academy['city'] ?? '-') ?></td>
            <td><?= e($academy['created_at']) ?></td>
            <td>
                <?php
                    component(
                        'ui.button',
                        [
                            'url'=>"/academy/{$academy['academy_id']}/edit",
                            'text'=>'ویرایش',
                            'type'=>'primary'
                        ]
                    );
                    component(
                        'ui.button',
                        [
                            'url'=>"/academy/{$academy['academy_id']}",
                            'text'=>'مشاهده',
                            'type'=>'secondary'
                        ]
                    );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>



<?php
component(
    'ui.pagination',
    [
        'pagination' => [
            'current_page' => $academies['page'],
            'last_page'    => $academies['last_page']
        ]
    ]
);
?>



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