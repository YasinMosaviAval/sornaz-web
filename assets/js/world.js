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
