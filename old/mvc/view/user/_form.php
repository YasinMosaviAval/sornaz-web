<?php
/**
 * mvc/view/user/_form.php
 *
 * فرم مشترک برای create و edit
 *
 * متغیرهای لازم:
 *   $isEdit  — bool: true در حالت edit
 *   $old     — مقادیر پیش‌فرض فیلدها
 *   $errors  — آرایه خطاها
 *   $user    — (فقط در edit) داده کاربر برای action URL
 */
$errors = $errors ?? [];
$old    = $old    ?? [];
$action = $isEdit ? "/user/update/{$user['id']}" : '/user/store';
?>

<form class="form" action="<?= $action ?>" method="POST" novalidate>

  <!-- ایمیل -->
  <div class="form__group <?= isset($errors['email']) ? 'form__group--error' : '' ?>">
    <label class="form__label" for="email">
      ایمیل <span class="form__required" aria-hidden="true">*</span>
    </label>
    <input
      class="form__input"
      type="email"
      id="email"
      name="email"
      value="<?= htmlspecialchars($old['email'] ?? '') ?>"
      autocomplete="email"
      required
    >
    <?php if (isset($errors['email'])): ?>
      <span class="form__error" role="alert"><?= htmlspecialchars($errors['email']) ?></span>
    <?php endif; ?>
  </div>

  <!-- نام کاربری -->
  <div class="form__group <?= isset($errors['username']) ? 'form__group--error' : '' ?>">
    <label class="form__label" for="username">
      نام کاربری <span class="form__required" aria-hidden="true">*</span>
    </label>
    <input
      class="form__input"
      type="text"
      id="username"
      name="username"
      value="<?= htmlspecialchars($old['username'] ?? '') ?>"
      autocomplete="username"
      minlength="3"
      required
    >
    <?php if (isset($errors['username'])): ?>
      <span class="form__error" role="alert"><?= htmlspecialchars($errors['username']) ?></span>
    <?php endif; ?>
  </div>

  <!-- رمز عبور -->
  <div class="form__group <?= isset($errors['password']) ? 'form__group--error' : '' ?>">
    <label class="form__label" for="password">
      رمز عبور
      <?php if (!$isEdit): ?>
        <span class="form__required" aria-hidden="true">*</span>
      <?php else: ?>
        <span class="form__hint">(خالی بگذارید تا تغییر نکند)</span>
      <?php endif; ?>
    </label>
    <input
      class="form__input"
      type="password"
      id="password"
      name="password"
      autocomplete="<?= $isEdit ? 'new-password' : 'new-password' ?>"
      minlength="8"
      <?= !$isEdit ? 'required' : '' ?>
    >
    <?php if (isset($errors['password'])): ?>
      <span class="form__error" role="alert"><?= htmlspecialchars($errors['password']) ?></span>
    <?php endif; ?>
  </div>

  <!-- موبایل -->
  <div class="form__group <?= isset($errors['phone']) ? 'form__group--error' : '' ?>">
    <label class="form__label" for="phone">شماره موبایل</label>
    <input
      class="form__input"
      type="tel"
      id="phone"
      name="phone"
      value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
      autocomplete="tel"
      placeholder="09xxxxxxxxx"
    >
    <?php if (isset($errors['phone'])): ?>
      <span class="form__error" role="alert"><?= htmlspecialchars($errors['phone']) ?></span>
    <?php endif; ?>
  </div>

  <!-- کد ملی -->
  <div class="form__group">
    <label class="form__label" for="national_code">کد ملی</label>
    <input
      class="form__input"
      type="text"
      id="national_code"
      name="national_code"
      value="<?= htmlspecialchars($old['national_code'] ?? '') ?>"
      maxlength="10"
      placeholder="0000000000"
    >
  </div>

  <!-- جنسیت -->
  <div class="form__group <?= isset($errors['gender']) ? 'form__group--error' : '' ?>">
    <label class="form__label" for="gender">جنسیت</label>
    <select class="form__input" id="gender" name="gender">
      <option value="">انتخاب کنید</option>
      <option value="male"   <?= ($old['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>مرد</option>
      <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>زن</option>
      <option value="other"  <?= ($old['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>سایر</option>
    </select>
  </div>

  <!-- تاریخ تولد -->
  <div class="form__group">
    <label class="form__label" for="birthday">تاریخ تولد</label>
    <input
      class="form__input"
      type="date"
      id="birthday"
      name="birthday"
      value="<?= htmlspecialchars($old['birthday'] ?? '') ?>"
    >
  </div>

  <!-- دکمه‌ها -->
  <div class="form__actions">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'ذخیره تغییرات' : 'ثبت کاربر' ?>
    </button>
    <a href="<?= $isEdit ? "/user/show/{$user['id']}" : '/user/list' ?>" class="btn btn-ghost">
      انصراف
    </a>
  </div>

</form>
