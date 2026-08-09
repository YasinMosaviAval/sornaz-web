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
    <?=scripts()?>
</body>
</html>
