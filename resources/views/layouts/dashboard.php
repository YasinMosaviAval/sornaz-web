<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Sornaz' ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
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
</body>
</html>
