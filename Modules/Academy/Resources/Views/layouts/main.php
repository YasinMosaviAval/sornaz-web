<!DOCTYPE html>
<html lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?: trans('public.site_name', 'برنامه موسیقی سُرناز')) ?></title>
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <style>
        body { font-family:Vazirmatn,Tahoma,Arial,sans-serif; }
        .nav-link-site.active { color:#4f46e5;font-weight:600; }
        .language-toggle input:checked + span { background:#4f46e5; }
        .language-toggle input:checked + span::after { transform:translateX(1.25rem); }
        .academy-language-widget { position:fixed;top:5rem;right:1.25rem;z-index:45;direction:ltr; }
        main { transition:opacity .09s ease,transform .09s ease; }
        body.language-changing main { opacity:0;transform:translateY(4px); }
        [dir="ltr"] .relative > button.absolute.left-4 { left:auto;right:1rem; }
        .swal2-popup { font-family:Vazirmatn,Tahoma,Arial,sans-serif;direction:<?= e(direction()) ?>; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <? component('Page::sections.main-header'); ?>
    <label class="academy-language-widget language-toggle inline-flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium shadow-md cursor-pointer hover:border-indigo-300 transition">
        <i class="fas fa-globe"></i>
        <span><?= e(locale() === 'fa' ? trans('auth.language_persian', 'فارسی') : trans('auth.language_english', 'English')) ?></span>
        <input type="checkbox" class="sr-only" <?= locale() === 'en' ? 'checked' : '' ?> aria-label="<?= e(trans('auth.language_switch_aria', 'تغییر زبان')) ?>" onchange="changeAcademyLanguage(this.checked ? 'en' : 'fa')">
        <span class="relative block h-6 w-11 rounded-full bg-gray-300 transition-colors after:absolute after:top-1 after:left-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform"></span>
    </label>
    <main class="flex-1"><?= $slot ?></main>
    <? component('Page::sections.main-footer'); ?>
    <div id="modalContainer"></div>
    <script>
        window.academyTranslations = <?= json_encode(translations('academy.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        window.changeAcademyLanguage = locale => { document.body.classList.add('language-changing'); setTimeout(() => location.href='/language/'+locale, 90); };
    </script>
    <?= scripts() ?>
</body>
</html>
