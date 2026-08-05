<?php

ob_start();

component(
    'ui.input',
    [
        'label'=>'نام کاربری',
        'name'=>'username',
        'value'=>$academy['username'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'ایمیل',
        'name'=>'email',
        'type'=>'email',
        'value'=>$academy['email'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'موبایل',
        'name'=>'phone',
        'value'=>$academy['phone'] ?? ''
    ]
);

component(
    'ui.select',
    [
        'label'=>'وضعیت',
        'name'=>'status',
        'value'=>$academy['status'],
        'options'=>[
            'approved'=>'فعال',
            'pending'=>'غیرفعال'
        ]
    ]
);

component(
    'ui.input',
    [
        'label'=>'Locale',
        'name'=>'locale',
        'value'=>$academy['locale']
    ]
);

component(
    'ui.input',
    [
        'label'=>'Timezone',
        'name'=>'timezone',
        'value'=>$academy['timezone']
    ]
);

$form=ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'اطلاعات حساب',
        'slot'=>$form
    ]
);
