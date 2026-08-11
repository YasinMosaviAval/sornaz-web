<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <title><?= e(trans('public.site_name', 'برنامه موسیقی سُرناز')) ?></title>
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
        .accordion-body {
            max-height:0;
            overflow:hidden;
            opacity:0;
            transform:translateY(-6px);
            transition:max-height .29s cubic-bezier(.4,0,.2,1),opacity .15s ease,transform .225s cubic-bezier(.4,0,.2,1);
            will-change:max-height,opacity,transform;
        }
        .accordion-body.open { max-height:var(--accordion-height, 1000px); opacity:1; transform:translateY(0); }
        .accordion-icon { transition:transform .21s cubic-bezier(.4,0,.2,1); }
        .accordion-icon.open { transform: rotate(180deg); }
        .auth-toast-success { background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0 !important; }
        .auth-toast-error { background:#fef2f2 !important; color:#991b1b !important; border:1px solid #fecaca !important; }
        .swal2-popup { font-family:Vazirmatn,Tahoma,Arial,sans-serif; direction:<?= e(direction()) ?>; }
        .home-hero-slide { opacity:0; visibility:hidden; transform:scale(1.035); transition:opacity .7s ease,visibility .7s ease,transform 5.5s ease; }
        .home-hero-slide.is-active { opacity:1; visibility:visible; transform:scale(1); }
        .hero-slider-dot { width:.65rem; height:.65rem; border-radius:9999px; background:rgba(255,255,255,.55); transition:width .3s,background .3s; }
        .hero-slider-dot.is-active { width:2rem; background:#fff; }
        @media (prefers-reduced-motion:reduce) { .home-hero-slide { transition:none; } }
        .language-toggle input:checked + span { background:#4f46e5; }
        .language-toggle input:checked + span::after { transform:translateX(1.25rem); }
        .public-language-widget { position:fixed; top:5rem; right:1.25rem; z-index:45; direction:ltr; }
        main { animation:public-page-in .14s ease-out; transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main { opacity:0; transform:translateY(4px); }
        @keyframes public-page-in { from { opacity:0;transform:translateY(4px); } to { opacity:1;transform:none; } }
        @media (prefers-reduced-motion:reduce) { main,.accordion-body,.accordion-icon { animation:none;transition:none; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <?php $authSuccess = session()->getFlash('auth_success'); ?>
    <div id="authFlashMessage" class="hidden" data-success="<?= e($authSuccess ?? '') ?>" data-error=""></div>
    <? component('main-header'); ?>
    <label class="public-language-widget language-toggle inline-flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium shadow-md cursor-pointer hover:border-indigo-300 transition">
        <i class="fas fa-globe"></i>
        <span><?= e(locale() === 'fa' ? trans('auth.language_persian', 'فارسی') : trans('auth.language_english', 'English')) ?></span>
        <input type="checkbox" class="sr-only" <?= locale() === 'en' ? 'checked' : '' ?> aria-label="<?= e(trans('auth.language_switch_aria', 'تغییر زبان')) ?>" onchange="changeAuthLanguage(this.checked ? 'en' : 'fa')">
        <span class="relative block h-6 w-11 rounded-full bg-gray-300 transition-colors after:absolute after:top-1 after:left-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform"></span>
    </label>
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
    <script>
        window.authTranslations = <?= json_encode(translations('auth.js.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        window.publicTranslations = <?= json_encode(translations('public.js.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <?= scripts() ?>
    <script src="/assets/System/js/auth.js?v=<?= filemtime(base_path('assets/System/js/auth.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
</body>
</html>
