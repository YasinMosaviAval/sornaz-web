<?
/**
 * theme/default.php
 *
 * متغیرهای مورد نیاز در $config:
 *   app.lang, app.base
 *   page.current_title, page.description, page.og_image
 *   page.noindex, page.nofollow
 */
global $config;

$lang    = $config['app']['lang'] ?? 'fa';
$dir     = $lang === 'fa' ? 'rtl' : 'ltr';
$title   = getPageTitle();
$desc    = $config['page']['description'] ?? '';
$robots  = getRobotState();
$base    = baseUrl();
$url     = getFullUrl();
$ogImage = $config['page']['og_image']
           ?? $base . '/assets/og/default.jpg';

$uri     = $_SERVER['REQUEST_URI'] ?? '';
$isAdmin = str_contains($uri, '/admin/') || str_starts_with($uri, '/admin');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>

  <!-- ─── ۱. Charset & Viewport ──────────────────── -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ─── ۲. Title ───────────────────────────────── -->
  <title><?= htmlspecialchars($title) ?></title>

  <!-- ─── ۳. SEO ─────────────────────────────────── -->
  <meta name="description" content="<?= htmlspecialchars($desc) ?>">
  <meta name="robots"      content="<?= $robots ?>">
  <meta name="author"      content="Sornaz Academy">
  <link rel="canonical"    href="<?= htmlspecialchars($url) ?>">

  <!-- ─── ۴. Open Graph ──────────────────────────── -->
  <meta property="og:title"        content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description"  content="<?= htmlspecialchars($desc) ?>">
  <meta property="og:image"        content="<?= htmlspecialchars($ogImage) ?>">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt"    content="<?= htmlspecialchars($title) ?>">
  <meta property="og:url"          content="<?= htmlspecialchars($url) ?>">
  <meta property="og:type"         content="website">
  <meta property="og:site_name"    content="Sornaz Academy">
  <meta property="og:locale"       content="<?= $lang === 'fa' ? 'fa_IR' : 'en_US' ?>">

  <!-- ─── ۵. Twitter Card ────────────────────────── -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($desc) ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($ogImage) ?>">
  <meta name="twitter:image:alt"   content="<?= htmlspecialchars($title) ?>">

  <!-- ─── ۶. Favicon ─────────────────────────────── -->
  <link rel="icon"             href="<?= $base ?>/assets/images/logo/favicon.svg" type="image/svg+xml">
  <link rel="icon"             href="<?= $base ?>/assets/images/logo/favicon.ico" sizes="any">
  <link rel="apple-touch-icon" href="<?= $base ?>/assets/images/logo/apple-touch-icon.png" sizes="180x180">
  <link rel="manifest"         href="<?= $base ?>/manifest.json">
  <meta name="theme-color"     content="#1a1a2e">

  <!-- ─── ۷. Preconnect ──────────────────────────── -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- ─── ۸. Fonts ───────────────────────────────── -->
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  
  <!-- ─── ۹. Styles Colors ──────────────────────────────── -->
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/variables.css">
  <!-- ─── ۹. Styles ──────────────────────────────── -->
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/style.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/font-awesome.min.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/print.css" media="print">
  
  
  <!-- ─── ۹. Data Table ──────────────────────────────── -->
  <link rel="stylesheet" href="<?= $base ?>/styles/library/dataTables.min.css" />
  <script src="<?= $base ?>/scripts/library/dataTables.min.js"></script>


  <!-- ─── ۱۰. Schema.org ─────────────────────────── -->
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Sornaz Academy",
      "url": "<?= $base ?>",
      "logo": "<?= $base ?>/assets/images/logo/logo.png",
      "sameAs": []
    }
  </script>

</head>
<body class="lang-<?= $lang ?><?= $isAdmin ? ' layout-admin' : ' layout-default' ?>">

  <? if (!$isAdmin): ?>
    <? require __DIR__ . '/header.php'; ?>
  <? endif; ?>


  <main id="main-content" role="main">
    <?= $content ?>
  </main>

  <? if (!$isAdmin): ?>
    <? require __DIR__ . '/footer.php'; ?>
  <? endif; ?>

  <? echo 'SESSION'; dump($_SESSION); ?>
  <?// echo 'SERVER'; dump($_SERVER); ?>
  <?// echo 'POST'; dump($_POST); ?>
  <?// echo 'GET'; dump($_GET); ?>
  <?// echo 'FILES'; dump($_FILES); ?>
  <?// echo 'COOKIE'; dump($_COOKIE); ?>
  <?// echo 'ENV'; dump($_ENV); ?>
  <?// echo 'REQUEST'; dump($_REQUEST); ?>
  <?// echo 'GLOBALS'; dump($_GLOBALS); ?>

  <!-- Scripts — آخر body -->
  <script src="<?= $base ?>/assets/scripts/jquery-1.11.3.min.js"></script>
  <script src="<?= $base ?>/assets/scripts/chart.js"></script>
  <script src="<?= $base ?>/assets/scripts/jspdf.umd.min.js"></script>
  <script src="<?= $base ?>/assets/scripts/main.js"></script>
  <? if ($isAdmin): ?>
  <script src="<?= $base ?>/assets/scripts/admin.js"></script>
  <? endif; ?>

</body>
</html>

<script>
  // Initialise DataTable:
  let table = new DataTable('#myTable');
  // This file is required by the index.html file and will
// be executed in the renderer process for that window.
// All of the Node.js APIs are available in this process.
window.$ = window.jquery = require('./node_modules/jquery');
window.dt = require('./node_modules/datatables.net')();
window.$('#table_id').DataTable();
</script>