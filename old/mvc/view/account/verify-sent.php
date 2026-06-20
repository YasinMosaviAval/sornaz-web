<?php /* mvc/view/account/verify-sent.php */ ?>
<section class="section">
  <div class="container container--narrow">
    <div class="auth-card auth-card--success">
      <div class="auth-icon"><i class="fa-solid fa-envelope-circle-check" aria-hidden="true"></i></div>
      <h1 class="auth-card__title">کد تأیید ارسال شد</h1>
      <?php if (($method ?? '') === 'email'): ?>
        <p>لینک تأیید به آدرس <strong><?= htmlspecialchars($email ?? '') ?></strong> ارسال شد.</p>
        <p class="auth-card__hint">پوشه spam/junk را هم بررسی کنید.</p>
      <?php else: ?>
        <p>کد تأیید به شماره <strong><?= htmlspecialchars($phone ?? '') ?></strong> ارسال شد.</p>
        <a href="/account/verify-phone" class="btn btn-primary">وارد کردن کد</a>
      <?php endif; ?>
    </div>
  </div>
</section>
---
<?php /* mvc/view/account/verified.php */ ?>
<section class="section">
  <div class="container container--narrow">
    <div class="auth-card auth-card--success">
      <div class="auth-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
      <h1 class="auth-card__title">حساب شما تأیید شد!</h1>
      <p>اکنون می‌توانید از تمام امکانات سایت استفاده کنید.</p>
      <a href="/" class="btn btn-primary">رفتن به صفحه اصلی</a>
    </div>
  </div>
</section>
