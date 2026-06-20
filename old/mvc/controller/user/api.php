<?php

/**
 * mvc/controller/user/api.php
 *
 * REST API endpoints — همه JSON برمی‌گردونن
 *
 * GET    /api/user/list           → listGet()
 * GET    /api/user/show/{id}      → showGet($id)
 * POST   /api/user/store          → storePost()
 * POST   /api/user/update/{id}    → updatePost($id)
 * POST   /api/user/delete/{id}    → deletePost($id)
 * POST   /api/user/ban/{id}       → banPost($id)
 * POST   /api/user/approve/{id}   → approvePost($id)
 */
trait UserApiTrait {

  // ── GET: لیست ───────────────────────────────────

  public function listGet(): void {
    $this->requireAuth();
    $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();

    $result = $model->paginate(
      page:    (int) $this->get('page', 1),
      perPage: (int) $this->get('per_page', 20),
      conditions: [
        'status' => $this->get('status'),
        'gender' => $this->get('gender'),
        'search' => $this->get('search'),
      ]
    );

    $this->success($result);
  }


  // ── GET: یه کاربر ───────────────────────────────

  public function showGet(int $id): void {
    $this->requireAuth();

    // کاربر عادی فقط پروفایل خودش رو می‌بینه
    if (!isAdmin() && getUserId() !== $id) {
      $this->error('Forbidden', 403);
    }

    $user = (new UserModel())->findById($id);

    if (!$user) {
      $this->error('کاربر یافت نشد', 404);
    }

    // رمز عبور رو از response حذف کن
    unset($user['password']);

    $this->success($user);
  }


  // ── POST: ثبت کاربر جدید ────────────────────────

  public function storePost(): void {
    $this->requireAuth();
    $this->requireRole(['admin', 'superadmin']);

    $body  = $this->body();
    $model = new UserModel();

    // validation
    $errors = [];

    if (empty($body['email']) || !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'ایمیل معتبر الزامی است';
    } elseif ($model->emailExists($body['email'])) {
      $errors['email'] = 'این ایمیل قبلاً ثبت شده است';
    }

    if (empty($body['username']) || strlen($body['username']) < 3) {
      $errors['username'] = 'نام کاربری باید حداقل ۳ کاراکتر باشد';
    } elseif ($model->usernameExists($body['username'])) {
      $errors['username'] = 'این نام کاربری قبلاً انتخاب شده است';
    }

    if (empty($body['password']) || strlen($body['password']) < 8) {
      $errors['password'] = 'رمز عبور باید حداقل ۸ کاراکتر باشد';
    }

    if (!empty($errors)) {
      $this->error('اطلاعات وارد شده معتبر نیست', 422, $errors);
    }

    $newId = $model->create([
      'email'         => $body['email'],
      'username'      => $body['username'],
      'password'      => encryptPassword($body['password']),
      'phone'         => $body['phone']          ?? null,
      'national_code' => $body['national_code']  ?? null,
      'gender'        => $body['gender']         ?? null,
      'birthday'      => $body['birthday']       ?? null,
      'created_by'    => getUserId(),
    ]);

    $user = $model->findById($newId);
    unset($user['password']);

    $this->success($user, 'کاربر با موفقیت ثبت شد');
  }


  // ── POST: ویرایش ────────────────────────────────

  public function updatePost(int $id): void {
    $this->requireAuth();

    if (!isAdmin() && getUserId() !== $id) {
      $this->error('Forbidden', 403);
    }

    $body  = $this->body();
    $model = new UserModel();

    if (!$model->findById($id)) {
      $this->error('کاربر یافت نشد', 404);
    }

    $errors = [];

    if (!empty($body['email'])) {
      if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'فرمت ایمیل صحیح نیست';
      } elseif ($model->emailExists($body['email'], $id)) {
        $errors['email'] = 'این ایمیل قبلاً ثبت شده است';
      }
    }

    if (!empty($body['username']) && $model->usernameExists($body['username'], $id)) {
      $errors['username'] = 'این نام کاربری قبلاً انتخاب شده است';
    }

    if (!empty($body['password']) && strlen($body['password']) < 8) {
      $errors['password'] = 'رمز عبور باید حداقل ۸ کاراکتر باشد';
    }

    if (!empty($errors)) {
      $this->error('اطلاعات وارد شده معتبر نیست', 422, $errors);
    }

    $model->update($id, [
      'email'         => $body['email']         ?? null,
      'username'      => $body['username']       ?? null,
      'phone'         => $body['phone']          ?? null,
      'national_code' => $body['national_code']  ?? null,
      'gender'        => $body['gender']         ?? null,
      'birthday'      => $body['birthday']       ?? null,
    ]);

    if (!empty($body['password'])) {
      $model->updatePassword($id, encryptPassword($body['password']));
    }

    $user = $model->findById($id);
    unset($user['password']);

    $this->success($user, 'اطلاعات با موفقیت بروزرسانی شد');
  }


  // ── POST: حذف ───────────────────────────────────

  public function deletePost(int $id): void {
    $this->requireAuth();
    $this->requireRole(['superadmin']);

    $model = new UserModel();

    if (!$model->findById($id)) {
      $this->error('کاربر یافت نشد', 404);
    }

    $model->delete($id);
    $this->success(null, 'کاربر با موفقیت حذف شد');
  }


  // ── POST: ban ───────────────────────────────────

  public function banPost(int $id): void {
    $this->requireAuth();
    $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();

    if (!$model->findById($id)) {
      $this->error('کاربر یافت نشد', 404);
    }

    $model->updateStatus($id, 'banned');
    $this->success(null, 'کاربر مسدود شد');
  }


  // ── POST: approve ────────────────────────────────

  public function approvePost(int $id): void {
    $this->requireAuth();
    $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();

    if (!$model->findById($id)) {
      $this->error('کاربر یافت نشد', 404);
    }

    $model->approve($id, getUserId());
    $this->success(null, 'کاربر تأیید شد');
  }

}
