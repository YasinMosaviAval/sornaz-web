<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آموزشگاه موسیقی</title>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; }
        .site-page { display: none; }
        .site-page.active { display: block; }
        .nav-link-site.active { color: #4f46e5; font-weight: 600; }
        .accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .accordion-body.open { max-height: 800px; }
        .accordion-icon { transition: transform 0.3s; }
        .accordion-icon.open { transform: rotate(180deg); }
        .auth-toast-success { background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0 !important; }
        .auth-toast-error { background:#fef2f2 !important; color:#991b1b !important; border:1px solid #fecaca !important; }
        .swal2-popup { font-family:Tahoma,Arial,sans-serif; direction:rtl; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <?php $authSuccess = session()->getFlash('auth_success'); ?>
    <div id="authFlashMessage" class="hidden" data-success="<?= e($authSuccess ?? '') ?>" data-error=""></div>
    <? component('main-header'); ?>
    <main class="flex-1">
        <?=$slot?>
    </main>
    <? component('main-footer'); ?>
    <?
        pushScript('home.js');
        pushScript('site-pages.js');
        pushScript('main.js');
    ?>
    <div id="modalContainer"></div>
    <?= scripts() ?>
    <script src="/assets/System/js/auth.js?v=<?= filemtime(base_path('assets/System/js/auth.js')) ?: 1 ?>"></script>
</body>
</html>
