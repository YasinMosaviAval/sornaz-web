# راهنمای کامل تگ‌های `<head>`

---

## ۱. Charset و Viewport — اجباری، اول از همه

```html
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

**چرا اول باشن؟** مرورگر باید encoding رو قبل از parse هر چیزی بدونه.
`initial-scale=1.0` از zoom پیش‌فرض موبایل جلوگیری می‌کنه.

---

## ۲. Title — اجباری

```html
<title>عنوان صفحه | نام سایت</title>
```

- بین **۵۰ تا ۶۰ کاراکتر** — بیشتر در Google کوتاه میشه
- فرمت: `عنوان صفحه | نام برند` یا برعکس
- هر صفحه title **یکتا** داشته باشه

---

## ۳. Meta اصلی SEO

```html
<meta name="description" content="توضیح صفحه — بین ۱۵۰ تا ۱۶۰ کاراکتر، جذاب و خلاصه">
<meta name="keywords"    content="موسیقی, آموزش گیتار, پیانو">
<meta name="author"      content="Sornaz Academy">
<meta name="robots"      content="index, follow">
```

**نکته:** `keywords` توسط Google نادیده گرفته میشه ولی برای Bing و موتورهای دیگه هنوز ارزش داره.

مقادیر `robots`:
| مقدار | معنی |
|---|---|
| `index, follow` | صفحه و لینک‌هاش ایندکس بشن (پیش‌فرض) |
| `noindex, follow` | صفحه ایندکس نشه ولی لینک‌ها دنبال بشن |
| `noindex, nofollow` | هیچ‌کدام — برای صفحات admin/login |
| `noarchive` | نسخه cache ذخیره نشه |

---

## ۴. Canonical — جلوگیری از محتوای تکراری

```html
<link rel="canonical" href="https://sornaz.com/courses/guitar">
```

وقتی یه صفحه با URL‌های مختلف قابل دسترسه (با/بدون www، با query string)، canonical به Google میگه کدوم نسخه اصلیه.

---

## ۵. Open Graph — نمایش در شبکه‌های اجتماعی

```html
<!-- اجباری -->
<meta property="og:title"       content="آموزش گیتار | سرناز">
<meta property="og:description" content="دوره‌های حرفه‌ای آموزش گیتار از صفر تا پیشرفته">
<meta property="og:image"       content="https://sornaz.com/assets/og/guitar-course.jpg">
<meta property="og:url"         content="https://sornaz.com/courses/guitar">
<meta property="og:type"        content="website">

<!-- اختیاری ولی مفید -->
<meta property="og:site_name"   content="Sornaz Academy">
<meta property="og:locale"      content="fa_IR">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt"    content="دوره آموزش گیتار سرناز">
```

**اندازه تصویر og:image:** دقیقاً **۱۲۰۰×۶۳۰** پیکسل — برای همه پلتفرم‌ها بهینه.

---

## ۶. Twitter Card

```html
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="آموزش گیتار | سرناز">
<meta name="twitter:description" content="دوره‌های حرفه‌ای آموزش گیتار">
<meta name="twitter:image"       content="https://sornaz.com/assets/og/guitar-course.jpg">
<meta name="twitter:image:alt"   content="دوره آموزش گیتار">
<!-- اگه اکانت توییتر داری: -->
<meta name="twitter:site"        content="@SornazAcademy">
```

مقادیر `twitter:card`:
| مقدار | نمایش |
|---|---|
| `summary` | تصویر کوچک + متن |
| `summary_large_image` | تصویر بزرگ — توصیه شده |
| `app` | برای اپلیکیشن موبایل |

---

## ۷. Favicon — همه فرمت‌ها

```html
<!-- مرورگرهای قدیمی -->
<link rel="icon" href="/favicon.ico" sizes="any">

<!-- مرورگرهای مدرن — SVG (بهترین) -->
<link rel="icon" href="/assets/images/logo/favicon.svg" type="image/svg+xml">

<!-- iOS -->
<link rel="apple-touch-icon" href="/assets/images/logo/apple-touch-icon.png" sizes="180x180">

<!-- Android / PWA -->
<link rel="manifest" href="/manifest.json">

<!-- رنگ نوار مرورگر در موبایل -->
<meta name="theme-color" content="#1a1a2e">
```

**فرمت‌های favicon که باید داشته باشی:**
| فایل | اندازه | کاربرد |
|---|---|---|
| `favicon.ico` | 16×16, 32×32 | مرورگرهای قدیمی |
| `favicon.svg` | vector | مرورگرهای مدرن — بهترین |
| `apple-touch-icon.png` | 180×180 | iOS home screen |
| `icon-192.png` | 192×192 | Android |
| `icon-512.png` | 512×512 | Android splash |

---

## ۸. زبان و Alternate

```html
<!-- برای سایت دو زبانه -->
<link rel="alternate" hreflang="fa" href="https://sornaz.com/fa/courses">
<link rel="alternate" hreflang="en" href="https://sornaz.com/en/courses">
<link rel="alternate" hreflang="x-default" href="https://sornaz.com/courses">
```

`x-default` برای صفحه‌ای که زبان خاصی نداره (مثل صفحه انتخاب زبان).

---

## ۹. Preconnect و DNS Prefetch — performance

```html
<!-- Preconnect: اتصال کامل از قبل برقرار میشه — برای منابع مطمئن -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- DNS Prefetch: فقط DNS resolve میشه — برای منابع احتمالی -->
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

