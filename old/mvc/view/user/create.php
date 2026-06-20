<?php
/**
 * mvc/view/user/create.php
 *
 * متغیرها از Controller:
 *   $errors  — آرایه خطاهای validation (اختیاری)
 *   $old     — مقادیر قبلی فرم برای نمایش مجدد (اختیاری)
 */
$isEdit = false;
?>

<section class="section">
  <div class="container container--narrow">

    <div class="page-header">
      <h1 class="page-title">افزودن کاربر جدید</h1>
      <a href="/user/list" class="btn btn-ghost">
        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        بازگشت
      </a>
    </div>

    <?php require __DIR__ . '/_form.php'; ?>

  </div>
</section>
