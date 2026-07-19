<?php

ob_start();

component('ui.input',[
    'label'=>'نام شعبه (فارسی)',
    'name'=>'name_fa',
    'value'=>$branch['name_fa'] ?? ''
]);

component('ui.input',[
    'label'=>'نام شعبه (English)',
    'name'=>'name_en',
    'value'=>$branch['name_en'] ?? ''
]);

component('ui.input',[
    'label'=>'شعار (فارسی)',
    'name'=>'slogan_fa',
    'value'=>$branch['slogan_fa'] ?? ''
]);

component('ui.input',[
    'label'=>'شعار (English)',
    'name'=>'slogan_en',
    'value'=>$branch['slogan_en'] ?? ''
]);

component('ui.textarea',[
    'label'=>'توضیح کوتاه (فارسی)',
    'name'=>'short_description_fa',
    'value'=>$branch['short_description_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'توضیح کوتاه (English)',
    'name'=>'short_description_en',
    'value'=>$branch['short_description_en'] ?? ''
]);

component('ui.textarea',[
    'label'=>'توضیحات کامل (فارسی)',
    'name'=>'description_fa',
    'value'=>$branch['description_fa'] ?? ''
]);

component('ui.textarea',[
    'label'=>'توضیحات کامل (English)',
    'name'=>'description_en',
    'value'=>$branch['description_en'] ?? ''
]);

component('ui.select',[
    'label'=>'نوع شعبه',
    'name'=>'academy_branch_type_id',
    'options'=>$branchTypes ?? [],
    'value'=>$branch['academy_branch_type_id'] ?? ''
]);

component('ui.select',[
    'label'=>'حالت فعالیت',
    'name'=>'mode',
    'options'=>[
        'physical'=>'حضوری',
        'online'=>'آنلاین',
        'hybrid'=>'ترکیبی'
    ],
    'value'=>$branch['mode'] ?? 'physical'
]);

?>

<div class="sn-form-group">

    <label>

        <input
            type="checkbox"
            name="is_main"
            value="1"
            <?=!empty($branch['is_main']) ? 'checked' : ''?>>

        شعبه اصلی

    </label>

</div>

<?php

$form = ob_get_clean();

component('ui.card',[
    'title'=>'اطلاعات شعبه',
    'slot'=>$form
]);