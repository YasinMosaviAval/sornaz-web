<?php

pushStyle('assets/css/components/google-map.css');
pushScript('assets/js/components/google-map.js');

$latitude  = $latitude ?? '';
$longitude = $longitude ?? '';

?>

<div class="google-map-component">

    <p class="offline-map-note">انتخاب موقعیت آفلاین است؛ عرض و طول جغرافیایی را وارد کنید.</p>
    <div id="google-map" class="offline-coordinate-picker" role="group" aria-label="انتخاب مختصات">
        <label>عرض جغرافیایی<input type="number" step="0.0000001" id="latitude-picker" value="<?= e($latitude ?: '35.6892') ?>"></label>
        <label>طول جغرافیایی<input type="number" step="0.0000001" id="longitude-picker" value="<?= e($longitude ?: '51.3890') ?>"></label>
        <button type="button" id="use-current-location">استفاده از موقعیت دستگاه</button>
        <span id="offline-map-status" aria-live="polite"></span>
    </div>

    <input
        type="hidden"
        id="latitude"
        name="latitude"
        value="<?=e($latitude)?>">

    <input
        type="hidden"
        id="longitude"
        name="longitude"
        value="<?=e($longitude)?>">

</div>
