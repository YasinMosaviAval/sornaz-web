<?php

ob_start();

if(!empty($logo)){
?>

<img
src="/<?=e($logo['path'])?>"
style="
width:120px;
border-radius:12px;
margin-bottom:15px;
">

<?php
}

component('ui.file',[
'label'=>'لوگو',
'name'=>'logo'
]);

if(!empty($cover)){
?>

<img
src="/<?=e($cover['path'])?>"
style="
width:100%;
max-height:220px;
object-fit:cover;
border-radius:10px;
margin-bottom:20px;
">

<?php
}

component('ui.file',[
'label'=>'کاور',
'name'=>'cover'
]);

component('ui.file',[
'label'=>'گالری تصاویر',
'name'=>'gallery[]',
'multiple'=>true
]);




$form=ob_get_clean();

component(
'ui.card',
[
'title'=>'رسانه ها',
'slot'=>$form
]
);



ob_start();

component(
    'ui.video',
    [
        'label'=>'ویدئو معرفی',
        'name'=>'intro_video',
        'value'=>$academy['intro_video'] ?? ''
    ]
);

component(
    'ui.academy_videos',
    [
        'value'=>$academyVideos ?? []
    ]
);

$videos = ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'ویدیوهای آموزشگاه',
        'slot'=>$videos
    ]
);

