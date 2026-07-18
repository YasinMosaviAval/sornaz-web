<?php

// pushStyle('assets/css/components/google-map.css');
// pushScript('https://maps.googleapis.com/maps/api/js?key='. config('services.google.maps.key'). '&libraries=places');
// pushScript('assets/js/components/google-map.js');


// $latitude=$latitude ?? '';
// $longitude=$longitude ?? '';

?>

<!-- <input type="hidden" id="google-country" name="country_id">
<input type="hidden" id="google-province" name="province_id">
<input type="hidden" id="google-county" name="county_id">
<input type="hidden" id="google-postal" name="postal_code">

<div class="google-map-component">
    <div
        id="google-map"
        class="google-map"
        data-lat="<?//=e($latitude)?>"
        data-lng="<?//=e($longitude)?>"
    ></div>
    <div class="google-map-fields"> -->
        <?php
        /*component('ui.input',[
            'label'=>'Latitude',
            'name'=>'latitude',
            'id'=>'latitude',
            'value'=>$latitude
        ]);
        component('ui.input',[
            'label'=>'Longitude',
            'name'=>'longitude',
            'id'=>'longitude',
            'value'=>$longitude
        ]);
        component('ui.textarea',[
            'label'=>'آدرس انتخاب شده',
            'name'=>'address',
            'id'=>'google-address',
            'rows'=>3,
            'value'=>$address ?? ''
        ]);*/
        ?>
    <!-- </div>
    <button
        type="button"
        id="google-current-location"
        class="btn btn-secondary">
        موقعیت فعلی من
    </button>
    
</div> -->

<?php

pushStyle('assets/css/components/google-map.css');
pushScript('assets/js/components/google-map.js');

$latitude  = $latitude ?? '';
$longitude = $longitude ?? '';

?>

<div class="google-map-component">

    <input
        id="google-search"
        class="form-control"
        placeholder="جستجوی آدرس">

    <div id="google-map"></div>

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

<script src="https://maps.googleapis.com/maps/api/js?key=<?=config('google.api_key')?>&libraries=places"></script>
