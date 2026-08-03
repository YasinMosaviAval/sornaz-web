<?php

ob_start();

component('ui.input',[
    'label'=>'جستجوی آدرس',
    'id'=>'google-map-search',
    'name'=>'google_map_search'
]);


component(
    'ui.google_map',
    [
        'latitude'=>$address['latitude'] ?? '',
        'longitude'=>$address['longitude'] ?? '',
    ]
);


$map=ob_get_clean();

component('ui.card',[
    'title'=>'موقعیت آموزشگاه',
    'slot'=>$map
]);