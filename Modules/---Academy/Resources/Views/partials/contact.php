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
        'label'=>'موبایل دوم',
        'name'=>'mobile',
        'value'=>$contact['mobile'] ?? ''
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


$form=ob_get_clean();

component(
'ui.card',
[
'title'=>'راه های ارتباطی',
'slot'=>$form
]
);