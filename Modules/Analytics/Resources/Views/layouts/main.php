<!DOCTYPE html>
<html lang="<?= htmlspecialchars(locale()) ?>" dir="<?= locale() === 'en' ? 'ltr' : 'rtl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <title><?= e($title ?: trans('public.site_name', 'برنامه موسیقی سُرناز')) ?></title>
    <link rel="icon" type="image/jpeg" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/vendor/jalalidatepicker/jalalidatepicker.min.css">
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
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
        .auth-toast-success { background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0 !important; }
        .auth-toast-error { background:#fef2f2 !important; color:#991b1b !important; border:1px solid #fecaca !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
    <?php $authSuccess=session()->getFlash('auth_success'); ?>
    <div id="authFlashMessage" class="hidden" data-success="<?= e($authSuccess??'') ?>" data-error=""></div>
    <? component('Page::sections.main-header'); ?>
    <main class="flex-1">
        <?=$slot?>
    </main>
    <? component('Page::sections.main-footer'); ?>
    <script>window.siteUserAuthenticated=<?= auth()->check()?'true':'false' ?>;</script>
    <?
        pushScript('home.js');
        pushScript('Page::site-pages.js');
        pushScript('Page::main.js');
    ?>
    <div id="modalContainer"></div>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script>window.authTranslations=<?= json_encode(translations('auth.js.'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
    <script src="/assets/System/js/auth.js?v=<?= filemtime(base_path('assets/System/js/auth.js')) ?: 1 ?>"></script>
    <script>
        (function(){
            const bar=document.getElementById('siteScrollProgressBar');
            if(!bar)return;
            const update=function(){const max=document.documentElement.scrollHeight-window.innerHeight;bar.parentElement.style.display=max>0?'block':'none';bar.style.width=(max>0?Math.min(100,Math.max(0,(window.scrollY/max)*100)):0)+'%';};
            window.addEventListener('scroll',update,{passive:true});
            window.addEventListener('resize',update,{passive:true});
            update();
        })();
    </script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?: 1 ?>"></script>
    <?= scripts() ?>
    <script src="/assets/vendor/jalalidatepicker/jalalidatepicker.min.js?v=<?= filemtime(base_path('assets/vendor/jalalidatepicker/jalalidatepicker.min.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/localized-date.js?v=<?= filemtime(base_path('assets/theme/localized-date.js')) ?: 1 ?>"></script>
    <script>window.initLocalizedDateInputs?.(document);</script>
    <script src="/assets/Page/js/page-content-editor.js?v=<?= filemtime(base_path('assets/Page/js/page-content-editor.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?: 1 ?>"></script>
    <script src="/assets/theme/help-center.js?v=<?= filemtime(base_path('assets/theme/help-center.js')) ?: 1 ?>"></script>
</body>
</html>
