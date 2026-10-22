<!doctype html>
<html lang="<?= e(locale() === 'en' ? 'en' : 'fa') ?>" dir="<?= locale() === 'en' ? 'ltr' : 'rtl' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="origin">
    <meta name="robots" content="noindex,nofollow">
    <title><?= locale() === 'en' ? 'Continue payment' : 'ادامه پرداخت' ?></title>
    <script src="/assets/Analytics/js/payment-continue.js" defer></script>
</head>
<body>
<main>
    <h1><?= locale() === 'en' ? 'Connecting to Zarinpal' : 'در حال انتقال به زرین‌پال' ?></h1>
    <p><?= locale() === 'en' ? 'If you are not redirected automatically, continue below.' : 'اگر انتقال خودکار انجام نشد، دکمه زیر را بزنید.' ?></p>
    <a id="merchantPaymentContinue" href="<?= e($gatewayUrl) ?>" referrerpolicy="origin"><?= locale() === 'en' ? 'Continue payment' : 'ادامه پرداخت' ?></a>
</main>
</body>
</html>
