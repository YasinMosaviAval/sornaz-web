<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آموزشگاه موسیقی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <style>
        body { font-family: Vazirmatn, Tahoma, sans-serif; }
        .site-page { display: none; }
        .site-page.active { display: block; }
        .nav-link-site.active { color: #4f46e5; font-weight: 600; }
        .accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .accordion-body.open { max-height: 800px; }
        .accordion-icon { transition: transform 0.3s; }
        .accordion-icon.open { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <?// component('main-header'); ?>
    <main class="flex-1">
        <?=$slot?>
    </main>
    <?// component('main-footer'); ?>
    <?
        pushScript('home.js');
        pushScript('auth.js');

        pushScript('main.js');
    ?>
    <div id="modalContainer"></div>
    <?= scripts() ?>
</body>
</html>
