<?php
// use Core\View\View;
// $title ??= 'Sornaz';
?>



<!-- <!doctype html>
<html lang="fa" dir="rtl">
<head>
    <?// include resource_path('views/layouts/partials/head.php'); ?>
</head>
<body class="dashboard-layout">
<div class="app">
    <aside class="app-sidebar">
        <?// View::component('layout.sidebar'); ?>
    </aside>
    <div class="app-wrapper">
        <header class="app-navbar">
            <?// View::component('layout.navbar'); ?>
        </header>
        <section class="app-breadcrumb">
            <?//= $breadcrumb ?? '' ?>
        </section>
        <section class="app-toolbar">
            <?//= $toolbar ?? '' ?>
        </section>
        <main class="app-content">
            <?//= $content ?? '' ?>
        </main>
        <footer class="app-footer">
            <?// View::component('layout.footer'); ?>
        </footer>
    </div>
</div>
</body>
</html> -->



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
