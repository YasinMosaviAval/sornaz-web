<?php
namespace Modules\System\Controllers\Web;

use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\System\Services\UserService;
use Modules\System\Requests\UserStoreRequest;
use Modules\System\Services\RegistrationOtpService;
use Modules\System\Services\PasswordResetOtpService;
use Core\database\DB;
use Modules\System\Services\UserNotificationService;

class UserController {

    public function __construct(
        protected UserService $service,
        protected RegistrationOtpService $registrationOtp,
        protected PasswordResetOtpService $passwordResetOtp,
        protected UserNotificationService $notifications
    ) {
    }

    public function directory() {
        return ResponseFactory::view('Analytics::users', ['users' => $this->service->publicDirectory()])
            ->layout('main')
            ->title('سُرناز | کاربران');
    }


    public function login() {
        $identifier = trim($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';
        if (!$identifier || !$password) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => trans('auth.error.credentials_required', 'نام‌کاربری، ایمیل یا شماره موبایل و رمز عبور الزامی است.')]);
        }
        $user = $this->service->attempt($identifier, $password);
        if (!$user) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => trans('auth.error.credentials_invalid', 'نام‌کاربری، ایمیل، شماره موبایل یا رمز عبور اشتباه است.')]);
        }
        auth()->login((int)$user['user_id'], !empty($_POST['remember']));
        $this->recordLogin((int)$user['user_id']);
        session()->flash('auth_success', trans('auth.success.login', 'ورود به حساب کاربری با موفقیت انجام شد.'));
        return redirect('/page/home');
    }


    public function logout() {
        auth()->logout();

        return redirect('/page/home');
    }


    public function store() {
        try {
            $request = new UserStoreRequest($_POST);
            $data = $request->validated();
            $data['invite_code'] = trim((string)($_POST['invite_code'] ?? ''));
            if (!$this->validRegistrationIdentifier($data)) {
                return redirect('/register')->withInput($_POST)->withErrors(['identifier' => trans('auth.error.identifier_invalid', 'ایمیل یا شماره موبایل معتبر را وارد کنید.')]);
            }
            if (empty($_POST['terms'])) {
                return redirect('/register')->withInput($_POST)->withErrors(['terms' => trans('auth.error.terms_required', 'پذیرش قوانین الزامی است.')]);
            }
            $verification = $this->registrationOtp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) {
                return redirect('/register')->withInput($_POST)->withErrors(['otp' => $verification['message']]);
            }
            $userId = $this->service->register($data);
            if (!$userId) {
                return redirect('/register')
                    ->withInput($_POST)
                    ->withErrors(['username' => trans('auth.error.register_failed', 'ثبت‌نام ناموفق بود.')]);
            }
            $this->registrationOtp->clear();
            $now = date('Y-m-d H:i:s');
            $verified = ['status' => 'approved', 'type' => 'human', 'approved_at' => $now, 'approved_by' => $userId, 'updated_by' => $userId];
            if (($data['register_method'] ?? '') === 'phone') $verified['phone_verified_at'] = $now;
            DB::table('users')->where('user_id', $userId)->update($verified);
            auth()->login((int)$userId, true);
            $this->recordLogin((int)$userId);
            $this->notifications->send(
                1,
                'ثبت‌نام کاربر جدید در جدول users',
                'کاربر با user_id=' . $userId . ' (انجام‌دهنده عملیات: user_id=' . $userId . ') در جدول users یک سطر جدید اضافه کرد. ستون‌های درج‌شده: username، email، phone، password، type، status، locale، timezone، register_method، created_by، updated_by، approved_at، approved_by' . (($data['register_method'] ?? '') === 'phone' ? '، phone_verified_at' : '') . '. همچنین ستون‌های status، type، approved_at، approved_by، updated_by و' . (($data['register_method'] ?? '') === 'phone' ? ' phone_verified_at' : '') . ' در همین سطر به‌روزرسانی شدند و ورود در ستون‌های last_login_at و last_login_ip ثبت شد.',
                'users',
                $userId,
                $userId
            );
            session()->flash('auth_success', 'ثبت نام شما و ورود به حساب کاربری با موفقیت انجام شد.');
            return redirect('/page/home');
        } catch (ValidationException $e) {
            return redirect('/register')
                ->withInput($_POST)
                ->withErrors($e->getErrors());
        }
    }

    public function sendRegistrationOtp() {
        try {
            $data = (new UserStoreRequest($_POST))->validated();
            $data['invite_code'] = trim((string)($_POST['invite_code'] ?? ''));
            if (!$this->validRegistrationIdentifier($data)) {
                return ResponseFactory::json(['success' => false, 'message' => trans('auth.error.identifier_invalid', 'ایمیل یا شماره موبایل معتبر را وارد کنید.')], 422);
            }
            if (empty($_POST['terms'])) {
                return ResponseFactory::json(['success' => false, 'message' => trans('auth.error.terms_required', 'پذیرش قوانین الزامی است.')], 422);
            }
            $method = $data['register_method'];
            $destination = trim((string)$data[$method]);
            $result = $this->registrationOtp->send($method, $destination, $this->otpData($data));
            $status = $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503);
            return ResponseFactory::json(['success' => $result['ok']] + $result, $status);
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => trans('auth.error.register_failed', 'اطلاعات فرم را بررسی کنید.'), 'errors' => $e->getErrors()], 422);
        }
    }

    public function sendPasswordResetOtp() {
        $method = $_POST['method'] ?? '';
        $destination = trim((string)($_POST['destination'] ?? ''));
        if ($method === 'email') {
            $destination = strtolower($destination);
            if (!filter_var($destination, FILTER_VALIDATE_EMAIL)) {
                return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.email_invalid', 'ایمیل معتبر نیست.')], 422);
            }
        } elseif ($method === 'phone') {
            $destination = preg_replace('/\D+/', '', $destination);
            if (!preg_match('/^09\d{9}$/', $destination)) {
                return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.phone_invalid', 'شماره موبایل معتبر نیست.')], 422);
            }
        } else {
            return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.request_failed', 'روش ارسال معتبر نیست.')], 422);
        }
        $result = $this->passwordResetOtp->send($method, $destination);
        $status = $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 422);
        return ResponseFactory::json(['success' => $result['ok']] + $result, $status);
    }

    public function verifyPasswordResetOtp() {
        $code = trim((string)($_POST['code'] ?? ''));
        if (!preg_match('/^\d{6}$/', $code)) {
            return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.otp_incomplete', 'کد ۶ رقمی را کامل وارد کنید.')], 422);
        }
        $result = $this->passwordResetOtp->verify($code);
        return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : 422);
    }

    public function resetPassword() {
        $password = (string)($_POST['password'] ?? '');
        $confirmation = (string)($_POST['password_confirmation'] ?? '');
        if (strlen($password) < 8) {
            return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.password_short', 'رمز عبور باید حداقل ۸ کاراکتر باشد.')], 422);
        }
        if (!hash_equals($password, $confirmation)) {
            return ResponseFactory::json(['success' => false, 'message' => trans('auth.js.password_mismatch', 'رمز عبور و تکرار آن یکسان نیست.')], 422);
        }
        $result = $this->passwordResetOtp->reset($password);
        if ($result['ok']) session()->flash('auth_success', $result['message']);
        return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : 422);
    }

    private function validRegistrationIdentifier(array &$data): bool {
        $method = $data['register_method'] ?? '';
        if ($method === 'email') {
            $data['email'] = strtolower(trim((string)($data['email'] ?? '')));
            $data['phone'] = null;
            return filter_var($data['email'], FILTER_VALIDATE_EMAIL) !== false;
        }
        $data['phone'] = preg_replace('/\D+/', '', (string)($data['phone'] ?? ''));
        $data['email'] = null;
        return (bool)preg_match('/^09\d{9}$/', $data['phone']);
    }

    private function otpData(array $data): array {
        unset($data['otp'], $data['password2'], $data['terms']);
        ksort($data);
        return $data;
    }

    private function recordLogin(int $userId): void {
        DB::table('users')->where('user_id', $userId)->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ]);
    }




}
