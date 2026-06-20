<?php

/**
 * mvc/controller/user/queries.php
 *
 * عملیات‌هایی که نیاز به رندر view ندارن
 * معمولاً POST هستن و بعدش redirect می‌کنن
 */
trait UserQueriesTrait {


  // ── تغییر وضعیت ─────────────────────────────────

  public function ban(int $id): void {
    $this->requireRole(['admin', 'superadmin']);
    (new UserModel())->updateStatus($id, 'banned');
    $this->redirect("/user/show/$id");
  }

  public function activate(int $id): void {
    $this->requireRole(['admin', 'superadmin']);
    (new UserModel())->updateStatus($id, 'active');
    $this->redirect("/user/show/$id");
  }

  public function deactivate(int $id): void {
    $this->requireRole(['admin', 'superadmin']);
    (new UserModel())->updateStatus($id, 'inactive');
    $this->redirect("/user/show/$id");
  }


  // ── تأیید کاربر ─────────────────────────────────

  public function approve(int $id): void {
    $this->requireRole(['admin', 'superadmin']);
    (new UserModel())->approve($id, getUserId());
    $this->redirect("/user/show/$id");
  }


  // ── بازگردانی کاربر حذف‌شده ─────────────────────

  public function restore(int $id): void {
    $this->requireRole(['superadmin']);
    (new UserModel())->restore($id);
    $this->redirect("/user/show/$id");
  }

}
