<style>

    .academy-card{
        display:flex;
        align-items:center;
        gap:24px;
    }

    .academy-avatar{
        width:80px;
        height:80px;
        border-radius:50%;
        background:#f3f4f6;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:34px;
    }

    .academy-content{
        flex:1;
    }

    .academy-meta{
        display:flex;
        gap:20px;
        margin-top:12px;
        flex-wrap:wrap;
        color:#666;
        font-size:14px;
    }

    .academy-status{
        display:flex;
        align-items:center;
    }


    .sn-stat-card{
    background:#fff;
    border-radius:12px;
    padding:20px;
    text-align:center;
    box-shadow:0 8px 24px rgba(0,0,0,.05);
    }

    .sn-stat-value{
    font-size:28px;
    font-weight:700;
    margin-bottom:8px;
    }

    .sn-stat-title{
    font-size:13px;
    color:#777;
    }

    .sn-info-item{
    display:flex;
    padding:14px 0;
    border-bottom:1px solid #eee;
    }

    .sn-info-label{
    width:180px;
    font-weight:600;
    color:#666;
    }

    .sn-info-value{
    flex:1;
    }

    .sn-avatar{
    width:96px;
    height:96px;
    border-radius:50%;
    background:#0ea5e9;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:42px;
    font-weight:bold;
    }

    .sn-badge{
    padding:6px 12px;
    border-radius:50px;
    font-size:12px;
    }

    .sn-badge-success{
    background:#dcfce7;
    color:#166534;
    }

    .sn-badge-secondary{
    background:#eee;
    }



</style>

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

<div class="grid-4">
    <?php
component('ui.stat-card',[
'title'=>'دانشجو',
'value'=>0
]);
component('ui.stat-card',[
    'title'=>'مدرس',
'value'=>0
]);
component('ui.stat-card',[
'title'=>'شعبه',
'value'=>0
]);
component('ui.stat-card',[
    'title'=>'دوره',
    'value'=>0
    ]);
    ?>
</div>
<div class="grid-4">
    <?
    component('ui.card',[
    'title'=>'اطلاعات تماس',
    'slot'=>'
    '.component('ui.info-item',[
    'label'=>'ایمیل',
    'value'=>$academy["email"]
    ],true).'
    '.component('ui.info-item',[
    'label'=>'موبایل',
    'value'=>$academy["phone"]
    ],true).'
    '
    ]);
    component('ui.badge',[

    'text'=>'فعال',

    'type'=>'success'

    ]);
    component('ui.button',[

    'url'=>"/academy/{$academy['academy_id']}/edit",

    'text'=>'ویرایش'

    ]);

    component('ui.button',[

    'url'=>"/academy",

    'text'=>'بازگشت',

    'type'=>'secondary'

    ]);
    ?>
</div>

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