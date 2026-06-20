<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');

// dump($settings);
/**
 * mvc/view/account/login.php
 * متغیرها: $error, $success, $old, $method
 */
$method = $method ?? 'email';
?>

<!--
  <main>
    <form action="<?//= baseUrl()?>/login" method="post">
      <div>
        <label for="email"><?//= translate($settings, 'authentication_email') ?></label>
        <i class="icon-copy dw dw-user1"></i>
        <input type="text" id="email" name="email" placeholder="<?//= translate($settings, 'authentication_email_placeholder') ?>">
      </div>

      <div>
        <label for="password"><?//= translate($settings, 'authentication_password') ?></label>
        <i class="dw dw-padlock1"></i>
        <input type="password" id="password" name="password" placeholder="********">
      </div>

      <div>
        <div>
          <input type="checkbox" id="customCheck1" />
          <label for="customCheck1"><?//= translate($settings, 'authentication_remember') ?></label>
        </div>
        <a href="<?//= baseUrl()?>/account/forgotPassword"><?//= translate($settings, 'authentication_forgot_password') ?></a>
      </div>

      <button type="submit"><?//= translate($settings, 'authentication_login') ?></button>
      <br><br><?//= translate($settings, 'authentication_or') ?><br><br>
      <a href="<?//= baseUrl()?>/account/register"><?//= translate($settings, 'authentication_register') ?></a>
    </form>
  </main>
-->
<main class="login">
  <!-- <section class="section"> -->
    <!-- <div class="container container--narrow">
      <div class="auth-card"> -->
        <!-- <h1 class="auth-card__title">ورود</h1> -->
        <div class="header">
            <h1><?= translate($settings, 'authentication_login') ?></h1>
        </div>
        
        <br>
        <div class="auth-tabs" style="width: 100%; text-align: center;">
          <button type="button" class="auth-tab <?= $method==='email'?'auth-tab--active':'' ?>" onclick="switchMethod('email')">ایمیل</button>
          <button type="button" class="auth-tab <?= $method==='phone'?'auth-tab--active':'' ?>" onclick="switchMethod('phone')">موبایل</button>
        </div>

        <? if ($error ?? null): ?>
          <div class="alert alert--error" role="alert"><?= htmlspecialchars($error) ?></div>
        <? endif; ?>
        <? if ($success ?? null): ?>
          <div class="alert alert--success" role="status"><?= htmlspecialchars($success) ?></div>
        <? endif; ?>

        <form class="form" action="/account/login" method="POST" novalidate>
          <input type="hidden" name="method" id="input-method" value="<?= $method ?>">

          <div class="form__group method-email <?= $method!=='email'?'hidden':'' ?>">
            <label class="form__label" for="email"><?= translate($settings, 'authentication_email') ?></label>
            <input class="form__input" type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" autocomplete="email" placeholder="<?= translate($settings, 'authentication_email_placeholder') ?>">
          </div>

          <br>
          
          <div class="form__group method-email <?= $method!=='email'?'hidden':'' ?>">
            <label class="form__label" for="password">
              <?= translate($settings, 'authentication_password') ?>
              <a href="<?=baseUrl()?>/account/forgotPassword" class="form__label-link"><?= translate($settings, 'authentication_forgot_password') ?></a>
            </label>
            <input class="form__input" type="password" id="password" name="password" autocomplete="current-password" placeholder="********">
          </div>

          <!-- <div class="form__group method-email <?//= $method!=='email'?'hidden':'' ?>">
            <label class="form__check">
              <input type="checkbox" name="remember" value="1">
              <span>مرا به خاطر بسپار</span>
            </label>
          </div> -->

          <div class="form__group method-phone <?= $method!=='phone'?'hidden':'' ?>">
            <label class="form__label" for="phone">شماره موبایل</label>
            <input class="form__input" type="tel" id="phone" name="phone" placeholder="09123456789" autocomplete="tel">
            <br>
            <br>
            <span class="form__hint">کد تأیید به این شماره ارسال می‌شود</span>
          </div>

          <br>
          
          <div style="text-align: center;">
            <button type="submit" class="btn btn-primary btn--full">
              <span class="method-email <?= $method!=='email'?'hidden':'' ?>">ورود</span>
              <span class="method-phone <?= $method!=='phone'?'hidden':'' ?>">دریافت کد</span>
            </button>
          </div>

          <!-- <p>
            <a href="/account/google" class="btn-social btn-social--google">
              <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.707A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.707V4.961H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.039l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z"/>
              </svg>
              ادامه با Google
            </a>
          </p> -->
          <p class="auth-card__sub">حساب ندارید؟ <a href="/register">ثبت‌نام</a></p>
        </form>
      <!-- </div>
    </div> -->
  <!-- </section> -->
</main>

<script>
  function switchMethod(m){
    document.getElementById('input-method').value=m;
    document.querySelectorAll('.auth-tab').forEach(t=>t.classList.remove('auth-tab--active'));
    document.querySelectorAll('.method-email,.method-phone').forEach(el=>el.classList.add('hidden'));
    document.querySelectorAll('.method-'+m).forEach(el=>el.classList.remove('hidden'));
    event.target.classList.add('auth-tab--active');
  }
</script>
