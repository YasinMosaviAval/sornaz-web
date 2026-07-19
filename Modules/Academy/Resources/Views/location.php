<!-- <h2>انتخاب موقعیت آموزشگاه</h2>

<form method="post">

<input
type="hidden"
id="latitude"
name="latitude"
value="<?=e($address['latitude']??'35.6892')?>">

<input
type="hidden"
id="longitude"
name="longitude"
value="<?=e($address['longitude']??'51.3890')?>">

<div
id="map"
style="
height:500px;
border-radius:15px;
">
</div>

<br>

<?php

component(
'ui.button',
[
'submit'=>true,
'text'=>'ذخیره موقعیت'
]
);

?>

</form>

<script
src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY">
</script>

<script>

const latInput=document.getElementById('latitude');
const lngInput=document.getElementById('longitude');

const center={
lat:parseFloat(latInput.value),
lng:parseFloat(lngInput.value)
};

const map=new google.maps.Map(
document.getElementById('map'),
{
zoom:15,
center:center
}
);

const marker=new google.maps.Marker({
position:center,
map:map,
draggable:true
});

marker.addListener(
'dragend',
function(e){

latInput.value=e.latLng.lat();

lngInput.value=e.latLng.lng();

}
);

</script> -->