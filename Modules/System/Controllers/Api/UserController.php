<?php
namespace Modules\System\Controllers\Api;

use Core\database\DB;
use Core\http\ResponseFactory;
use Core\translation\TranslationService;
use Core\validation\ValidationException;
use Modules\System\Requests\UserStoreRequest;
use Modules\System\Services\MobileAuthTokenService;
use Modules\System\Services\RegistrationOtpService;
use Modules\System\Services\UserNotificationService;
use Modules\System\Services\UserService;

class UserController {

    public function __construct(
        protected UserService $service,
        protected RegistrationOtpService $registrationOtp,
        protected UserNotificationService $notifications,
        protected MobileAuthTokenService $tokens
    ) {
    }

    public function login() {
        $identifier = trim((string)($_POST['identifier'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if ($identifier === '' || $password === '') return $this->error('نام کاربری، ایمیل یا شماره موبایل و رمز عبور الزامی است.', 422);
        $user = $this->service->attempt($identifier, $password);
        if (!$user) return $this->error('نام کاربری، ایمیل، شماره موبایل یا رمز عبور اشتباه است.', 401);
        $this->recordLogin((int)$user['user_id']);
        return $this->authenticated($user, 'ورود با موفقیت انجام شد.');
    }

    public function sendRegistrationOtp() {
        try {
            $data = $this->registrationData();
            $result = $this->registrationOtp->send($data['register_method'], (string)$data[$data['register_method']], $this->otpData($data));
            return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503));
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success'=>false, 'message'=>'اطلاعات فرم را بررسی کنید.', 'errors'=>$e->getErrors()], 422);
        }
    }

    public function register() {
        try {
            $data = $this->registrationData();
            $verification = $this->registrationOtp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) return $this->error($verification['message'], 422);
            session()->put('suppress_database_notifications', true);
            $userId = $this->service->register($data);
            if (!$userId) {
                session()->forget('suppress_database_notifications');
                return $this->error('ثبت‌نام ناموفق بود.', 422);
            }
            $this->registrationOtp->clear();
            $now = date('Y-m-d H:i:s');
            $verified = ['status'=>'approved','type'=>'human','approved_at'=>$now,'approved_by'=>$userId,'updated_by'=>$userId];
            if ($data['register_method'] === 'phone') $verified['phone_verified_at'] = $now;
            DB::table('users')->where('user_id', $userId)->update($verified);
            $this->recordLogin($userId);
            $contact = (string)$data[$data['register_method']];
            $this->sendRegistrationNotifications($userId, $data['username'], $data['register_method'], $contact);
            session()->forget('suppress_database_notifications');
            $user = DB::table('users')->where('user_id', $userId)->first();
            return $this->authenticated($user, 'ثبت‌نام با موفقیت انجام شد.', 201);
        } catch (ValidationException $e) {
            session()->forget('suppress_database_notifications');
            return ResponseFactory::json(['success'=>false, 'message'=>'اطلاعات فرم را بررسی کنید.', 'errors'=>$e->getErrors()], 422);
        } catch (\Throwable $e) {
            session()->forget('suppress_database_notifications');
            return $this->error('ثبت‌نام ناموفق بود. لطفاً دوباره تلاش کنید.', 500);
        }
    }

    public function me() {
        $user = $this->tokens->userFromRequest();
        if (!$user) return $this->error('نشست شما معتبر نیست. دوباره وارد شوید.', 401);
        return ResponseFactory::json(['success'=>true, 'user'=>$this->profile($user)]);
    }

    public function logout() {
        return ResponseFactory::json(['success'=>true, 'message'=>'خروج با موفقیت انجام شد.']);
    }

    public function contact() {
        $message = trim((string)($_POST['message'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        if ($message === '') return $this->error('متن پیام الزامی است.', 422);
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->error('ایمیل معتبر وارد کنید.', 422);
        return ResponseFactory::json(['success'=>true, 'message'=>'پیام شما ارسال شد. در اولین فرصت پاسخ می‌دهیم.']);
    }

    private function registrationData(): array {
        $data = (new UserStoreRequest($_POST))->validated();
        $data['invite_code'] = trim((string)($_POST['invite_code'] ?? ''));
        $data['full_name'] = trim((string)($_POST['full_name'] ?? ''));
        $data['locale'] = in_array($_POST['locale'] ?? '', ['fa','en'], true) ? $_POST['locale'] : 'fa';
        $method = (string)($data['register_method'] ?? '');
        if ($method === 'email') {
            $data['email'] = strtolower(trim((string)($data['email'] ?? '')));
            $data['phone'] = null;
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new ValidationException(['email'=>'ایمیل معتبر وارد کنید.']);
        } else {
            $data['phone'] = preg_replace('/\D+/', '', (string)($data['phone'] ?? ''));
            $data['email'] = null;
            if (!preg_match('/^09\d{9}$/', $data['phone'])) throw new ValidationException(['phone'=>'شماره موبایل معتبر وارد کنید.']);
        }
        if (empty($_POST['terms'])) throw new ValidationException(['terms'=>'پذیرش قوانین الزامی است.']);
        return $data;
    }

    private function otpData(array $data): array {
        unset($data['otp'], $data['password2'], $data['terms']);
        ksort($data);
        return $data;
    }

    private function authenticated(array $user, string $message, int $status = 200) {
        return ResponseFactory::json(['success'=>true, 'message'=>$message, 'token'=>$this->tokens->issue($user), 'user'=>$this->profile($user)], $status);
    }

    private function profile(array $user): array {
        $id = (int)$user['user_id'];
        $locale = in_array($user['locale'] ?? '', ['fa','en'], true) ? $user['locale'] : 'fa';
        $name = TranslationService::manager()->get('users', $id, 'full_name', $locale)
            ?: TranslationService::manager()->get('users', $id, 'full_name', 'fa')
            ?: (string)$user['username'];
        return ['id'=>$id,'username'=>$user['username'],'full_name'=>$name,'email'=>$user['email'] ?: null,'phone'=>$user['phone'] ?: null];
    }

    private function recordLogin(int $userId): void {
        DB::table('users')->where('user_id', $userId)->update(['last_login_at'=>date('Y-m-d H:i:s'),'last_login_ip'=>substr((string)($_SERVER['REMOTE_ADDR'] ?? ''),0,45)]);
    }

    private function sendRegistrationNotifications(int $userId, string $username, string $method, string $contact): void {
        $at = date('Y-m-d H:i:s');
        $label = $method === 'phone' ? 'شماره تلفن' : 'ایمیل';
        $this->notifications->send($userId, 'ثبت‌نام موفق', "ثبت نام شما در تاریخ {$at} با موفقیت صورت گرفت.", 'users', $userId, $userId, 'Registration successful', "Your registration was completed successfully on {$at}.");
        $this->notifications->send(1, 'ثبت‌نام کاربر جدید', "کاربری با آی‌دی {$userId} با نام کاربری {$username} و {$label} {$contact} در سایت ثبت‌نام کرد.", 'users', $userId, $userId, 'New user registration', "User ID {$userId} registered through the mobile app with {$method} {$contact}.");
        foreach ([['ایجاد اکانت مالی کاربر جدید','financial_system_accounts'],['ایجاد کد دعوت کاربر جدید','user_referrals'],['ایجاد نقش کاربر جدید','user_roles']] as [$title,$entity]) {
            $this->notifications->send(1, $title, "برای کاربر جدید با آی‌دی {$userId} ایجاد شد.", $entity, $userId, $userId);
        }
    }

    private function error(string $message, int $status) {
        return ResponseFactory::json(['success'=>false, 'message'=>$message], $status);
    }
}
