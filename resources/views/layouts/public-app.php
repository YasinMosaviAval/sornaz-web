<!DOCTYPE html>
<html data-help-enabled="<?= (int)auth()->id() === 1 ? '1' : '0' ?>" data-inline-can-edit="<?= \Modules\System\Services\SiteAdminAccess::allows(auth()->user()) ? '1' : '0' ?>" lang="<?= e(locale()) ?>" dir="<?= e(direction()) ?>">
<head>
    <script>window.siteCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script src="/assets/theme/csrf.js?v=1"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle ?? $title ?? trans('public.site_name', 'سُرناز')) ?></title>
    <script>(function(){const r=document.documentElement;r.dataset.theme=localStorage.getItem('sornaz.theme')||'indigo';r.dataset.mode=localStorage.getItem('sornaz.mode')||'light';})();</script>
    <link rel="icon" href="/assets/images/logo/cropped-favicon_512x512.jpg">
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <script src="/assets/vendor/tailwind/tailwindcss.js"></script>
    <script>tailwind.config={darkMode:['class','[data-mode="dark"]']};</script>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?>">
    <?php foreach ($pageStyles ?? [] as $asset): ?>
    <link rel="stylesheet" href="/<?= e($asset) ?>?v=<?= filemtime(base_path($asset)) ?>">
    <?php endforeach; ?>
</head>
<body class="bg-gray-50 text-gray-800">
    <?php component('Page::sections.main-header'); ?>
    <?= $slot ?>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;window.siteUserAuthenticated=<?= auth()->check()?'true':'false' ?>;</script>
    <script src="/assets/Page/js/main.js?v=<?= filemtime(base_path('assets/Page/js/main.js')) ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?>"></script>
    <script src="/assets/theme/theme.js?v=<?= filemtime(base_path('assets/theme/theme.js')) ?>"></script>
    <script src="/assets/theme/public-app.js?v=<?= filemtime(base_path('assets/theme/public-app.js')) ?>"></script>
    <?php foreach ($pageScripts ?? [] as $asset): ?>
    <script src="/<?= e($asset) ?>?v=<?= filemtime(base_path($asset)) ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>
