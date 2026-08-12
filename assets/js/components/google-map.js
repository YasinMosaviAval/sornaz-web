window.addEventListener('load',function(){
    const latitude=document.getElementById('latitude'),longitude=document.getElementById('longitude');
    const latitudePicker=document.getElementById('latitude-picker'),longitudePicker=document.getElementById('longitude-picker');
    const status=document.getElementById('offline-map-status');
    if(!latitude||!longitude||!latitudePicker||!longitudePicker)return;
    const sync=()=>{latitude.value=latitudePicker.value;longitude.value=longitudePicker.value;};
    latitudePicker.addEventListener('input',sync);longitudePicker.addEventListener('input',sync);sync();
    document.getElementById('use-current-location')?.addEventListener('click',function(){
        if(!navigator.geolocation){status.textContent='موقعیت‌یابی در این مرورگر پشتیبانی نمی‌شود.';return;}
        status.textContent='در حال دریافت موقعیت...';
        navigator.geolocation.getCurrentPosition(function(position){latitudePicker.value=position.coords.latitude.toFixed(7);longitudePicker.value=position.coords.longitude.toFixed(7);sync();status.textContent='موقعیت دستگاه ثبت شد.';},function(){status.textContent='دسترسی به موقعیت دستگاه ممکن نشد؛ مختصات را دستی وارد کنید.';});
    });
});




