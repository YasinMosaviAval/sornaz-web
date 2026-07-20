<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=e($title)?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet">
    <?php
    pushStyle('Modules/Home/assets/css/reset.css');
    pushStyle('Modules/Home/assets/css/variables.css');
    pushStyle('Modules/Home/assets/css/typography.css');
    pushStyle('Modules/Home/assets/css/layout.css');
    pushStyle('Modules/Home/assets/css/header.css');
    pushStyle('Modules/Home/assets/css/footer.css');
    pushStyle('Modules/Home/assets/css/hero.css');
    ?>
    <?=styles()?>
</head>
<body>
    <?php include __DIR__.'/../partials/header.php';?>
    <main>
    <?=$slot?>
    </main>
    <?php include __DIR__.'/../partials/footer.php';?>
    <?php
    pushScript('Modules/Home/assets/js/header.js');
    pushScript('Modules/Home/assets/js/home.js');
    ?>
    <?=scripts()?>
</body>
</html>