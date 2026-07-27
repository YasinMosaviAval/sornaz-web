<?php

ob_start();

component(
    'ui.input',
    [
        'label'=>'کشور',
        'name'=>'country_id',
        'value'=>$address['country_id'] ?? ''
    ]
);

component(
    'ui.select',
    [
        'label'=>'استان',
        'name'=>'province_id',
        'id'=>'province_id',
        'value'=>$address['province_id'] ?? null,
        'options'=>$provinces
    ]
);

component(
    'ui.select',
    [
        'label'=>'شهر',
        'name'=>'county_id',
        'id'=>'county_id',
        'value'=>$address['county_id'] ?? null,
        'options'=>$counties ?? []
    ]
);

component(
    'ui.input',
    [
        'label'=>'کدپستی',
        'name'=>'postal_code',
        'value'=>$address['postal_code'] ?? ''
    ]
);

component(
    'ui.textarea',
    [
        'label'=>'نشانی',
        'name'=>'address',
        'rows'=>4,
        'value'=>$address['address'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'Latitude',
        'name'=>'latitude',
        'value'=>$address['latitude'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'Longitude',
        'name'=>'longitude',
        'value'=>$address['longitude'] ?? ''
    ]
);


$form=ob_get_clean();

component(
'ui.card',
[
'title'=>'آدرس',
'slot'=>$form
]
);
