<?php
/**
 * mvc/view/user/show.php
 *
 * متغیرها از Controller:
 *   $user — داده‌های کاربر
 */
?>

<section class="section">
  <div class="container container--narrow">

    <div class="page-header">
      <h1 class="page-title">پروفایل کاربر</h1>
      <div class="page-header__actions">
        <a href="/user/edit/<?= $user['id'] ?>"  class="btn btn-ghost">ویرایش</a>
        <?php if (isSuperAdmin()): ?>
          <a href="/user/delete/<?= $user['id'] ?>" class="btn btn-outline"
             onclick="return confirm('کاربر حذف شود؟')">حذف</a>
        <?php endif; ?>
        <a href="/user/list" class="btn btn-ghost">
          <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          بازگشت
        </a>
      </div>
    </div>

    <!-- کارت اطلاعات -->
    <div class="card">
      <div class="card__body">

        <dl class="info-list">

          <div class="info-list__row">
            <dt class="info-list__label">شناسه</dt>
            <dd class="info-list__value"><?= $user['id'] ?></dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">نام کاربری</dt>
            <dd class="info-list__value"><?= htmlspecialchars($user['username'] ?? '—') ?></dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">ایمیل</dt>
            <dd class="info-list__value">
              <a href="mailto:<?= htmlspecialchars($user['email'] ?? '') ?>">
                <?= htmlspecialchars($user['email'] ?? '—') ?>
              </a>
            </dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">موبایل</dt>
            <dd class="info-list__value"><?= htmlspecialchars($user['phone'] ?? '—') ?></dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">کد ملی</dt>
            <dd class="info-list__value"><?= htmlspecialchars($user['national_code'] ?? '—') ?></dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">جنسیت</dt>
            <dd class="info-list__value">
              <?php match($user['gender'] ?? '') {
                'male'   => print('مرد'),
                'female' => print('زن'),
                'other'  => print('سایر'),
                default  => print('—'),
              }; ?>
            </dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">تاریخ تولد</dt>
            <dd class="info-list__value">
              <?= $user['birthday'] ? jdate($user['birthday']) : '—' ?>
            </dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">وضعیت</dt>
            <dd class="info-list__value">
              <span class="status-badge status-badge--<?= $user['activity_status'] ?>">
                <?php match($user['activity_status']) {
                  'active'   => print('فعال'),
                  'inactive' => print('غیرفعال'),
                  'banned'   => print('مسدود'),
                  default    => print($user['activity_status']),
                }; ?>
              </span>
            </dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">تاریخ ثبت</dt>
            <dd class="info-list__value">
              <?= $user['created_at'] ? jdate($user['created_at'], 'Y/m/d') : '—' ?>
            </dd>
          </div>

          <div class="info-list__row">
            <dt class="info-list__label">آخرین بازدید</dt>
            <dd class="info-list__value">
              <?= $user['last_visit_time'] ? jdate($user['last_visit_time'], 'Y/m/d') : 'هنوز وارد نشده' ?>
            </dd>
          </div>

          <?php if ($user['approved_at']): ?>
          <div class="info-list__row">
            <dt class="info-list__label">تاریخ تأیید</dt>
            <dd class="info-list__value"><?= jdate($user['approved_at'], 'Y/m/d') ?></dd>
          </div>
          <?php endif; ?>

        </dl>

      </div>

      <!-- عملیات وضعیت -->
      <?php if (isAdmin()): ?>
        <div class="card__footer">
          <?php if ($user['activity_status'] !== 'active'): ?>
            <a href="/user/activate/<?= $user['id'] ?>"   class="btn btn-primary btn--sm">فعال‌سازی</a>
          <?php endif; ?>
          <?php if ($user['activity_status'] !== 'banned'): ?>
            <a href="/user/ban/<?= $user['id'] ?>"        class="btn btn-outline btn--sm">مسدودسازی</a>
          <?php endif; ?>
          <?php if (!$user['approved_at']): ?>
            <a href="/user/approve/<?= $user['id'] ?>"    class="btn btn-ghost btn--sm">تأیید کاربر</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>

  </div>
</section>
