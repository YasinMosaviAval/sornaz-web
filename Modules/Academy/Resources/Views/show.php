<h1 class="page-title">
    <?= e($academy['username']) ?>
</h1>

<div class="page-toolbar">
    <?php
        component(
            'ui.button',
            [
                'url'=>'/academy',
                'text'=>'بازگشت',
                'type'=>'secondary'
            ]
        );
        component(
            'ui.button',
            [
                'url'=>"/academy/{$academy['academy_id']}/edit",
                'text'=>'ویرایش',
                'type'=>'primary'
            ]
        );
    ?>
</div>

<?php
    component(
        'ui.academy-info-card',
        [
            'academy'=>$academy
        ]
    );
?>

<?php
    ob_start();
?>

<table class="sn-table">
    <tr>
        <th>شناسه آموزشگاه</th>
        <td><?= e($academy['academy_id']) ?></td>
    </tr>
    <tr>
        <th>شناسه کاربر</th>
        <td><?= e($academy['user_id']) ?></td>
    </tr>
    <tr>
        <th>نام کاربری</th>
        <td><?= e($academy['username']) ?></td>
    </tr>
    <tr>
        <th>ایمیل</th>
        <td><?= e($academy['email']) ?></td>
    </tr>
    <tr>
        <th>موبایل</th>
        <td><?= e($academy['phone']) ?></td>
    </tr>
    <tr>
        <th>وضعیت</th>
        <td><?= e($academy['status']) ?></td>
    </tr>
    <tr>
        <th>Locale</th>
        <td><?= e($academy['locale']) ?></td>
    </tr>
    <tr>
        <th>Timezone</th>
        <td><?= e($academy['timezone']) ?></td>
    </tr>
    <tr>
        <th>تاریخ ایجاد</th>
        <td><?= e($academy['created_at']) ?></td>
    </tr>
</table>

<?php
    $content = ob_get_clean();
    component(
        'ui.card',
        [
            'title'=>'اطلاعات آموزشگاه',
            'slot'=>$content
        ]
    );
?>