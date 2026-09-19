<?php
$lang = $boot['locale'];
$config = json_encode($boot, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?>
<!doctype html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'fa' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <title><?= $lang === 'fa' ? 'جامعه سُرناز' : 'Sornaz community' ?></title>
    <link rel="stylesheet" href="/assets/social/community.css">
</head>
<body>
    <div id="community-root"></div>
    <div id="community-toast" role="status" aria-live="polite"></div>
    <script>window.SOCIAL_BOOT = <?= $config ?>;</script>
    <script src="/assets/social/community.js" defer></script>
</body>
</html>
