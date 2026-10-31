<!doctype html>
<html>
<head>
    <title><?= e(($title ?? '') ?: trans('public.site_name', 'سُرناز')) ?></title>
</head>
<body>
    <header>
        <?php if(auth()->check()): ?>
            Logged as:
            <?= auth()->user()['email'] ?>
            |
            <a href="/logout">Logout</a>
        <?php else: ?>
            <a href="/login">Login</a>
        <?php endif; ?>
    </header>
    <hr>
    <?= user()['email'] ?>
    <hr>
    <?= user_id() ?>
    <hr>
    <?= $content ?>
</body>
</html>
