<?php /* mvc/view/account/verify-otp.php */ ?>
<section class="section">
  <div class="container container--narrow">
    <div class="auth-card">
      <h1 class="auth-card__title">تأیید شماره موبایل</h1>
      <p class="auth-card__sub">کد ۶ رقمی ارسال‌شده به <strong><?= htmlspecialchars($phone ?? '') ?></strong> را وارد کنید</p>

      <?php if ($error ?? null): ?><div class="alert alert--error" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($success ?? null): ?><div class="alert alert--success" role="status"><?= htmlspecialchars($success) ?></div><?php endif; ?>

      <form class="form" action="/account/verify-phone" method="POST">
        <div class="form__group">
          <label class="form__label" for="code">کد تأیید</label>
          <input class="form__input form__input--otp" type="text" id="code" name="code"
                 maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                 autocomplete="one-time-code" autofocus placeholder="______">
        </div>
        <button type="submit" class="btn btn-primary btn--full">تأیید</button>
      </form>

      <div class="auth-resend">
        <span id="resend-timer">ارسال مجدد تا <span id="countdown">120</span> ثانیه</span>
        <form id="resend-form" action="/account/resend-otp" method="POST" style="display:none">
          <button type="submit" class="btn btn-ghost btn--sm">ارسال مجدد کد</button>
        </form>
      </div>
    </div>
  </div>
</section>
<script>
let s = 120;
const t = setInterval(() => {
  document.getElementById('countdown').textContent = --s;
  if (s <= 0) {
    clearInterval(t);
    document.getElementById('resend-timer').style.display = 'none';
    document.getElementById('resend-form').style.display = 'block';
  }
}, 1000);
</script>
