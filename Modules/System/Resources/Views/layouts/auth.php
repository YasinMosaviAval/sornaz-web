<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <title><?= e(trans('auth.site_name', 'آموزشگاه موسیقی')) ?></title>
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <style>
        body { font-family: Vazirmatn, Tahoma, Arial, sans-serif; }
        .site-page { display: none; }
        .site-page.active { display: block; }
        .nav-link-site.active { color: #4f46e5; font-weight: 600; }
        .accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .accordion-body.open { max-height: 800px; }
        .accordion-icon { transition: transform 0.3s; }
        .accordion-icon.open { transform: rotate(180deg); }
        .auth-toast-success { background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0 !important; }
        .auth-toast-error { background:#fef2f2 !important; color:#991b1b !important; border:1px solid #fecaca !important; }
        .swal2-popup { font-family:Vazirmatn,Tahoma,Arial,sans-serif; direction:<?= e(direction()) ?>; }
        [dir="ltr"] .auth-directional-left { left:auto; right:1rem; }
        [dir="ltr"] .relative > button.absolute.left-4 { left:auto; right:1rem; }
        main { animation:auth-page-in .14s ease-out; transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main { opacity:0; transform:translateY(4px); }
        @keyframes auth-page-in { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
        @media (prefers-reduced-motion:reduce) { main { animation:none; transition:none; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <header class="auth-header-switchers min-h-16 px-4 py-3 flex items-center justify-between gap-2 border-b border-gray-100 bg-white" dir="ltr">
        <? component('theme-switcher'); ?>
        <? component('language-switcher'); ?>
    </header>
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
    <script>window.authTranslations = <?= json_encode(translations('auth.js.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <?= scripts() ?>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
</body>
</html>
