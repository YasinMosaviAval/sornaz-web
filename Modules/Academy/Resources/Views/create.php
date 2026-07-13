<!-- <h1 class="page-title">ایجاد آموزشگاه</h1> -->

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

<?php
ob_start();
?>

<?php
component(
    'ui.form',
    [
        'action' => '/academy',
        'method' => 'POST',
        'slot' => (function () {

            ob_start();
?>

<div class="sn-grid sn-grid-2">

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'username',
                'text' => 'نام کاربری',
                'required' => true
            ]
        );

        component(
            'ui.input',
            [
                'name' => 'username',
                'id' => 'username'
            ]
        );

        component(
            'ui.error',
            [
                'field' => 'username'
            ]
        );
        ?>

    </div>

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'email',
                'text' => 'ایمیل'
            ]
        );

        component(
            'ui.input',
            [
                'type' => 'email',
                'name' => 'email',
                'id' => 'email'
            ]
        );

        component(
            'ui.error',
            [
                'field' => 'email'
            ]
        );
        ?>

    </div>

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'phone',
                'text' => 'موبایل'
            ]
        );

        component(
            'ui.input',
            [
                'name' => 'phone',
                'id' => 'phone'
            ]
        );

        ?>

    </div>

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'status',
                'text' => 'وضعیت'
            ]
        );

        component(
            'ui.select',
            [
                'name' => 'status',
                'id' => 'status',
                'value' => 'approved',
                'options' => [
                    'approved' => 'فعال',
                    'pending'  => 'غیرفعال'
                ]
            ]
        );
        ?>

    </div>

</div>

<hr class="sn-divider">

<div class="sn-grid sn-grid-2">

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'locale',
                'text' => 'Locale'
            ]
        );

        component(
            'ui.input',
            [
                'name' => 'locale',
                'id' => 'locale',
                'value' => 'fa'
            ]
        );
        ?>

    </div>

    <div class="sn-form-group">

        <?php
        component(
            'ui.label',
            [
                'for' => 'timezone',
                'text' => 'Timezone'
            ]
        );

        component(
            'ui.input',
            [
                'name' => 'timezone',
                'id' => 'timezone',
                'value' => 'Asia/Tehran'
            ]
        );
        ?>

    </div>

</div>

<div class="sn-form-actions">

<?php

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

<?php

            return ob_get_clean();

        })()
    ]
);
?>

<?php

$content = ob_get_clean();

component(
    'ui.card',
    [
        'title' => 'اطلاعات آموزشگاه',
        'slot' => $content
    ]
);

?>