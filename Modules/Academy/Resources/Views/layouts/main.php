<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <title><?= e($title ?: trans('public.site_name', 'برنامه موسیقی سُرناز')) ?></title>
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <style>
        body { font-family:Vazirmatn,Tahoma,Arial,sans-serif; }
        .nav-link-site.active { color:#4f46e5;font-weight:600; }
        main { transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main { opacity:0;transform:translateY(4px); }
        [dir="ltr"] .relative > button.absolute.left-4 { left:auto;right:1rem; }
        .swal2-popup { font-family:Vazirmatn,Tahoma,Arial,sans-serif;direction:<?= e(direction()) ?>; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <? component('Page::sections.main-header'); ?>
    <main class="flex-1"><?= $slot ?></main>
    <? component('Page::sections.main-footer'); ?>
    <div id="modalContainer"></div>
    <script>
        window.academyTranslations = <?= json_encode(translations('academy.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <?= scripts() ?>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
</body>
</html>
