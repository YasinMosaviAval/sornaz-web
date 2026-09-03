<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آموزشگاه موسیقی</title>
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <script>tailwind.config={darkMode:['class','[data-mode="dark"]']};</script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
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
    <div id="siteScrollProgress" class="pointer-events-none fixed inset-x-0 top-0 z-[100] hidden h-[5px] bg-transparent" aria-hidden="true"><div id="siteScrollProgressBar" class="h-full w-0 bg-indigo-600"></div></div>
    <header class="min-h-16 px-4 py-3 flex items-center justify-end border-b bg-white"><? component('inline-edit-switch'); ?></header>
    <?// component('main-header'); ?>
    <main class="flex-1">
        <?=$slot?>
    </main>
    <footer class="bg-gray-900 text-gray-300 px-4 py-6">
        <? component('Page::sections.trust-badges'); ?>
    </footer>
    <?
        pushScript('home.js');
        pushScript('auth.js');

        pushScript('main.js');
    ?>
    <div id="modalContainer"></div>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script>(function(){const bar=document.getElementById('siteScrollProgressBar');if(!bar)return;const update=function(){const max=document.documentElement.scrollHeight-window.innerHeight;bar.parentElement.style.display=max>0?'block':'none';bar.style.width=(max>0?Math.min(100,Math.max(0,(window.scrollY/max)*100)):0)+'%';};window.addEventListener('scroll',update,{passive:true});window.addEventListener('resize',update,{passive:true});update();})();</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?: 1 ?>"></script>
    <?= scripts() ?>
    <script src="/assets/theme/help-center.js?v=<?= filemtime(base_path('assets/theme/help-center.js')) ?: 1 ?>"></script>
</body>
</html>
