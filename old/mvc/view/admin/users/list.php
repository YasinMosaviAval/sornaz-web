<?php
/**
 * mvc/view/admin/users/list.php
 * متغیرها: $users, $total, $page, $pages, $roles, $filters
 */
?>

<section class="admin-section">
  <div class="admin-container">

    <!-- Header -->
    <div class="admin-page-header">
      <div class="admin-page-header__title">
        <h1>مدیریت کاربران</h1>
        <span class="admin-badge"><?= number_format($total) ?> کاربر</span>
      </div>
      <div class="admin-page-header__actions">
        <a href="/admin/users/audit-log" class="btn btn-ghost btn--sm">
          <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> لاگ تغییرات
        </a>
      </div>
    </div>

    <!-- فیلترها -->
    <form class="admin-filter-bar" action="/admin/users" method="GET" id="filter-form">
      <div class="admin-filter-bar__search">
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <input type="search" name="search" placeholder="جستجو در نام، ایمیل، موبایل..."
               value="<?= htmlspecialchars($filters['search']) ?>"
               class="admin-filter-bar__input">
      </div>

      <select name="status" class="admin-filter-bar__select" onchange="this.form.submit()">
        <option value="">همه وضعیت‌ها</option>
        <option value="active"   <?= $filters['status']==='active'   ? 'selected':'' ?>>فعال</option>
        <option value="inactive" <?= $filters['status']==='inactive' ? 'selected':'' ?>>غیرفعال</option>
        <option value="banned"   <?= $filters['status']==='banned'   ? 'selected':'' ?>>مسدود</option>
      </select>

      <select name="role" class="admin-filter-bar__select" onchange="this.form.submit()">
        <option value="">همه نقش‌ها</option>
        <?php foreach ($roles as $role): ?>
          <option value="<?= htmlspecialchars($role['name']) ?>"
                  <?= $filters['role']===$role['name'] ? 'selected':'' ?>>
            <?= htmlspecialchars($role['name']) ?>
            (<?= $role['user_count'] ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <select name="approved" class="admin-filter-bar__select" onchange="this.form.submit()">
        <option value="">همه</option>
        <option value="1" <?= $filters['approved']==='1' ? 'selected':'' ?>>تأیید شده</option>
        <option value="0" <?= $filters['approved']==='0' ? 'selected':'' ?>>تأیید نشده</option>
      </select>

      <label class="admin-filter-bar__check">
        <input type="checkbox" name="no_role" value="1"
               <?= $filters['no_role'] ? 'checked':'' ?> onchange="this.form.submit()">
        <span>بدون نقش</span>
      </label>

      <button type="submit" class="btn btn-primary btn--sm">اعمال</button>
      <?php if (array_filter($filters)): ?>
        <a href="/admin/users" class="btn btn-ghost btn--sm">پاک کردن</a>
      <?php endif; ?>
    </form>

    <!-- جدول -->
    <?php if (empty($users)): ?>
      <div class="admin-empty-state">
        <i class="fa-solid fa-users-slash" aria-hidden="true"></i>
        <p>کاربری یافت نشد</p>
      </div>
    <?php else: ?>

      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>کاربر</th>
              <th>ایمیل / موبایل</th>
              <th>نقش‌ها</th>
              <th>وضعیت</th>
              <th>تاریخ عضویت</th>
              <th>آخرین بازدید</th>
              <th>عملیات</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr class="admin-table__row">

                <td class="admin-table__cell--id"><?= $user['id'] ?></td>

                <td>
                  <div class="admin-user-cell">
                    <?php if ($user['avatar']): ?>
                      <img src="<?= htmlspecialchars($user['avatar']) ?>"
                           alt="" class="admin-avatar">
                    <?php else: ?>
                      <div class="admin-avatar admin-avatar--placeholder">
                        <?= mb_substr($user['fullname'] ?? $user['username'] ?? '?', 0, 1) ?>
                      </div>
                    <?php endif; ?>
                    <div class="admin-user-cell__info">
                      <span class="admin-user-cell__name">
                        <?= htmlspecialchars($user['fullname'] ?? '—') ?>
                      </span>
                      <span class="admin-user-cell__username">
                        @<?= htmlspecialchars($user['username'] ?? '') ?>
                      </span>
                    </div>
                  </div>
                </td>

                <td>
                  <div class="admin-contact-cell">
                    <?php if ($user['email']): ?>
                      <span><?= htmlspecialchars($user['email']) ?></span>
                    <?php endif; ?>
                    <?php if ($user['phone']): ?>
                      <span class="admin-contact-cell__phone"><?= htmlspecialchars($user['phone']) ?></span>
                    <?php endif; ?>
                  </div>
                </td>

                <td>
                  <div class="admin-roles-cell">
                    <?php if (empty($user['roles'])): ?>
                      <span class="role-badge role-badge--empty">بدون نقش</span>
                    <?php else: ?>
                      <?php foreach ($user['roles'] as $roleName): ?>
                        <?php
                        // پیدا کردن رنگ نقش
                        $roleColor = '#6b7280';
                        foreach ($roles as $r) {
                          if ($r['name'] === $roleName) { $roleColor = $r['color'] ?? '#6b7280'; break; }
                        }
                        ?>
                        <span class="role-badge"
                              style="--role-color: <?= htmlspecialchars($roleColor) ?>">
                          <?= htmlspecialchars($roleName) ?>
                        </span>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </td>

                <td>
                  <span class="status-chip status-chip--<?= $user['activity_status'] ?>">
                    <?= match($user['activity_status']) {
                      'active'   => 'فعال',
                      'inactive' => 'غیرفعال',
                      'banned'   => 'مسدود',
                      default    => $user['activity_status'],
                    } ?>
                  </span>
                  <?php if (!$user['approved_at']): ?>
                    <span class="status-chip status-chip--warning">تأیید نشده</span>
                  <?php endif; ?>
                </td>

                <td class="admin-table__cell--date">
                  <?= $user['created_at'] ? jdate($user['created_at']) : '—' ?>
                </td>

                <td class="admin-table__cell--date">
                  <?= $user['last_visit_time'] ? jdate($user['last_visit_time']) : 'هیچ‌وقت' ?>
                </td>

                <td>
                  <div class="admin-actions">
                    <a href="/admin/users/show/<?= $user['id'] ?>"
                       class="btn btn-ghost btn--xs" title="مشاهده و ویرایش">
                      <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                    </a>
                    <?php if ($user['activity_status'] !== 'banned'): ?>
                      <form action="/admin/users/status/<?= $user['id'] ?>" method="POST"
                            style="display:inline">
                        <input type="hidden" name="status" value="banned">
                        <button type="submit" class="btn btn-outline btn--xs btn--danger"
                                onclick="return confirm('مسدود شود؟')" title="مسدود کردن">
                          <i class="fa-solid fa-ban" aria-hidden="true"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form action="/admin/users/status/<?= $user['id'] ?>" method="POST"
                            style="display:inline">
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-outline btn--xs" title="فعال کردن">
                          <i class="fa-solid fa-check" aria-hidden="true"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <nav class="admin-pagination" aria-label="صفحه‌بندی">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="/admin/users?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"
               class="admin-pagination__item <?= $i === $page ? 'admin-pagination__item--active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</section>