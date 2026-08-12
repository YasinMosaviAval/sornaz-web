<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آموزشگاه موسیقی</title>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
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
    <? component('main-header'); ?>
    <main class="flex-1">
        <?=$slot?>
    </main>
    <? component('main-footer'); ?>
    <?
        pushScript('home.js');
        pushScript('auth.js');

        pushScript('Page::site-pages.js');
        pushScript('Page::main.js');
    ?>
    <div id="modalContainer"></div>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?: 1 ?>"></script>
    <?= scripts() ?>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
</body>
</html>
