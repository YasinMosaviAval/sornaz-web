<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title ?? 'وبلاگ') ?></title>
    <?= styles() ?>
</head>
<body>
<header class="blog-header">
    <div class="container">
        <a href="/">
            سُرنـاز
        </a>
        <nav>
            <a href="/">خانه</a>
            <a href="/blog">مقالات</a>
        </nav>
    </div>
</header>
<main>
    <?= $slot ?>
</main>
<footer>
    © <?= date('Y') ?>
</footer>
<?= scripts() ?>
</body>
</html>