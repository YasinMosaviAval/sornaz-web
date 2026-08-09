<?php
namespace Modules\System\Controllers\Web;

use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\System\Services\UserService;
use Modules\System\Requests\UserStoreRequest;
use Modules\System\Services\RegistrationOtpService;

class UserController {

    public function __construct(protected UserService $service, protected RegistrationOtpService $registrationOtp) {
    }


    public function login() {
        $identifier = trim($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';
        if (!$identifier || !$password) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => 'نام‌کاربری، ایمیل یا شماره موبایل و رمز عبور الزامی است.']);
        }
        $user = $this->service->attempt($identifier, $password);
        if (!$user) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => 'نام‌کاربری، ایمیل، شماره موبایل یا رمز عبور اشتباه است.']);
        }
        auth()->login($user['user_id']);
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
            if (!$this->validRegistrationIdentifier($data)) {
                return redirect('/register')->withInput($_POST)->withErrors(['identifier' => 'ایمیل یا شماره موبایل معتبر را وارد کنید.']);
            }
            if (empty($_POST['terms'])) {
                return redirect('/register')->withInput($_POST)->withErrors(['terms' => 'پذیرش قوانین الزامی است.']);
            }
            $verification = $this->registrationOtp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) {
                return redirect('/register')->withInput($_POST)->withErrors(['otp' => $verification['message']]);
            }
            $userId = $this->service->register($data);
            if (!$userId) {
                return redirect('/register')
                    ->withInput($_POST)
                    ->withErrors(['username' => 'ثبت‌نام ناموفق بود.']);
            }
            $this->registrationOtp->clear();
            return redirect('/login');
        } catch (ValidationException $e) {
            return redirect('/register')
                ->withInput($_POST)
                ->withErrors($e->getErrors());
        }
    }

    public function sendRegistrationOtp() {
        try {
            $data = (new UserStoreRequest($_POST))->validated();
            if (!$this->validRegistrationIdentifier($data)) {
                return ResponseFactory::json(['success' => false, 'message' => 'ایمیل یا شماره موبایل معتبر را وارد کنید.'], 422);
            }
            if (empty($_POST['terms'])) {
                return ResponseFactory::json(['success' => false, 'message' => 'پذیرش قوانین الزامی است.'], 422);
            }
            $method = $data['register_method'];
            $destination = trim((string)$data[$method]);
            $result = $this->registrationOtp->send($method, $destination, $this->otpData($data));
            $status = $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503);
            return ResponseFactory::json(['success' => $result['ok']] + $result, $status);
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => 'اطلاعات فرم را بررسی کنید.', 'errors' => $e->getErrors()], 422);
        }
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




}
