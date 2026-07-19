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
