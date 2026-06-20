# ساختار استاندارد View‌ها

## قوانین کلی

هر view فایل فقط HTML خروجی می‌ده:
- ❌ هیچ query به DB نمی‌زنه
- ❌ هیچ `header()` یا `redirect` نمی‌ده
- ❌ هیچ business logic ندارد
- ✅ فقط داده‌های آماده رو نمایش می‌ده
- ✅ از `htmlspecialchars()` برای نمایش متن کاربر استفاده می‌کنه

---

## ساختار HTML هر صفحه

```
<main>                       ← توسط default.php رندر میشه
  │
  ├── <section class="hero">         ← بخش اول/Hero (اگه داشت)
  │     └── <div class="container">
  │
  ├── <section class="section X">   ← هر بخش مجزا
  │     └── <div class="container">
  │           ├── <h2 class="section__title">
  │           └── محتوا
  │
  └── ...
</main>
```

---

## CSS Naming — BEM

```
block__element--modifier

.card                   ← block
.card__title            ← element
.card__title--large     ← modifier
.card--featured         ← modifier روی block
```

---

## کلاس‌های Layout آماده

```html
<!-- Container -->
<div class="container">...</div>           <!-- max-width + padding -->

<!-- Grid -->
<div class="grid grid--2">...</div>        <!-- 2 ستون -->
<div class="grid grid--3">...</div>        <!-- 3 ستون -->
<div class="grid grid--4">...</div>        <!-- 4 ستون -->

<!-- Section -->
<section class="section">...</section>     <!-- padding بالا/پایین -->
```

---

## تگ‌های Semantic که باید استفاده بشن

| محتوا | تگ |
|---|---|
| هر بخش مجزای صفحه | `<section aria-labelledby="...">` |
| یه آیتم مستقل (دوره، مقاله) | `<article>` |
| sidebar یا فیلتر | `<aside>` |
| فرم | `<form>` |
| لیست لینک‌ها | `<ul role="list">` |
| اطلاعات تماس | `<address>` |

---

## الگوی کارت دوره

```php
<article class="card" aria-labelledby="course-<?= $course['id'] ?>-title">
  <div class="card__image">
    <img
      src="<?= baseUrl() ?>/assets/images/courses/<?= $course['image'] ?>"
      alt="<?= htmlspecialchars($course['title']) ?>"
      loading="lazy"
      width="400"
      height="250"
    >
  </div>
  <div class="card__body">
    <span class="badge"><?= htmlspecialchars($course['category']) ?></span>
    <h3 id="course-<?= $course['id'] ?>-title" class="card__title">
      <?= htmlspecialchars($course['title']) ?>
    </h3>
    <p class="card__text"><?= htmlspecialchars($course['summary']) ?></p>
  </div>
  <div class="card__footer">
    <span class="card__price"><?= number_format($course['price']) ?> تومان</span>
    <a href="/courses/<?= $course['slug'] ?>" class="btn btn-primary btn--sm">
      مشاهده دوره
    </a>
  </div>
</article>
```

---

## الگوی صفحه با Sidebar

```php
<section class="section">
  <div class="container">
    <div class="layout-sidebar">

      <aside class="sidebar" aria-label="فیلترها">
        <!-- فیلتر، دسته‌بندی، ... -->
      </aside>

      <div class="content-area">
        <!-- لیست دوره‌ها، مقالات، ... -->
      </div>

    </div>
  </div>
</section>
```

---

## الگوی فرم

```php
<section class="section">
  <div class="container container--narrow">

    <h1 class="page-title">ورود به حساب</h1>

    <form class="form" action="/login" method="POST" novalidate>

      <div class="form__group">
        <label class="form__label" for="email">ایمیل</label>
        <input
          class="form__input"
          type="email"
          id="email"
          name="email"
          required
          autocomplete="email"
          placeholder="example@email.com"
        >
        <span class="form__error" role="alert"></span>
      </div>

      <div class="form__group">
        <label class="form__label" for="password">رمز عبور</label>
        <input
          class="form__input"
          type="password"
          id="password"
          name="password"
          required
          autocomplete="current-password"
        >
      </div>

      <button type="submit" class="btn btn-primary btn--full">ورود</button>

    </form>

  </div>
</section>
```

---

## الگوی پیام Flash

```php
<!-- در view، اگه Controller پیام فرستاد نمایش بده -->
<?php if ($error ?? null): ?>
  <div class="alert alert--error" role="alert">
    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<?php if ($success ?? null): ?>
  <div class="alert alert--success" role="status">
    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
    <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>
```

---

## کلاس‌های Button

```html
<a href="#" class="btn btn-primary">اصلی</a>       <!-- پر رنگ -->
<a href="#" class="btn btn-ghost">ساده</a>          <!-- بدون پس‌زمینه -->
<a href="#" class="btn btn-outline">خط دار</a>      <!-- border دار -->

<!-- اندازه -->
<a href="#" class="btn btn-primary btn--sm">کوچک</a>
<a href="#" class="btn btn-primary btn--lg">بزرگ</a>
<a href="#" class="btn btn-primary btn--full">تمام عرض</a>
```

---

## نکته: htmlspecialchars

هر متنی که از DB یا کاربر میاد باید wrap بشه:

```php
<!-- ✅ درست -->
<?= htmlspecialchars($user['name']) ?>

<!-- ❌ اشتباه — XSS -->
<?= $user['name'] ?>

<!-- ✅ درست برای HTML که خودت ساختی و مطمئنی -->
<?= $content ?>
```
