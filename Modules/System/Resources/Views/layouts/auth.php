<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(trans('auth.site_name', 'آموزشگاه موسیقی')) ?></title>
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
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
        .language-toggle input:checked + span { background:#4f46e5; }
        .language-toggle input:checked + span::after { transform:translateX(1.25rem); }
        .auth-language-widget { position:fixed; top:1rem; right:1.25rem; z-index:50; direction:ltr; }
        main { animation:auth-page-in .14s ease-out; transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main { opacity:0; transform:translateY(4px); }
        @keyframes auth-page-in { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }
        @media (prefers-reduced-motion:reduce) { main { animation:none; transition:none; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <header class="h-16" aria-label="<?= e(trans('auth.language_switch_aria', 'تغییر زبان')) ?>">
        <label class="auth-language-widget language-toggle inline-flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium shadow-sm cursor-pointer hover:border-indigo-300 transition">
            <i class="fas fa-globe"></i>
            <span><?= e(locale() === 'fa' ? trans('auth.language_persian', 'فارسی') : trans('auth.language_english', 'English')) ?></span>
            <input type="checkbox" class="sr-only" <?= locale() === 'en' ? 'checked' : '' ?>
                   aria-label="<?= e(trans('auth.language_switch_aria', 'تغییر زبان')) ?>"
                   onchange="changeAuthLanguage(this.checked ? 'en' : 'fa')">
            <span class="relative block h-6 w-11 rounded-full bg-gray-300 transition-colors after:absolute after:top-1 after:left-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform"></span>
        </label>
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
</body>
</html>
