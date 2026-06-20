<?php

/**
 * mvc/controller/account/api.php
 *
 * REST API برای اپ موبایل
 * همه responses: JSON
 */
trait AccountApiTrait {

  // ── POST /api/account/register ───────────────────

  public function registerPost(): void {
    $body   = $this->body();
    $method = $body['method'] ?? 'email';
    $auth   = new AuthModel();
    $errors = [];

    if ($method === 'email') {
      $email    = trim($body['email'] ?? '');
      $password = $body['password'] ?? '';

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'ایمیل معتبر نیست';
      elseif ($auth->emailExists($email))              $errors['email'] = 'این ایمیل قبلاً ثبت شده است';
      if (strlen($password) < 8)                       $errors['password'] = 'رمز عبور حداقل ۸ کاراکتر';

      if (!empty($errors)) { $this->error('اطلاعات نامعتبر', 422, $errors); }

      $userId = $auth->register([
        'email'    => $email,
        'password' => encryptPassword($password),
        'fullname' => $body['fullname'] ?? '',
      ]);

      // ارسال OTP به ایمیل
      $otp  = new OtpModel();
      $code = $otp->generate($email, 'email', 'register');
      Mailer::sendOtp($email, $code, 'register');

      $this->success(['user_id' => $userId], 'کد تأیید به ایمیل ارسال شد');

    } else {
      $phone = trim($body['phone'] ?? '');

      if (!preg_match('/^09[0-9]{9}$/', $phone)) $errors['phone'] = 'شماره موبایل معتبر نیست';
      elseif ($auth->phoneExists($phone))          $errors['phone'] = 'این شماره قبلاً ثبت شده است';

      if (!empty($errors)) { $this->error('اطلاعات نامعتبر', 422, $errors); }

      $userId = $auth->register(['phone' => $phone, 'fullname' => $body['fullname'] ?? '']);

      $otp  = new OtpModel();
      $code = $otp->generate($phone, 'phone', 'register');
      Sms::sendOtp($phone, $code, 'register');

      $this->success(['user_id' => $userId], 'کد تأیید به موبایل ارسال شد');
    }
  }


  // ── POST /api/account/login ──────────────────────

  public function loginPost(): void {
    $body   = $this->body();
    $method = $body['method'] ?? 'email';
    $auth   = new AuthModel();

    if ($method === 'email') {
      $email    = trim($body['email'] ?? '');
      $password = $body['password'] ?? '';
      $user     = $auth->findByEmail($email);

      if (!$user || !verifyPassword($password, $user['password'])) {
        $this->error('ایمیل یا رمز عبور اشتباه است', 401);
      }

      if ($user['activity_status'] === 'banned')   { $this->error('حساب مسدود است', 403); }
      if ($user['activity_status'] === 'inactive')  { $this->error('حساب تأیید نشده است', 403); }

    } else {
      $phone = trim($body['phone'] ?? '');
      $user  = $auth->findByPhone($phone);

      if (!$user) {
        // ثبت خودکار برای ورود با موبایل
        $userId = $auth->register(['phone' => $phone]);
        $user   = $auth->findById($userId);
      }

      // ارسال OTP
      try {
        $otp  = new OtpModel();
        $code = $otp->generate($phone, 'phone', 'login');
        Sms::sendOtp($phone, $code, 'login');
        $this->success(['requires_otp' => true, 'phone' => $phone], 'کد OTP ارسال شد');
      } catch (RuntimeException) {
        $this->error('تعداد درخواست‌ها بیش از حد مجاز است', 429);
      }
    }

    // صدور token
    $sessions = new SessionModel();
    $token    = $sessions->createSession($user['id'], $body['device'] ?? 'mobile');
    $auth->updateLastVisit($user['id']);

    unset($user['password']);
    $this->success(['token' => $token, 'user' => $user], 'ورود موفق');
  }


  // ── POST /api/account/verify-otp ────────────────

