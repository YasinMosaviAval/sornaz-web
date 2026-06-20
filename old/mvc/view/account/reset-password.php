<?php /* mvc/view/account/reset-password.php */ ?>
<section class="section">
  <div class="container container--narrow">
    <div class="auth-card">
      <h1 class="auth-card__title">رمز عبور جدید</h1>

      <?php if ($errors['password'] ?? null): ?>
        <div class="alert alert--error" role="alert"><?= htmlspecialchars($errors['password']) ?></div>
      <?php endif; ?>

      <form class="form" action="/account/reset-password" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

        <div class="form__group">
          <label class="form__label" for="password">رمز عبور جدید <span class="form__required">*</span></label>
          <input class="form__input" type="password" id="password" name="password"
                 autocomplete="new-password" minlength="8" autofocus>
        </div>

        <div class="form__group <?= isset($errors['password_confirm']) ? 'form__group--error' : '' ?>">
          <label class="form__label" for="password_confirm">تکرار رمز عبور <span class="form__required">*</span></label>
          <input class="form__input" type="password" id="password_confirm" name="password_confirm"
                 autocomplete="new-password">
          <?php if ($errors['password_confirm'] ?? null): ?>
            <span class="form__error" role="alert"><?= htmlspecialchars($errors['password_confirm']) ?></span>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn--full">ذخیره رمز جدید</button>
      </form>
    </div>
  </div>
</section>
