<?php

use Core\View\View;

$title ??= 'Sornaz';

?>



<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <?php include resource_path('views/layouts/partials/head.php'); ?>
</head>
<body class="dashboard-layout">
<div class="app">
    <aside class="app-sidebar">
        <?php View::component('layout.sidebar'); ?>
    </aside>
    <div class="app-wrapper">
        <header class="app-navbar">
            <?php View::component('layout.navbar'); ?>
        </header>
        <section class="app-breadcrumb">
            <?= $breadcrumb ?? '' ?>
        </section>
        <section class="app-toolbar">
            <?= $toolbar ?? '' ?>
        </section>
        <main class="app-content">
            <?= $content ?? '' ?>
        </main>
        <footer class="app-footer">
            <?php View::component('layout.footer'); ?>
        </footer>
    </div>
</div>
</body>
</html>
