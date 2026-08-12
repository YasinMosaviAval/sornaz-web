<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Sornaz' ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="<?= asset('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="/assets/theme/theme.css?v=<?= filemtime(base_path('assets/theme/theme.css')) ?: 1 ?>">
    <?php foreach(\Core\View\View::styles() as $style): ?>
    <link rel="stylesheet" href="<?= asset($style) ?>">
    <?php endforeach; ?>
</head>
<body>
    <div class="sn-dashboard">
        <aside class="sn-sidebar">
            <?php component('layout.sidebar'); ?>
        </aside>
        <div class="sn-main">
            <header class="sn-header">
                <?php component('inline-edit-switch'); ?>
                <?php component('layout.header',[
                    'title'=>$title,
                    'breadcrumb'=>$breadcrumb,
                    'toolbar'=>$toolbar
                ]); ?>
            </header>
            <main class="sn-content">
                <?= $content ?>
            </main>
        </div>
    </div>
    <?php foreach(\Core\View\View::scripts() as $script): ?>
        <script src="<?= asset($script) ?>"></script>
    <?php endforeach; ?>
    <div id="modalContainer"></div>
    <script>window.adminCsrfToken=<?= json_encode(csrf_token()) ?>;</script>
    <script src="/assets/theme/dialog.js?v=<?= filemtime(base_path('assets/theme/dialog.js')) ?: 1 ?>"></script>
    <script src="/assets/Analytics/js/admin-inline-editor.js?v=<?= filemtime(base_path('assets/Analytics/js/admin-inline-editor.js')) ?: 1 ?>"></script>
</body>
</html>
