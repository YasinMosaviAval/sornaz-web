<?php

/**
 * mvc/controller/user/pages.php
 *
 * متدهایی که صفحات HTML رو رندر می‌کنن
 */

trait UserPagesTrait {

  // ── READ: لیست کاربران ───────────────────────────

  public function list(): void {
    // $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();

    $page    = (int) $this->get('page', 1);
    $filters = [
      'status' => $this->get('status'),
      'gender' => $this->get('gender'),
      'search' => $this->get('search'),
    ];

    $result = $model->paginate($page, 20, $filters);

    $this->view('/user/list', 'لیست کاربران', [
      'users'   => $result['data'],
      'total'   => $result['total'],
      'page'    => $result['page'],
      'pages'   => $result['pages'],
      'perPage' => $result['perPage'],
      'filters' => $filters,
    ]);
  }


  // ── READ: نمایش یه کاربر ────────────────────────

  public function show(int $id): void {
    // $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();
    $user  = $model->findById($id);

    if (!$user) {
      http_response_code(404);
      $this->view('/error/404', 'کاربر یافت نشد');
      return;
    }

    $this->view('/user/show', 'پروفایل کاربر', ['user' => $user]);
  }


  // ── CREATE: فرم ثبت کاربر جدید ──────────────────

  public function create(): void {
    // $this->requireRole(['admin', 'superadmin']);
    $this->view('/user/create', 'افزودن کاربر جدید');
  }


  // ── CREATE: پردازش فرم ──────────────────────────

  public function store(): void {
    // $this->requireRole(['admin', 'superadmin']);

    $model  = new UserModel();
    $errors = $this->validateUserInput();

    if (!empty($errors)) {
      $this->view('/user/create', 'افزودن کاربر', ['errors' => $errors, 'old' => $_POST]);
      return;
    }

    // بررسی تکراری نبودن
    if ($model->emailExists($this->post('email'))) {
      $this->view('/user/create', 'افزودن کاربر', [
        'errors' => ['email' => 'این ایمیل قبلاً ثبت شده است'],
        'old'    => $_POST,
      ]);
      return;
    }

    $newId = $model->create([
      'email'         => $this->post('email'),
      'username'      => $this->post('username'),
      'password'      => encryptPassword($this->post('password')),
      'phone'         => $this->post('phone'),
      'national_code' => $this->post('national_code'),
      'gender'        => $this->post('gender'),
      'birthday'      => $this->post('birthday') ?: null,
      'created_by'    => getUserId(),
    ]);

    $this->redirect("/user/show/$newId");
  }


  // ── UPDATE: فرم ویرایش ──────────────────────────

  public function edit(int $id): void {
    // $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();
    $user  = $model->findById($id);

    if (!$user) {
      http_response_code(404);
      $this->view('/error/404', 'کاربر یافت نشد');
      return;
    }

    $this->view('/user/edit', 'ویرایش کاربر', ['user' => $user]);
  }


  // ── UPDATE: پردازش فرم ──────────────────────────

  public function update(int $id): void {
    // $this->requireRole(['admin', 'superadmin']);

    $model = new UserModel();
    $user  = $model->findById($id);

    if (!$user) {
      http_response_code(404);
      $this->view('/error/404', 'کاربر یافت نشد');
      return;
    }

    $errors = $this->validateUserInput(excludeId: $id);

    if (!empty($errors)) {
      $this->view('/user/edit', 'ویرایش کاربر', ['user' => $user, 'errors' => $errors]);
      return;
    }

    $model->update($id, [
      'email'         => $this->post('email'),
      'username'      => $this->post('username'),
      'phone'         => $this->post('phone'),
      'national_code' => $this->post('national_code'),
      'gender'        => $this->post('gender'),
      'birthday'      => $this->post('birthday') ?: null,
    ]);

    // اگه رمز عبور جدید وارد شده
    if ($this->post('password')) {
      $model->updatePassword($id, encryptPassword($this->post('password')));
    }

    $this->redirect("/user/show/$id");
  }


  // ── DELETE: حذف کاربر ───────────────────────────

  public function delete(int $id): void {
    // $this->requireRole(['superadmin']);

    $model = new UserModel();

    if (!$model->findById($id)) {
      http_response_code(404);
      $this->view('/error/404', 'کاربر یافت نشد');
      return;
    }

    $model->delete($id); // soft delete
    $this->redirect('/user/list');
  }


  // ── Validation ───────────────────────────────────

  private function validateUserInput(int $excludeId = 0): array {
    $errors = [];
    $model  = new UserModel();

    $email    = trim($this->post('email', ''));
    $username = trim($this->post('username', ''));
    $password = $this->post('password', '');

    if (empty($email)) {
      $errors['email'] = 'ایمیل الزامی است';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'فرمت ایمیل صحیح نیست';
    } elseif ($model->emailExists($email, $excludeId)) {
      $errors['email'] = 'این ایمیل قبلاً ثبت شده است';
    }

    if (empty($username)) {
      $errors['username'] = 'نام کاربری الزامی است';
    } elseif (strlen($username) < 3) {
      $errors['username'] = 'نام کاربری باید حداقل ۳ کاراکتر باشد';
    } elseif ($model->usernameExists($username, $excludeId)) {
      $errors['username'] = 'این نام کاربری قبلاً انتخاب شده است';
    }

    // رمز عبور فقط برای ثبت جدید اجباریه
    if ($excludeId === 0 && empty($password)) {
      $errors['password'] = 'رمز عبور الزامی است';
    } elseif (!empty($password) && strlen($password) < 8) {
      $errors['password'] = 'رمز عبور باید حداقل ۸ کاراکتر باشد';
    }

    $phone = $this->post('phone');
    if ($phone && $model->phoneExists($phone, $excludeId)) {
      $errors['phone'] = 'این شماره موبایل قبلاً ثبت شده است';
    }

    $gender = $this->post('gender');
    if ($gender && !in_array($gender, ['male', 'female', 'other'])) {
      $errors['gender'] = 'جنسیت معتبر نیست';
    }

    return $errors;
  }

}
