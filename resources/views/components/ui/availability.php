<?php

pushStyle('assets/css/components/availability.css');

pushScript('assets/js/components/availability.js');

$days=[
    'saturday'=>'شنبه',
    'sunday'=>'یکشنبه',
    'monday'=>'دوشنبه',
    'tuesday'=>'سه شنبه',
    'wednesday'=>'چهارشنبه',
    'thursday'=>'پنجشنبه',
    'friday'=>'جمعه'
];

$value=$value ?? [];

$group=[];

foreach($value as $item){

    $group[$item['day_of_week']][]=$item;

}

?>

<div class="availability">

<?php foreach($days as $key=>$title): ?>

<div class="availability-day">

<h4><?=e($title)?></h4>

<div
class="availability-items"
data-day="<?=e($key)?>">

<?php

$rows=$group[$key] ?? [[]];

foreach($rows as $index => $row):

?>

<div class="availability-row">

<input
type="time"
name="availability[<?=e($key)?>][<?= $index ?>][start_time]"
value="<?=e($row['start_time'] ?? '')?>">

<input
type="time"
name="availability[<?=e($key)?>][<?= $index ?>][end_time]"
value="<?=e($row['end_time'] ?? '')?>">

<label>

<input
type="checkbox"
name="availability[<?=e($key)?>][<?= $index ?>][is_closed]"
value="1"
<?=!empty($row['is_closed'])?'checked':''?>>

تعطیل

</label>

<button
type="button"
class="availability-remove">

−

</button>

</div>

<?php endforeach; ?>

</div>

<button
type="button"
class="availability-add"
data-day="<?=e($key)?>">

+ افزودن بازه

</button>

</div>

<?php endforeach; ?>

</div>