  public function verifyOtpPost(): void {
    $body    = $this->body();
    $target  = trim($body['target'] ?? '');  // ایمیل یا موبایل
    $code    = trim($body['code'] ?? '');
    $purpose = $body['purpose'] ?? 'register';
    $auth    = new AuthModel();

    $otp = new OtpModel();
    if (!$otp->verify($target, $code, $purpose)) {
      $this->error('کد وارد شده اشتباه یا منقضی است', 422);
    }

    // فعال‌سازی
    if (filter_var($target, FILTER_VALIDATE_EMAIL)) {
      $auth->activateByEmail($target);
      $user = $auth->findByEmail($target);
    } else {
      $auth->activateByPhone($target);
      $user = $auth->findByPhone($target);
    }

    if (!$user) { $this->error('کاربر یافت نشد', 404); }

    // اگه ورود بود → صدور token
    if ($purpose === 'login' || $purpose === 'register') {
      $token = (new SessionModel())->createSession($user['id'], $body['device'] ?? 'mobile');
      unset($user['password']);
      $this->success(['token' => $token, 'user' => $user], 'تأیید موفق');
    }

    $this->success(null, 'تأیید موفق');
  }


  // ── POST /api/account/resend-otp ────────────────

  public function resendOtpPost(): void {
    $body    = $this->body();
    $target  = trim($body['target'] ?? '');
    $purpose = $body['purpose'] ?? 'register';
    $isEmail = filter_var($target, FILTER_VALIDATE_EMAIL);

    try {
      $otp  = new OtpModel();
      $type = $isEmail ? 'email' : 'phone';
      $code = $otp->generate($target, $type, $purpose);

      if ($isEmail) Mailer::sendOtp($target, $code, $purpose);
      else          Sms::sendOtp($target, $code, $purpose);

      $this->success(null, 'کد مجدداً ارسال شد');
    } catch (RuntimeException) {
      $this->error('تعداد درخواست‌ها بیش از حد مجاز است', 429);
    }
  }


  // ── POST /api/account/logout ─────────────────────

  public function logoutPost(): void {
    $this->requireAuth();
    $token = $this->getAuthToken();
    if ($token) (new SessionModel())->deleteByToken($token);
    $this->success(null, 'خروج موفق');
  }


  // ── POST /api/account/forgot-password ───────────

  public function forgotPasswordPost(): void {
    $body       = $this->body();
    $identifier = trim($body['identifier'] ?? '');
    $auth       = new AuthModel();
    $user       = $auth->findByEmailOrPhone($identifier);

    // anti-enumeration: همیشه موفق
    if (!$user) { $this->success(null, 'در صورت وجود حساب، کد ارسال شد'); }

    $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

    try {
      $otp  = new OtpModel();
      $type = $isEmail ? 'email' : 'phone';
      $code = $otp->generate($identifier, $type, 'reset');

      if ($isEmail) Mailer::sendOtp($identifier, $code, 'reset');
      else          Sms::sendOtp($identifier, $code, 'reset');
    } catch (RuntimeException) {
      $this->error('تعداد درخواست‌ها بیش از حد مجاز است', 429);
    }

    $this->success(null, 'کد بازیابی ارسال شد');
  }


  // ── POST /api/account/reset-password ────────────

  public function resetPasswordPost(): void {
    $body       = $this->body();
    $identifier = trim($body['identifier'] ?? '');
    $code       = trim($body['code'] ?? '');
    $password   = $body['password'] ?? '';

    if (strlen($password) < 8) {
      $this->error('رمز عبور حداقل ۸ کاراکتر باشد', 422);
    }

    $otp = new OtpModel();
    if (!$otp->verify($identifier, $code, 'reset')) {
      $this->error('کد نامعتبر یا منقضی است', 422);
    }

    $auth = new AuthModel();
    $user = $auth->findByEmailOrPhone($identifier);
    if (!$user) { $this->error('کاربر یافت نشد', 404); }

    $auth->updatePassword($user['id'], encryptPassword($password));
    (new SessionModel())->deleteAllByUser($user['id']);

    $this->success(null, 'رمز عبور با موفقیت تغییر کرد');
  }


  // ── GET /api/account/me ──────────────────────────

  public function meGet(): void {
    $this->requireAuth();
    $user = (new AuthModel())->findById(getUserId());
    if (!$user) { $this->error('کاربر یافت نشد', 404); }
    unset($user['password']);
    $this->success($user);
  }


  // ── Token Helper ─────────────────────────────────

  private function getAuthToken(): ?string {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (str_starts_with($header, 'Bearer ')) {
      return substr($header, 7);
    }
    return session_get('session_token');
  }

}
