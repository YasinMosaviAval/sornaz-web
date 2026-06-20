<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
// dump($settings);
/**
 * mvc/view/account/register.php
 * متغیرها: $errors, $old, $method
 */
$errors = $errors ?? [];
$old    = $old    ?? [];
$method = $method ?? 'email';
?>

<!--
  <main>
      <form action="<?//= baseUrl()?>/register" method="post">
      <div>
        <label><?//= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
        <input type="email" name="email"/>
      </div>
      <div>
        <label><?//= translate($settings, 'authentication_username') ?> <span class="required">*</span></label>
        <input type="text" name="username"/>
      </div>
      <div>
        <label><?//= translate($settings, 'authentication_password') ?> <span class="required">*</span></label>
        <input type="password" name="password1"/>
      </div>
      <div>
        <label><?//= translate($settings, 'authentication_confirm_password') ?> <span class="required">*</span></label>
        <input type="password" name="password2"/>
      </div>

          <button type="submit"><?//= translate($settings, 'authentication_register') ?></button>
      <br><br><?//= translate($settings, 'authentication_or') ?><br><br>
      <a href="<?//= baseUrl()?>/account/login"><?//= translate($settings, 'authentication_login') ?></a>
      </form>
  </main>
-->

<main class="register">
    <div class="header">
        <h1><?= translate($settings, 'authentication_register') ?></h1>
        <!-- <p class="sub-header"><?//= translate($settings, 'authentication_register_subheader') ?></p> -->
    </div>
  <section class="section">
    <!-- <div class="container container--narrow"> -->
      <!-- <div class="auth-card"> -->

        <!-- Tab -->
        <br>
        <div class="auth-tabs" style="width: 100%; text-align: center;">
          <button type="button" class="auth-tab <?= $method === 'email' ? 'auth-tab--active' : '' ?>" onclick="switchMethod('email')">ایمیل</button>
          <button type="button" class="auth-tab <?= $method === 'phone' ? 'auth-tab--active' : '' ?>" onclick="switchMethod('phone')">موبایل</button>
        </div>

        <? if (!empty($errors['_general'] ?? null)): ?>
          <div class="alert alert--error" role="alert"><?= htmlspecialchars($errors['_general']) ?></div>
        <? endif; ?>

        <form  action="<?=baseUrl()?>/register" method="post" novalidate>
          <input type="hidden" name="register_method" id="input-method" value="<?= $method ?>">

          <!-- نام -->
          <!-- <div class="form__group">
            <label class="form__label" for="fullname">نام کامل</label>
            <input class="form__input" type="text" id="fullname" name="fullname"
                    value="<?//= htmlspecialchars($old['fullname'] ?? '') ?>" autocomplete="name">
          </div> -->

          <!-- ایمیل -->
          <div class="form__group method-email <?= isset($errors['email']) ? 'form__group--error' : '' ?>">
            <label class="form__label" for="email"><?= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
            <input class="form__input" type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" autocomplete="email" placeholder="<?= translate($settings, 'authentication_email_placeholder') ?>">
            <? if (isset($errors['email'])): ?>
              <span class="form__error" role="alert"><?= htmlspecialchars($errors['email']) ?></span>
            <? endif; ?>
          </div>
          
          <!-- موبایل -->
          <div class="form__group method-phone <?= $method !== 'phone' ? 'hidden' : '' ?> <?= isset($errors['phone']) ? 'form__group--error' : '' ?>">
            <label class="form__label" for="phone">شماره موبایل <span class="required">*</span></label>
            <input class="form__input" type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="09123456789" autocomplete="tel">
            <? if (isset($errors['phone'])): ?>
              <span class="form__error" role="alert"><?= htmlspecialchars($errors['phone']) ?></span>
            <? endif; ?>
          </div>

          <br>


          <!-- رمز -->
          <div class="form__group method-email method-phone <?= isset($errors['password1']) ? 'form__group--error' : '' ?>">
            <label class="form__label" for="password1"><?= translate($settings, 'authentication_password') ?> <span class="required">*</span></label>
            <input class="form__input" type="password" id="password1" name="password1" autocomplete="new-password" minlength="8" placeholder="********">
            <? if (isset($errors['password1'])): ?>
              <span class="form__error" role="alert"><?= htmlspecialchars($errors['password1']) ?></span>
            <? endif; ?>
          </div>

          <br>
          <div class="form__group method-email method-phone <?= isset($errors['password2']) ? 'form__group--error' : '' ?>">
            <label class="form__label" for="password2"><?= translate($settings, 'authentication_confirm_password') ?> <span class="required">*</span></label>
            <input class="form__input" type="password" id="password2" name="password2" autocomplete="new-password" placeholder="********">
            <? if (isset($errors['password2'])): ?>
              <span class="form__error" role="alert"><?= htmlspecialchars($errors['password2']) ?></span>
            <? endif; ?>
          </div>

<br>






















          
          <div style="text-align: center;">
            <button type="submit" class="btn btn-primary btn--full"><?= translate($settings, 'authentication_register') ?></button>
          </div>

        
          <div style="text-align: center;">
            <p class="auth-card__sub">قبلاً ثبت‌نام کرده‌اید؟ <a href="<?=baseUrl()?>/account/login"><?= translate($settings, 'authentication_login') ?></a></p>

            <!-- Google -->
            <!-- <a href="/account/google" class="btn-social btn-social--google">
              <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.707A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.707V4.961H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.039l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z"/>
              </svg>
              ادامه با Google
            </a> -->
          </div>
        </form>

      <!-- </div> -->
    <!-- </div> -->
  </section>
</main>


<script>
function switchMethod(method) {
  document.getElementById('input-method').value = method;
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('auth-tab--active'));
  document.querySelectorAll('.method-email, .method-phone').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.method-' + method).forEach(el => el.classList.remove('hidden'));
  event.target.classList.add('auth-tab--active');
}
</script>
