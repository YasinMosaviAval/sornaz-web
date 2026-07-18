<?php

ob_start();

component(
    'ui.document_upload',
    [
        'name'=>'documents',
        'multiple'=>true,
        'value'=>$documents ?? []
    ]
);

$content=ob_get_clean();

component('ui.card',[
    'title'=>'اسناد آموزشگاه',
    'slot'=>$content
]);