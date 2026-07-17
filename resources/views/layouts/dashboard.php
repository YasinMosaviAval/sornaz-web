<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title><?= $title ?? 'Sornaz' ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="<?= asset('assets/css/dashboard.css') ?>">
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
    </body>
</html>
