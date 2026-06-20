<?php /* mvc/view/account/forgot-password.php */ ?>
<section class="section">
  <div class="container container--narrow">
    <div class="auth-card">
      <h1 class="auth-card__title">فراموشی رمز عبور</h1>
      <p class="auth-card__sub">ایمیل یا شماره موبایل خود را وارد کنید</p>

      <?php if ($error ?? null): ?><div class="alert alert--error" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <form class="form" action="/account/forgot-password" method="POST">
        <div class="form__group">
          <label class="form__label" for="identifier">ایمیل یا شماره موبایل</label>
          <input class="form__input" type="text" id="identifier" name="identifier"
                 value="<?= htmlspecialchars($old['identifier'] ?? '') ?>"
                 placeholder="example@email.com یا 09xxxxxxxxx" autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn--full">ارسال کد بازیابی</button>
      </form>

      <p class="auth-card__sub"><a href="/login">بازگشت به ورود</a></p>
    </div>
  </div>
</section>
