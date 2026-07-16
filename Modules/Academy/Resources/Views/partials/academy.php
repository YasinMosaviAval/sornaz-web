<?php

ob_start();

component('ui.input',[
'label'=>'نام آموزشگاه',
'name'=>'name_fa',
'value'=>$academy['name_fa'] ?? ''
]);

component('ui.input',[
'label'=>'Academy Name',
'name'=>'name_en',
'dir'=>'ltr',
'value'=>$academy['name_en'] ?? ''
]);

component('ui.textarea',[
'label'=>'شعار',
'name'=>'slogan_fa',
'value'=>$academy['slogan_fa'] ?? ''
]);

component('ui.textarea',[
'label'=>'Slogan',
'name'=>'slogan_en',
'dir'=>'ltr',
'value'=>$academy['slogan_en'] ?? ''
]);

component('ui.textarea',[
'label'=>'توضیح کوتاه',
'name'=>'short_description_fa',
'rows'=>3,
'value'=>$academy['short_description_fa'] ?? ''
]);

component('ui.textarea',[
'label'=>'Short Description',
'name'=>'short_description_en',
'rows'=>3,
'dir'=>'ltr',
'value'=>$academy['short_description_en'] ?? ''
]);

component('ui.textarea',[
'label'=>'معرفی کامل',
'name'=>'description_fa',
'rows'=>8,
'value'=>$academy['description_fa'] ?? ''
]);

component('ui.textarea',[
'label'=>'Description',
'name'=>'description_en',
'rows'=>8,
'dir'=>'ltr',
'value'=>$academy['description_en'] ?? ''
]);

component('ui.input',[
'label'=>'کلمات کلیدی',
'name'=>'keywords_fa',
'value'=>$academy['keywords_fa'] ?? ''
]);

component('ui.input',[
'label'=>'Keywords',
'name'=>'keywords_en',
'dir'=>'ltr',
'value'=>$academy['keywords_en'] ?? ''
]);

component('ui.textarea',[
'label'=>'Meta Description',
'name'=>'meta_description_fa',
'rows'=>3,
'value'=>$academy['meta_description_fa'] ?? ''
]);

component('ui.textarea',[
'label'=>'SEO Description',
'name'=>'meta_description_en',
'rows'=>3,
'dir'=>'ltr',
'value'=>$academy['meta_description_en'] ?? ''
]);





component('ui.textarea',[
    'label'=>'قوانین',
    'name'=>'rules_fa',
    'rows'=>5,
    'value'=>$academy['rules_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'Rules',
    'name'=>'rules_en',
    'dir'=>'ltr',
    'rows'=>5,
    'value'=>$academy['rules_en'] ?? ''
]);

component('ui.textarea',[
    'label'=>'شرایط ثبت نام',
    'name'=>'registration_fa',
    'rows'=>5,
    'value'=>$academy['registration_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'Registration',
    'name'=>'registration_en',
    'dir'=>'ltr',
    'rows'=>5,
    'value'=>$academy['registration_en'] ?? ''
]);

component('ui.input',[
    'label'=>'Meta Title',
    'name'=>'meta_title_fa',
    'value'=>$academy['meta_title_fa'] ?? ''
]);

component('ui.input',[
    'label'=>'Meta Title EN',
    'name'=>'meta_title_en',
    'dir'=>'ltr',
    'value'=>$academy['meta_title_en'] ?? ''
]);




$form=ob_get_clean();

component(
'ui.card',
[
'title'=>'اطلاعات آموزشگاه',
'slot'=>$form
]
);