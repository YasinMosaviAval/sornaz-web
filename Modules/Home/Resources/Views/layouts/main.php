<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=e($title)?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <?
    pushStyle('Modules/Home/assets/css/reset.css');
    pushStyle('Modules/Home/assets/css/variables.css');
    pushStyle('Modules/Home/assets/css/typography.css');
    pushStyle('Modules/Home/assets/css/layout.css');
    pushStyle('Modules/Home/assets/css/header.css');
    pushStyle('Modules/Home/assets/css/footer.css');
    pushStyle('Modules/Home/assets/css/hero.css');
    ?>
    <?=styles()?>
    <!-- <link rel="stylesheet" href="/assets/css/layout/header.css"> -->
</head>
<body>
    <? include __DIR__.'/../partials/header.php';?>
    <?// component('website.header'); ?>
    <main class="site-content">
    <?=$slot?>
    </main>
    <? include __DIR__.'/../partials/footer.php';?>
    <?
    pushScript('Modules/Home/assets/js/header.js');
    pushScript('Modules/Home/assets/js/home.js');
    ?>
    <?=scripts()?>
    <!-- <script src="/assets/js/layout/header.js"></script> -->
</body>
</html>