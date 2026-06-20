<?php
/**
 * mvc/view/user/edit.php
 *
 * متغیرها از Controller:
 *   $user    — داده‌های کاربر
 *   $errors  — خطاهای validation (اختیاری)
 */
$isEdit = true;
$old    = $user; // مقادیر فعلی رو به فرم می‌ده
?>

<section class="section">
  <div class="container container--narrow">

    <div class="page-header">
      <h1 class="page-title">ویرایش: <?= htmlspecialchars($user['username'] ?? '') ?></h1>
      <a href="/user/show/<?= $user['id'] ?>" class="btn btn-ghost">
        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        بازگشت به پروفایل
      </a>
    </div>

    <?php require __DIR__ . '/_form.php'; ?>

  </div>
</section>
