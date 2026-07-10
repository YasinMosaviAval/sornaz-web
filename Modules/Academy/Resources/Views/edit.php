<?php
// dump($academy);
?>

<h1 class="page-title">
    ویرایش آموزشگاه
</h1>

<div class="page-toolbar">
    <?php
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

<form method="post" action="/academy/<?= $academy['academy_id'] ?>">
    <input type="hidden" name="_method" value="PUT">

    <?php
    ob_start();

    component(
        'ui.input',
        [
            'label' => 'نام کاربری',
            'name'  => 'username',
            'value' => $academy['username'] ?? ''
        ]
    );

    component(
        'ui.input',
        [
            'label' => 'ایمیل',
            'name'  => 'email',
            'type'  => 'email',
            'value' => $academy['email'] ?? ''
        ]
    );

    component(
        'ui.input',
        [
            'label' => 'موبایل',
            'name'  => 'phone',
            'value' => $academy['phone'] ?? ''
        ]
    );

    component(
        'ui.select',
        [
            'label'   => 'وضعیت',
            'name'    => 'status',
            'value'   => $academy['status'] ?? '',
            'options' => [
                'approved' => 'فعال',
                'pending'  => 'غیرفعال'
            ]
        ]
    );

    component(
        'ui.input',
        [
            'label' => 'Locale',
            'name'  => 'locale',
            'value' => $academy['locale'] ?? ''
        ]
    );

    component(
        'ui.input',
        [
            'label' => 'Timezone',
            'name'  => 'timezone',
            'value' => $academy['timezone'] ?? ''
        ]
    );
    ?>

    <div style="margin-top:24px">

        <?php
        component(
            'ui.button',
            [
                'submit' => true,
                'text'   => 'ذخیره تغییرات',
                'type'   => 'success'
            ]
        );
        ?>

    </div>

    <?php

    $form = ob_get_clean();

    component(
        'ui.card',
        [
            'title' => 'ویرایش آموزشگاه',
            'slot'  => $form
        ]
    );

    ?>

</form>