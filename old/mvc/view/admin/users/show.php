<?php
/**
 * mvc/view/admin/users/show.php
 * متغیرها: $user, $all_roles, $perm_groups, $errors
 */
$errors  = $errors  ?? [];
$tab     = $_GET['tab'] ?? 'info';
$saved   = isset($_GET['saved']);
$errMsg  = $_GET['error'] ?? null;
?>

<section class="admin-section">
  <div class="admin-container">

    <!-- Header -->
    <div class="admin-page-header">
      <div class="admin-page-header__title">
        <a href="/admin/users" class="admin-back-btn">
          <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
        <div class="admin-user-header">
          <?php if ($user['avatar_thumb']): ?>
            <img src="<?= htmlspecialchars($user['avatar_thumb']) ?>" alt="" class="admin-avatar admin-avatar--lg">
          <?php else: ?>
            <div class="admin-avatar admin-avatar--lg admin-avatar--placeholder">
              <?= mb_substr($user['fullname'] ?? $user['username'] ?? '?', 0, 1) ?>
            </div>
          <?php endif; ?>
          <div>
            <h1><?= htmlspecialchars($user['fullname'] ?? $user['username'] ?? 'کاربر') ?></h1>
            <span class="admin-user-header__meta">
              شناسه: <?= $user['id'] ?> —
              <?= htmlspecialchars($user['email'] ?? '') ?>
            </span>
          </div>
        </div>
      </div>
      <div class="admin-page-header__actions">
        <span class="status-chip status-chip--<?= $user['activity_status'] ?>">
          <?= match($user['activity_status']) {
            'active' => 'فعال', 'inactive' => 'غیرفعال', 'banned' => 'مسدود', default => ''
          } ?>
        </span>
        <?php if ($user['approved_at']): ?>
          <span class="status-chip status-chip--success">تأیید شده</span>
        <?php else: ?>
          <a href="/admin/users/approve/<?= $user['id'] ?>" class="btn btn-primary btn--sm">
            <i class="fa-solid fa-check" aria-hidden="true"></i> تأیید حساب
          </a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($saved): ?>
      <div class="alert alert--success" role="status">تغییرات ذخیره شد</div>
    <?php endif; ?>
    <?php if ($errMsg): ?>
      <div class="alert alert--error" role="alert"><?= htmlspecialchars($errMsg) ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="admin-tabs">
      <?php $tabs = [
        'info'       => ['label' => 'اطلاعات پایه',  'icon' => 'fa-user'],
        'access'     => ['label' => 'دسترسی',         'icon' => 'fa-shield-halved'],
        'sessions'   => ['label' => 'نشست‌ها',        'icon' => 'fa-computer'],
        'security'   => ['label' => 'امنیت',           'icon' => 'fa-lock'],
        'audit'      => ['label' => 'لاگ تغییرات',    'icon' => 'fa-clock-rotate-left'],
      ]; ?>
      <?php foreach ($tabs as $key => $t): ?>
        <a href="/admin/users/show/<?= $user['id'] ?>?tab=<?= $key ?>"
           class="admin-tab <?= $tab === $key ? 'admin-tab--active' : '' ?>">
          <i class="fa-solid <?= $t['icon'] ?>" aria-hidden="true"></i>
          <?= $t['label'] ?>
          <?php if ($key === 'sessions' && count($user['sessions'])): ?>
            <span class="admin-badge admin-badge--sm"><?= count($user['sessions']) ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- ═══════ TAB: اطلاعات پایه ════════════════ -->
    <?php if ($tab === 'info'): ?>
    <div class="admin-tab-panel">
      <form class="form" action="/admin/users/update/<?= $user['id'] ?>" method="POST">

        <div class="form-grid form-grid--2">

          <div class="form__group <?= isset($errors['fullname']) ? 'form__group--error':'' ?>">
            <label class="form__label" for="fullname">نام کامل</label>
            <input class="form__input" type="text" id="fullname" name="fullname"
                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>">
            <?php if ($errors['fullname'] ?? null): ?>
              <span class="form__error"><?= htmlspecialchars($errors['fullname']) ?></span>
            <?php endif; ?>
          </div>

          <div class="form__group <?= isset($errors['username']) ? 'form__group--error':'' ?>">
            <label class="form__label" for="username">نام کاربری</label>
            <input class="form__input" type="text" id="username" name="username"
                   value="<?= htmlspecialchars($user['username'] ?? '') ?>">
            <?php if ($errors['username'] ?? null): ?>
              <span class="form__error"><?= htmlspecialchars($errors['username']) ?></span>
            <?php endif; ?>
          </div>

          <div class="form__group <?= isset($errors['email']) ? 'form__group--error':'' ?>">
            <label class="form__label" for="email">ایمیل</label>
            <input class="form__input" type="email" id="email" name="email"
                   value="<?= htmlspecialchars($user['email'] ?? '') ?>">
          </div>

          <div class="form__group <?= isset($errors['phone']) ? 'form__group--error':'' ?>">
            <label class="form__label" for="phone">موبایل</label>
            <input class="form__input" type="tel" id="phone" name="phone"
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>

          <div class="form__group">
            <label class="form__label" for="national_code">کد ملی</label>
            <input class="form__input" type="text" id="national_code" name="national_code"
                   value="<?= htmlspecialchars($user['national_code'] ?? '') ?>" maxlength="10">
          </div>

          <div class="form__group">
            <label class="form__label" for="gender">جنسیت</label>
            <select class="form__input" id="gender" name="gender">
              <option value="">انتخاب کنید</option>
              <option value="male"   <?= ($user['gender']??'')==='male'  ?'selected':'' ?>>مرد</option>
              <option value="female" <?= ($user['gender']??'')==='female'?'selected':'' ?>>زن</option>
              <option value="other"  <?= ($user['gender']??'')==='other' ?'selected':'' ?>>سایر</option>
            </select>
          </div>

          <div class="form__group">
            <label class="form__label" for="birthday">تاریخ تولد</label>
            <input class="form__input" type="date" id="birthday" name="birthday"
                   value="<?= htmlspecialchars($user['birthday'] ?? '') ?>">
          </div>

          <div class="form__group">
            <label class="form__label" for="activity_status">وضعیت حساب</label>
            <select class="form__input" id="activity_status" name="activity_status">
              <option value="active"   <?= ($user['activity_status']??'')==='active'  ?'selected':'' ?>>فعال</option>
              <option value="inactive" <?= ($user['activity_status']??'')==='inactive'?'selected':'' ?>>غیرفعال</option>
              <option value="banned"   <?= ($user['activity_status']??'')==='banned'  ?'selected':'' ?>>مسدود</option>
            </select>
          </div>

        </div>

        <div class="form__group">
          <label class="form__label" for="biography">بیوگرافی</label>
          <textarea class="form__input form__textarea" id="biography" name="biography"
                    rows="4"><?= htmlspecialchars($user['biography'] ?? '') ?></textarea>
        </div>

        <div class="form__actions">
          <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </div>
      </form>
    </div>

    <!-- ═══════ TAB: دسترسی ══════════════════════ -->
    <?php elseif ($tab === 'access'): ?>
    <div class="admin-tab-panel" id="access">

      <!-- ── نقش‌های فعلی ── -->
      <div class="admin-section-block">
        <h2 class="admin-section-block__title">
          <i class="fa-solid fa-user-tag" aria-hidden="true"></i>
          نقش‌های اختصاص‌داده‌شده
          <button class="btn btn-primary btn--sm" onclick="togglePanel('add-role-panel')">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> افزودن نقش
          </button>
          <a href="/admin/users/permissions/rebuild/<?= $user['id'] ?>"
             class="btn btn-ghost btn--sm" title="بازسازی cache مجوزها">
            <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i> بازسازی Cache
          </a>
        </h2>

        <?php if (empty($user['roles'])): ?>
          <p class="admin-empty-text">هیچ نقشی اختصاص داده نشده</p>
        <?php else: ?>
          <table class="admin-table admin-table--compact">
            <thead><tr><th>نقش</th><th>داده‌شده توسط</th><th>تاریخ</th><th>انقضا</th><th>یادداشت</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($user['roles'] as $role): ?>
                <tr>
                  <td>
                    <span class="role-badge"
                          style="--role-color: <?= htmlspecialchars($role['color'] ?? '#6b7280') ?>">
                      <?= htmlspecialchars($role['name']) ?>
                    </span>
                    <?php if ($role['is_system']): ?>
                      <i class="fa-solid fa-lock" title="نقش سیستمی" style="color:#ef4444;font-size:11px"></i>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($role['granted_by_username'] ?? 'سیستم') ?></td>
                  <td><?= $role['granted_at'] ? jdate($role['granted_at']) : '—' ?></td>
                  <td>
                    <?php if ($role['expires_at']): ?>
                      <span class="<?= $role['expires_at'] < date('Y-m-d H:i:s') ? 'text-danger' : '' ?>">
                        <?= jdate($role['expires_at']) ?>
                      </span>
                    <?php else: ?>
                      <span class="text-muted">دائمی</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($role['note'] ?? '') ?></td>
                  <td>
                    <form action="/admin/users/role/revoke/<?= $user['id'] ?>" method="POST"
                          style="display:inline">
                      <input type="hidden" name="role_id" value="<?= $role['role_id'] ?>">
                      <button type="submit" class="btn btn-outline btn--xs btn--danger"
                              onclick="return confirm('نقش «<?= htmlspecialchars($role['name']) ?>» حذف شود؟')"
                              <?= $role['is_system'] ? 'title="نقش سیستمی"' : '' ?>>
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <!-- فرم افزودن نقش -->
        <div id="add-role-panel" class="admin-inline-panel hidden">
          <form action="/admin/users/role/assign/<?= $user['id'] ?>" method="POST" class="form">
            <div class="form-grid form-grid--4">
              <div class="form__group">
                <label class="form__label">نقش *</label>
                <select class="form__input" name="role_id" required>
                  <option value="">انتخاب نقش</option>
                  <?php foreach ($all_roles as $role): ?>
                    <?php $hasRole = in_array($role['name'], array_column($user['roles'], 'name')); ?>
                    <option value="<?= $role['id'] ?>" <?= $hasRole ? 'disabled':'' ?>>
                      <?= htmlspecialchars($role['name']) ?>
                      (<?= $role['user_count'] ?> کاربر)
                      <?= $hasRole ? '✓':'' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form__group">
                <label class="form__label">انقضا (اختیاری)</label>
                <input class="form__input" type="datetime-local" name="expires_at"
                       min="<?= date('Y-m-d\TH:i') ?>">
              </div>
              <div class="form__group">
                <label class="form__label">یادداشت</label>
                <input class="form__input" type="text" name="note" placeholder="دلیل تخصیص...">
              </div>
              <div class="form__group form__group--action">
                <button type="submit" class="btn btn-primary">افزودن نقش</button>
                <button type="button" class="btn btn-ghost" onclick="togglePanel('add-role-panel')">انصراف</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- ── مجوزهای مستقیم ── -->
      <div class="admin-section-block">
        <h2 class="admin-section-block__title">
          <i class="fa-solid fa-key" aria-hidden="true"></i>
          مجوزهای مستقیم
          <span class="admin-badge--info">خارج از نقش</span>
          <button class="btn btn-primary btn--sm" onclick="togglePanel('add-perm-panel')">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> افزودن مجوز
          </button>
        </h2>

        <?php if (empty($user['direct_permissions'])): ?>
          <p class="admin-empty-text">هیچ مجوز مستقیمی تنظیم نشده</p>
        <?php else: ?>
          <table class="admin-table admin-table--compact">
            <thead><tr><th>مجوز</th><th>گروه</th><th>داده‌شده توسط</th><th>انقضا</th><th>یادداشت</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($user['direct_permissions'] as $perm): ?>
                <tr>
                  <td><code class="admin-code"><?= htmlspecialchars($perm['permission_name']) ?></code></td>
                  <td><span class="admin-badge"><?= htmlspecialchars($perm['group_name']) ?></span></td>
                  <td><?= htmlspecialchars($perm['granted_by_username'] ?? 'سیستم') ?></td>
                  <td>
                    <?php if ($perm['expires_at']): ?>
                      <span class="<?= $perm['expires_at'] < date('Y-m-d H:i:s') ? 'text-danger':'' ?>">
                        <?= jdate($perm['expires_at']) ?>
                      </span>
                    <?php else: ?><span class="text-muted">دائمی</span><?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($perm['note'] ?? '') ?></td>
                  <td>
                    <form action="/admin/users/permission/revoke/<?= $user['id'] ?>" method="POST" style="display:inline">
                      <input type="hidden" name="permission_id" value="<?= $perm['permission_id'] ?>">
                      <button type="submit" class="btn btn-outline btn--xs btn--danger"
                              onclick="return confirm('مجوز حذف شود؟')">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <!-- فرم افزودن مجوز -->
        <div id="add-perm-panel" class="admin-inline-panel hidden">
          <form action="/admin/users/permission/grant/<?= $user['id'] ?>" method="POST" class="form">
            <div class="form__group">
              <label class="form__label">مجوز *</label>
              <?php foreach ($perm_groups as $groupName => $perms): ?>
                <optgroup label="<?= htmlspecialchars($groupName) ?>">
                  <?php foreach ($perms as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </div>
            <!-- این بخش رو باید با یه select درست replace کنی -->
            <select class="form__input" name="permission_id" required>
              <option value="">انتخاب مجوز</option>
              <?php foreach ($perm_groups as $groupName => $perms): ?>
                <optgroup label="<?= htmlspecialchars($groupName) ?>">
                  <?php foreach ($perms as $p): ?>
                    <?php $hasIt = isset($user['all_permissions'][$p['name']]); ?>
                    <option value="<?= $p['id'] ?>" <?= $hasIt ? 'disabled':'' ?>>
                      <?= htmlspecialchars($p['name']) ?>
                      <?= $hasIt ? ('('. $user['all_permissions'][$p['name']] .')') : '' ?>
                    </option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
            <div class="form-grid form-grid--3" style="margin-top:12px">
              <div class="form__group">
                <label class="form__label">انقضا</label>
                <input class="form__input" type="datetime-local" name="expires_at">
              </div>
              <div class="form__group">
                <label class="form__label">یادداشت</label>
                <input class="form__input" type="text" name="note" placeholder="دلیل...">
              </div>
              <div class="form__group form__group--action">
                <button type="submit" class="btn btn-primary">افزودن مجوز</button>
                <button type="button" class="btn btn-ghost" onclick="togglePanel('add-perm-panel')">انصراف</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- ── همه مجوزهای فعال ── -->
      <div class="admin-section-block">
        <h2 class="admin-section-block__title">
          <i class="fa-solid fa-list-check" aria-hidden="true"></i>
          همه مجوزهای فعال
          <span class="admin-badge"><?= count($user['all_permissions']) ?></span>
        </h2>
        <div class="admin-perm-grid">
          <?php foreach ($user['all_permissions'] as $permName => $source): ?>
            <span class="admin-perm-chip admin-perm-chip--<?= $source ?>">
              <i class="fa-solid <?= $source === 'direct' ? 'fa-key' : 'fa-user-tag' ?>"
                 aria-hidden="true" title="منبع: <?= $source ?>"></i>
              <?= htmlspecialchars($permName) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- ═══════ TAB: نشست‌ها ══════════════════════ -->
    <?php elseif ($tab === 'sessions'): ?>
    <div class="admin-tab-panel">
      <h2 class="admin-section-block__title">نشست‌های فعال</h2>
      <?php if (empty($user['sessions'])): ?>
        <p class="admin-empty-text">هیچ نشست فعالی وجود ندارد</p>
      <?php else: ?>
        <table class="admin-table">
          <thead><tr><th>دستگاه</th><th>IP</th><th>شروع</th><th>انقضا</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($user['sessions'] as $sess): ?>
              <tr>
                <td class="admin-device-cell">
                  <?php
                  $ua = $sess['device'] ?? '';
                  $icon = str_contains($ua, 'Mobile') ? 'fa-mobile' :
                          (str_contains($ua, 'Tablet') ? 'fa-tablet' : 'fa-desktop');
                  ?>
                  <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i>
                  <span title="<?= htmlspecialchars($ua) ?>">
                    <?= mb_substr(htmlspecialchars($ua), 0, 50) ?>...
                  </span>
                </td>
                <td><code><?= htmlspecialchars($sess['ip'] ?? '—') ?></code></td>
                <td><?= $sess['created_at'] ? jdate($sess['created_at'], 'Y/m/d') : '—' ?></td>
                <td><?= $sess['expires_at'] ? jdate($sess['expires_at'], 'Y/m/d') : '—' ?></td>
                <td>
                  <a href="/admin/users/sessions/revoke/<?= $sess['id'] ?>"
                     class="btn btn-outline btn--xs btn--danger"
                     onclick="return confirm('این نشست لغو شود؟')">
                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> لغو
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- ═══════ TAB: امنیت ════════════════════════ -->
    <?php elseif ($tab === 'security'): ?>
    <div class="admin-tab-panel">
      <div class="admin-section-block">
        <h2 class="admin-section-block__title">
          <i class="fa-solid fa-key" aria-hidden="true"></i>
          بازنشانی رمز عبور
        </h2>
        <form action="/admin/users/reset-password/<?= $user['id'] ?>" method="POST" class="form">
          <div class="form-grid form-grid--2">
            <div class="form__group">
              <label class="form__label" for="new_password">رمز عبور جدید *</label>
              <input class="form__input" type="password" id="new_password" name="new_password"
                     minlength="8" placeholder="حداقل ۸ کاراکتر">
            </div>
          </div>
          <div class="alert alert--warning" role="alert">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            تغییر رمز عبور باعث خروج کاربر از همه دستگاه‌ها می‌شود
          </div>
          <button type="submit" class="btn btn-primary"
                  onclick="return confirm('رمز عبور بازنشانی شود؟')">
            بازنشانی رمز عبور
          </button>
        </form>
      </div>

      <div class="admin-section-block admin-section-block--danger">
        <h2 class="admin-section-block__title admin-section-block__title--danger">
          <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
          منطقه خطر
        </h2>
        <?php if ($user['id'] !== getUserId()): ?>
          <a href="/admin/users/delete/<?= $user['id'] ?>"
             class="btn btn-outline btn--danger"
             onclick="return confirm('کاربر «<?= htmlspecialchars($user['username'] ?? '') ?>» حذف شود؟ این عمل قابل بازگشت نیست.')">
            <i class="fa-solid fa-trash" aria-hidden="true"></i>
            حذف حساب کاربری
          </a>
        <?php else: ?>
          <p class="admin-empty-text">نمی‌توانید حساب خودتان را حذف کنید</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- ═══════ TAB: لاگ تغییرات ══════════════════ -->
    <?php elseif ($tab === 'audit'): ?>
    <div class="admin-tab-panel">
      <h2 class="admin-section-block__title">تاریخچه تغییرات</h2>
      <?php if (empty($user['audit_logs'])): ?>
        <p class="admin-empty-text">هیچ تغییری ثبت نشده</p>
      <?php else: ?>
        <div class="admin-audit-list">
          <?php foreach ($user['audit_logs'] as $log): ?>
            <div class="admin-audit-item">
              <div class="admin-audit-item__icon">
                <i class="fa-solid <?= match(true) {
                  str_contains($log['action'], 'role')       => 'fa-user-tag',
                  str_contains($log['action'], 'permission') => 'fa-key',
                  str_contains($log['action'], 'status')     => 'fa-toggle-on',
                  str_contains($log['action'], 'delete')     => 'fa-trash',
                  str_contains($log['action'], 'password')   => 'fa-lock',
                  default                                    => 'fa-pen',
                } ?>" aria-hidden="true"></i>
              </div>
              <div class="admin-audit-item__body">
                <div class="admin-audit-item__header">
                  <code class="admin-code"><?= htmlspecialchars($log['action']) ?></code>
                  <span class="admin-audit-item__by">
                    توسط @<?= htmlspecialchars($log['admin_username'] ?? 'سیستم') ?>
                  </span>
                  <span class="admin-audit-item__time">
                    <?= $log['created_at'] ? jdate($log['created_at'], 'Y/m/d H:i') : '' ?>
                  </span>
                  <span class="admin-audit-item__ip">
                    <code><?= htmlspecialchars($log['ip'] ?? '') ?></code>
                  </span>
                </div>
                <?php if ($log['new_data'] && $log['new_data'] !== '{}'): ?>
                  <details class="admin-audit-item__diff">
                    <summary>جزئیات تغییر</summary>
                    <div class="admin-audit-item__diff-body">
                      <?php if ($log['old_data'] && $log['old_data'] !== '{}'): ?>
                        <div class="admin-diff admin-diff--old">
                          <strong>قبل:</strong>
                          <pre><?= htmlspecialchars(
                            json_encode(json_decode($log['old_data'], true), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
                          ) ?></pre>
                        </div>
                      <?php endif; ?>
                      <div class="admin-diff admin-diff--new">
                        <strong>بعد:</strong>
                        <pre><?= htmlspecialchars(
                          json_encode(json_decode($log['new_data'], true), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
                        ) ?></pre>
                      </div>
                    </div>
                  </details>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php endif; ?>

  </div>
</section>

<script>
function togglePanel(id) {
  const panel = document.getElementById(id);
  panel?.classList.toggle('hidden');
}
</script>