<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=e($title)?></title>
    <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
    <?
    pushStyle('reset.css');
    pushStyle('variables.css');
    pushStyle('typography.css');
    pushStyle('layout.css');
    pushStyle('header.css');
    pushStyle('footer.css');
    pushStyle('hero.css');
    ?>
    <?=styles()?>
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
</head>
<body>
    <? component('header'); ?>
    <main class="site-content">
    <?=$slot?>
    </main>
    <? component('footer'); ?>
    <?
    pushScript('header.js');
    pushScript('home.js');
    ?>
    <div id="modalContainer"></div>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?: 1 ?>"></script>
    <?=scripts()?>
</body>
</html>
