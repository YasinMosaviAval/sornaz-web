<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Sornaz' ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
<div class="sn-dashboard">
    <aside class="sn-sidebar">
        <?php component('layout.sidebar'); ?>
    </aside>
    <div class="sn-main">
        <header class="sn-header">
            <?php component('layout.header',[
                'title'=>$title,
                'breadcrumb'=>$breadcrumb,
                'toolbar'=>$toolbar
            ]); ?>
        </header>
        <main class="sn-content">
            <?= $content ?>
        </main>
    </div>
</div>
<!-- <script>
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

</script> -->
</body>
</html>