<!-- Preload: یه منبع مهم رو با اولویت بالا لود کن -->
<link rel="preload" href="/assets/fonts/Vazirmatn.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/images/hero.webp" as="image">
<link rel="preload" href="/assets/styles/style.css" as="style">
```

**ترتیب اولویت:**
```
preload > preconnect > dns-prefetch > prefetch
```

| دستور | چه وقت |
|---|---|
| `preconnect` | Google Fonts، CDN اصلی |
| `dns-prefetch` | منابع third-party احتمالی |
| `preload` | فونت اصلی، تصویر hero، CSS اصلی |
| `prefetch` | صفحه بعدی که کاربر احتمالاً میره |

---

## ۱۰. Stylesheet‌ها — ترتیب مهمه

```html
<!-- ۱. CSS های خودت — اول -->
<link rel="stylesheet" href="/assets/styles/style.css">

<!-- ۲. فونت‌ها -->
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">

<!-- ۳. آیکون‌ها -->
<link rel="stylesheet" href="/assets/styles/font-awesome.min.css">

<!-- ۴. CSS های نسخه print -->
<link rel="stylesheet" href="/assets/styles/print.css" media="print">
```

**قانون:** هیچ‌وقت JS رو در `<head>` بدون `defer` یا `async` نذار — صفحه رو block می‌کنه.

---

## ۱۱. Schema.org / JSON-LD — structured data

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "name": "Sornaz Academy",
  "url": "https://sornaz.com",
  "logo": "https://sornaz.com/assets/images/logo/logo.png",
  "description": "آموزشگاه موسیقی سرناز",
  "telephone": "+98-21-XXXX-XXXX",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Tehran",
    "addressCountry": "IR"
  },
  "sameAs": [
    "https://instagram.com/sornazacademy",
    "https://t.me/sornazacademy"
  ]
}
</script>
```

برای صفحه دوره:
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "آموزش گیتار مقدماتی",
  "description": "دوره جامع آموزش گیتار از صفر",
  "provider": {
    "@type": "Organization",
    "name": "Sornaz Academy"
  },
  "offers": {
    "@type": "Offer",
    "price": "1500000",
    "priceCurrency": "IRR"
  }
}
</script>
```

---

## ۱۲. تگ‌های امنیتی

```html
<!-- جلوگیری از XSS و injection -->
<meta http-equiv="X-Content-Type-Options" content="nosniff">

<!-- جلوگیری از clickjacking -->
<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">

<!-- Content Security Policy — مشخص می‌کنه منابع از کجا لود بشن -->
<meta http-equiv="Content-Security-Policy"
  content="default-src 'self';
           script-src 'self' https://cdnjs.cloudflare.com;
           style-src  'self' https://fonts.googleapis.com 'unsafe-inline';
           font-src   'self' https://fonts.gstatic.com;
           img-src    'self' data: https:;">
```

**نکته:** CSP رو بهتره از طریق HTTP header (در `.htaccess`) تنظیم کنی، نه meta — قوی‌تره.

---

## ۱۳. نسخه نهایی `default.php` با همه تگ‌ها

```php
<?php global $config;
$lang  = $config['app']['lang'] ?? 'fa';
$dir   = $lang === 'fa' ? 'rtl' : 'ltr';
$title = getPageTitle();
$desc  = $config['page']['description'] ?? '';
$robots = getRobotState();
$base  = baseUrl();
$url   = getFullUrl();
$ogImage = $config['page']['og_image'] ?? $base . '/assets/og/default.jpg';
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
  <meta property="og:url"          content="<?= htmlspecialchars($url) ?>">
  <meta property="og:type"         content="website">
  <meta property="og:site_name"    content="Sornaz Academy">
  <meta property="og:locale"       content="<?= $lang === 'fa' ? 'fa_IR' : 'en_US' ?>">

  <!-- ─── ۵. Twitter Card ────────────────────────── -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($desc) ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($ogImage) ?>">

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

  <!-- ─── ۹. Styles ──────────────────────────────── -->
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/style.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/font-awesome.min.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/styles/print.css" media="print">

  <!-- ─── ۱۰. Schema.org ─────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "Sornaz Academy",
    "url": "<?= $base ?>",
    "logo": "<?= $base ?>/assets/images/logo/logo.png"
  }
  </script>

</head>
```

---

## چک‌لیست قبل از go-live

- [ ] هر صفحه title یکتا دارد (۵۰-۶۰ کاراکتر)
- [ ] هر صفحه description یکتا دارد (۱۵۰-۱۶۰ کاراکتر)
- [ ] og:image با اندازه ۱۲۰۰×۶۳۰ وجود دارد
- [ ] canonical URL صحیح است
- [ ] favicon.svg وجود دارد
- [ ] apple-touch-icon.png وجود دارد
- [ ] manifest.json وجود دارد
- [ ] هیچ JS در head بدون defer/async نیست
- [ ] Schema.org برای نوع محتوا تنظیم شده
- [ ] صفحات admin/login روی noindex هستند
