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

<?php

ob_start();

component(
    'ui.input',
    [
        'label' => 'کشور',
        'name'  => 'country_id',
        'value' => $address['country_id'] ?? ''
    ]
);

component(
    'ui.select',
    [
        'label'   => 'استان',
        'name'    => 'province_id',
        'id'      => 'province_id',
        'value'   => $address['province_id'] ?? null,
        'options' => $provinces,
    ]
);

component(
    'ui.select',
    [
        'label'   => 'شهر',
        'name'    => 'county_id',
        'id'      => 'county_id',
        'value'   => $address['county_id'] ?? null,
        'options' => $counties ?? [],
    ]
);

component(
    'ui.input',
    [
        'label' => 'کد پستی',
        'name'  => 'postal_code',
        'value' => $address['postal_code'] ?? ''
    ]
);

component(
    'ui.textarea',
    [
        'label' => 'آدرس',
        'name'  => 'address',
        'rows'  => 4,
        'value' => $address['address'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label' => 'Latitude',
        'name'  => 'latitude',
        'value' => $address['latitude'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label' => 'Longitude',
        'name'  => 'longitude',
        'value' => $address['longitude'] ?? ''
    ]
);

$addressForm = ob_get_clean();

component(
    'ui.card',
    [
        'title' => 'آدرس آموزشگاه',
        'slot'  => $addressForm
    ]
);
?>


<?php

ob_start();

component(
    'ui.input',
    [
        'label'=>'تلفن ثابت',
        'name'=>'telephone',
        'value'=>$contact['telephone'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'واتساپ',
        'name'=>'whatsapp',
        'value'=>$contact['whatsapp'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'تلگرام',
        'name'=>'telegram',
        'value'=>$contact['telegram'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'اینستاگرام',
        'name'=>'instagram',
        'value'=>$contact['instagram'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'وب سایت',
        'name'=>'website',
        'value'=>$contact['website'] ?? ''
    ]
);

$contactForm = ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'راه های ارتباطی',
        'slot'=>$contactForm
    ]
);

?>



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