<?
$home = $data['home'];
// dump($home);
?>

<main class="site-main">
  <section class="hero-section">
    <div class="container">
        <img src="<?=baseUrl() . $home['home_background_image_1']['source'] ?>" alt="<?= translate($home, 'home_background_image_1') ?>" class="hero-image">
    </div>
    <div class="container">
      <h2 class="hero-title"><?= translate($home, 'home_main_title') ?></h2>
      <p class="hero-subtitle"><?= translate($home, 'home_main_description') ?></p>
      <a href="<?=baseUrl() . $home['home_main_title_link']['url'] ?>" class="btn-primary"><?= translate($home, 'home_main_title_link') ?></a>
    </div>
  </section>

  <img class="home-image" src="<?=baseUrl() . $home['home_main_logo']['source'] ?>" alt="<?= translate($home, 'home_main_logo') ?>">

  <section class="overview-section">
    <div>
      <h3><?=translate($home, 'home_title_1') ?></h3>
      <div><?= translate($home, 'home_description_1', 'content') ?></div>
    </div>
  </section>
</main>

<!-- ─── Hero ──────────────────────────────────────── -->
<!--
  <section class="hero" aria-labelledby="hero-title">
    <div class="container">
      <h1 id="hero-title" class="hero__title">
        <?//= htmlspecialchars($home['hero_title']['text_fa'] ?? 'آموزش موسیقی حرفه‌ای') ?>
      </h1>
      <p class="hero__subtitle">
        <?//= htmlspecialchars($home['hero_subtitle']['text_fa'] ?? '') ?>
      </p>
      <div class="hero__actions">
        <a href="/courses" class="btn btn-primary">مشاهده دوره‌ها</a>
        <a href="/about"   class="btn btn-ghost">بیشتر بدانید</a>
      </div>
    </div>
  </section>
-->

<!-- ─── Features ─────────────────────────────────── -->
<!--
  <section class="section features" aria-labelledby="features-title">
    <div class="container">
      <h2 id="features-title" class="section__title">چرا سرناز؟</h2>
      <div class="grid grid--3">

        <article class="card">
          <div class="card__icon" aria-hidden="true"><i class="fa-solid fa-music"></i></div>
          <h3 class="card__title">اساتید مجرب</h3>
          <p class="card__body">تدریس توسط اساتید با سابقه و مدرک معتبر</p>
        </article>

        <article class="card">
          <div class="card__icon" aria-hidden="true"><i class="fa-solid fa-graduation-cap"></i></div>
          <h3 class="card__title">گواهینامه رسمی</h3>
          <p class="card__body">دریافت مدرک معتبر پس از پایان دوره</p>
        </article>

        <article class="card">
          <div class="card__icon" aria-hidden="true"><i class="fa-solid fa-laptop"></i></div>
          <h3 class="card__title">آموزش آنلاین</h3>
          <p class="card__body">امکان آموزش حضوری و آنلاین</p>
        </article>

      </div>
    </div>
  </section>
-->
