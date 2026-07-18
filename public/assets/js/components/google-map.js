// document.addEventListener("DOMContentLoaded",function(){
//     const mapBox=document.getElementById("google-map");
//     if(!mapBox){
//         return;
//     }
//     let lat=parseFloat(mapBox.dataset.lat||35.6892);
//     let lng=parseFloat(mapBox.dataset.lng||51.3890);
//     const map=new google.maps.Map(mapBox,{
//         center:{lat:lat, lng:lng},
//         zoom:14,
//         mapTypeControl:false,
//         streetViewControl:false,
//         fullscreenControl:false
//     });
//     const marker=new google.maps.Marker({
//         position:{lat:lat, lng:lng},
//         draggable:true,
//         map:map
//     });
//     marker.addListener("dragend",function(e){
//         document.getElementById("latitude").value=e.latLng.lat();
//         document.getElementById("longitude").value=e.latLng.lng();
//     });
//     map.addListener("click",function(e){
//         marker.setPosition(e.latLng);
//         document.getElementById("latitude").value=e.latLng.lat();
//         document.getElementById("longitude").value=e.latLng.lng();
//     });
// });


// const input=document.getElementById("google-map-search");
// const search=new google.maps.places.SearchBox(input);
// map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
// search.addListener("places_changed",function(){
//     const places=search.getPlaces();
//     if(!places.length){
//         return;
//     }
//     const place=places[0];
//     if(!place.geometry){
//         return;
//     }
//     map.panTo(place.geometry.location);
//     map.setZoom(16);
//     marker.setPosition(place.geometry.location);
//     updateLocation(place.geometry.location);
// });


// function updateLocation(location){
//     document.getElementById("latitude").value=
//     location.lat();
//     document.getElementById("longitude").value=
//     location.lng();
//     reverseGeocode(location);
// }


// const geocoder=new google.maps.Geocoder();


// function reverseGeocode(location){
//     geocoder.geocode({
//         location:location
//     },function(results,status){
//         if(status!=="OK"){
//             return;
//         }
//         if(!results.length){
//             return;
//         }
//         const result=results[0];
//         document.getElementById("google-address").value=
//         result.formatted_address;
//         parseAddress(result.address_components);
//     });
// }


// function parseAddress(items){
//     let country="";
//     let province="";
//     let county="";
//     let postal="";
//     items.forEach(function(item){
//         if(item.types.includes("country")){
//             country=item.long_name;
//         }
//         if(item.types.includes("administrative_area_level_1")){
//             province=item.long_name;
//         }
//         if(item.types.includes("administrative_area_level_2")){
//             county=item.long_name;
//         }
//         if(item.types.includes("postal_code")){
//             postal=item.long_name;
//         }
//     });
//     document.getElementById("google-country").value=country;
//     document.getElementById("google-province").value=province;
//     document.getElementById("google-county").value=county;
//     document.getElementById("google-postal").value=postal;
// }


// document
// .getElementById("google-current-location")
// .addEventListener("click",function(){
//     navigator.geolocation.getCurrentPosition(function(pos){
//         const p={
//             lat:pos.coords.latitude,
//             lng:pos.coords.longitude
//         };
//         marker.setPosition(p);
//         map.panTo(p);
//         map.setZoom(17);
//         updateLocation({
//             lat:()=>p.lat,
//             lng:()=>p.lng
//         });
//     });
// });



let map;
let marker;
window.addEventListener("load",function(){
    if(typeof google==="undefined"){
        return;
    }
    const lat = parseFloat(document.getElementById("latitude").value || 35.6892);
    const lng = parseFloat(document.getElementById("longitude").value || 51.3890);
    map = new google.maps.Map(
        document.getElementById("google-map"),
        {
            zoom:13,
            center:{lat,lng}
        }
    );
    marker = new google.maps.Marker({
        position:{lat,lng},
        map,
        draggable:true
    });

    marker.addListener("dragend",function(e){
        document.getElementById("latitude").value = e.latLng.lat();
        document.getElementById("longitude").value = e.latLng.lng();
    });

    const input = document.getElementById("google-search");
    const search = new google.maps.places.SearchBox(input);
    search.addListener("places_changed",function(){
        const places = search.getPlaces();
        if(!places.length){
            return;
        }
        const place = places[0];

        fetch("/api/world/google-address",{
            method:"POST",
            headers:{
                "Content-Type":"application/json"
            },
            body:JSON.stringify(place)
        })
        .then(r=>r.json())
        .then(function(res){
            document.getElementById("country_id").value=res.country_id;
            document.getElementById("province_id").value=res.province_id;
            document.getElementById("county_id").value=res.county_id;
        })

        marker.setPosition(place.geometry.location);
        map.panTo(place.geometry.location);
        document.getElementById("latitude").value = place.geometry.location.lat();
        document.getElementById("longitude").value = place.geometry.location.lng();
    });
});




