<h1 class="page-title">ایجاد آموزشگاه</h1>
<div class="page-toolbar">
    <?
    component(
        'ui.button',
        [
            'url'  => '/academy',
            'text' => 'بازگشت',
            'type' => 'secondary'
        ]
    );
    ?>
</div>

<form method="post" action="/academy">
    <?
    ob_start();
    ?>
    <?
    component(
        'ui.input',
        [
            'label' => 'نام کاربری',
            'name'  => 'username'
        ]
    );
    ?>
    <?
    component(
        'ui.input',
        [
            'label' => 'ایمیل',
            'name'  => 'email',
            'type'  => 'email'
        ]
    );
    ?>
    <?
    component(
        'ui.input',
        [
            'label' => 'موبایل',
            'name'  => 'phone'
        ]
    );
    ?>
    <?
    component(
        'ui.select',
        [
            'label' => 'وضعیت',
            'name'  => 'status',
            'value' => 'approved',
            'options' => [
                'approved' => 'فعال',
                'pending' => 'غیرفعال'
            ]
        ]
    );
    ?>
    <?
    component(
        'ui.input',
        [
            'label' => 'Locale',
            'name'  => 'locale',
            'value' => 'fa'
        ]
    );
    ?>
    <?
    component(
        'ui.input',
        [
            'label' => 'Timezone',
            'name'  => 'timezone',
            'value' => 'Asia/Tehran'
        ]
    );
    ?>
    <div style="margin-top:24px;">
        <?
        component(
            'ui.button',
            [
                'text' => 'ذخیره آموزشگاه',
                'type' => 'success',
                'submit' => true
            ]
        );
        ?>
    </div>
    <?
    $form = ob_get_clean();
    component(
        'ui.card',
        [
            'title' => 'اطلاعات آموزشگاه',
            'slot'  => $form
        ]
    );
    ?>
</form>
