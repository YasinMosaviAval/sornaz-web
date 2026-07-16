<?php
// dump($academy);
?>

<!-- <h1 class="page-title">ویرایش آموزشگاه</h1> -->

<div class="page-toolbar">
<?php
component(
    'ui.button',
    [
        'url'=>'/academy',
        'text'=>'بازگشت',
        'type'=>'secondary'
    ]
);
?>
</div>

<form method="post" action="/academy/<?= $academy['academy_id'] ?>" enctype="multipart/form-data">
<input type="hidden" name="_method" value="PUT">

<?php

/*
|--------------------------------------------------------------------------
| اطلاعات اصلی
|--------------------------------------------------------------------------
*/

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
        'value'=>$academy['status'] ?? '',
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
        'value'=>$academy['locale'] ?? ''
    ]
);

component(
    'ui.input',
    [
        'label'=>'Timezone',
        'name'=>'timezone',
        'value'=>$academy['timezone'] ?? ''
    ]
);

$generalForm = ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'اطلاعات اصلی',
        'slot'=>$generalForm
    ]
);

/*
|--------------------------------------------------------------------------
| آدرس
|--------------------------------------------------------------------------
*/


ob_start();


component('ui.input',[
    'label'=>'نام آموزشگاه (فارسی)',
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
    'label'=>'شعار (فارسی)',
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
    'label'=>'توضیح کوتاه (فارسی)',
    'name'=>'short_description_fa',
    'rows'=>3,
    'value'=>$academy['short_description_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'Short Description',
    'name'=>'short_description_en',
    'dir'=>'ltr',
    'rows'=>3,
    'value'=>$academy['short_description_en'] ?? ''
]);




component('ui.textarea',[
    'label'=>'معرفی کامل (فارسی)',
    'name'=>'description_fa',
    'rows'=>8,
    'value'=>$academy['description_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'Description',
    'name'=>'description_en',
    'dir'=>'ltr',
    'rows'=>8,
    'value'=>$academy['description_en'] ?? ''
]);




component(
    'ui.input',
    [
        'label'=>'کلمات کلیدی',
        'name'=>'keywords_fa',
        'value'=>$academy['keywords_fa'] ?? ''
    ]
);
component('ui.input',[
    'label'=>'Keywords',
    'name'=>'keywords_en',
    'dir'=>'ltr',
    'value'=>$academy['keywords_en'] ?? ''
]);



component(
    'ui.textarea',
    [
        'label'=>'توضیحات متا',
        'name'=>'meta_description_fa',
        'rows'=>3,
        'value'=>$academy['meta_description_fa'] ?? ''
    ]
);
component('ui.textarea',[
    'label'=>'Seo Description',
    'name'=>'meta_description_en',
    'rows'=>3,
    'dir'=>'ltr',
    'value'=>$academy['meta_description_en'] ?? ''
]);

component(
    'ui.file',
    [

        'label'=>'لوگو',

        'name'=>'logo'

    ]
);


$form=ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'اطلاعات آموزشگاه',
        'slot'=>$form
    ]
);



ob_start();

component('ui.file',[
    'label'=>'لوگو',
    'name'=>'logo'
]);

component('ui.file',[
    'label'=>'کاور',
    'name'=>'cover'
]);

component('ui.file',[
    'label'=>'گالری تصاویر',
    'name'=>'gallery[]',
    'multiple'=>true
]);

$mediaForm=ob_get_clean();

component('ui.card',[
    'title'=>'تصاویر آموزشگاه',
    'slot'=>$mediaForm
]);

?>



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

$addressForm = ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'آدرس آموزشگاه',
        'slot'=>$addressForm
    ]
);

/*
|--------------------------------------------------------------------------
| راه های ارتباطی
|--------------------------------------------------------------------------
*/

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

$contactForm = ob_get_clean();

component(
    'ui.card',
    [
        'title'=>'راه های ارتباطی',
        'slot'=>$contactForm
    ]
);

?>

<div style="margin-top:30px">

<?php

component(
    'ui.button',
    [
        'submit'=>true,
        'text'=>'ذخیره تغییرات',
        'type'=>'success'
    ]
);

?>

</div>

</form>

<script>
// document.addEventListener("DOMContentLoaded",function(){
//     const province=document.getElementById("province_id");
//     const county=document.getElementById("county_id");
//     if(!province || !county){
//         return;
//     }
//     province.addEventListener("change",function(){
//         county.innerHTML='<option>در حال دریافت...</option>';
//         fetch("/api/world/provinces/"+province.value+"/counties")
//             .then(r=>r.json())
//             .then(function(items){
//                 county.innerHTML='<option value="">انتخاب شهر</option>';
//                 Object.keys(items).forEach(function(id){
//                     county.innerHTML+=
//                         '<option value="'+id+'">'+items[id]+'</option>';
//                 });
//             });
//     });
// });
document.addEventListener("DOMContentLoaded", function () {
    const province = document.getElementById("province_id");
    const county = document.getElementById("county_id");
    if (!province || !county) {
        return;
    }
    province.addEventListener("change", function () {
        county.innerHTML = '<option value="">در حال دریافت...</option>';
        fetch("/api/world/provinces/" + province.value + "/counties")
        .then(r => r.json())
        .then(function (response) {
            county.innerHTML = '<option value="">انتخاب شهر</option>';
            const items = response.data;
            for (const id in items) {
                county.innerHTML += `<option value="${id}">${items[id]}</option>`;
            }
        });
    });
});
</script>