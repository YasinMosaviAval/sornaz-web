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




