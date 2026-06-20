<?php
/**
 * mvc/view/user/list.php
 *
 * متغیرها از Controller:
 *   $users   — آرایه کاربران
 *   $total   — تعداد کل
 *   $page    — صفحه فعلی
 *   $pages   — تعداد کل صفحات
 *   $filters — فیلترهای فعال
 */
?>

<section class="section">
  <div class="container">

    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title">کاربران <span class="badge"><?= $total ?></span></h1>
      <a href="/user/create" class="btn btn-primary">
        <i class="fa-solid fa-plus" aria-hidden="true"></i>
        کاربر جدید
      </a>
    </div>

    <!-- Filters -->
    <form class="filter-bar" action="/user/list" method="GET">
      <input
        class="form__input"
        type="search"
        name="search"
        placeholder="جستجو در نام، ایمیل، موبایل..."
        value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
      >
      <select class="form__input" name="status">
        <option value="">همه وضعیت‌ها</option>
        <option value="active"   <?= ($filters['status'] ?? '') === 'active'   ? 'selected' : '' ?>>فعال</option>
        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
        <option value="banned"   <?= ($filters['status'] ?? '') === 'banned'   ? 'selected' : '' ?>>مسدود</option>
      </select>
      <select class="form__input" name="gender">
        <option value="">همه جنسیت‌ها</option>
        <option value="male"   <?= ($filters['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>مرد</option>
        <option value="female" <?= ($filters['gender'] ?? '') === 'female' ? 'selected' : '' ?>>زن</option>
      </select>
      <button type="submit" class="btn btn-ghost">فیلتر</button>
    </form>

    <!-- Table -->
    <?php if (empty($users)): ?>
      <div class="empty-state">
        <i class="fa-solid fa-users-slash" aria-hidden="true"></i>
        <p>کاربری یافت نشد</p>
      </div>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>نام کاربری</th>
              <th>ایمیل</th>
              <th>موبایل</th>
              <th>وضعیت</th>
              <th>تاریخ ثبت</th>
              <th>عملیات</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $i => $user): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username'] ?? '—') ?></td>
                <td><?= htmlspecialchars($user['email']    ?? '—') ?></td>
                <td><?= htmlspecialchars($user['phone']    ?? '—') ?></td>
                <td>
                  <span class="status-badge status-badge--<?= $user['activity_status'] ?>">
                    <?php match($user['activity_status']) {
                      'active'   => print('فعال'),
                      'inactive' => print('غیرفعال'),
                      'banned'   => print('مسدود'),
                      default    => print($user['activity_status']),
                    }; ?>
                  </span>
                </td>
                <td><?= $user['created_at'] ? jdate($user['created_at']) : '—' ?></td>
                <td class="actions">
                  <a href="/user/show/<?= $user['id'] ?>"  class="btn btn-ghost btn--sm">مشاهده</a>
                  <a href="/user/edit/<?= $user['id'] ?>"  class="btn btn-ghost btn--sm">ویرایش</a>
                  <a href="/user/delete/<?= $user['id'] ?>" class="btn btn-outline btn--sm"
                     onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <nav class="pagination" aria-label="صفحه‌بندی">
          <?= pagination('/user/list?page', 2, 'pagination__item', 'pagination__item--active', $page, $pages) ?>
        </nav>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</section>
