<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\Analytics\Requests\AcademyRegistrationRequest;
use Modules\Analytics\Services\AcademyRegistrationService;
use Modules\System\Services\RegistrationOtpService;

class AcademyRegistrationController {
    public function __construct(
        protected AcademyRegistrationService $service,
        protected RegistrationOtpService $otp
    ) {}

    public function store() {
        try {
            $data = $this->validatedData();
            $verification = $this->otp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) return $this->back(['otp' => $verification['message']]);
            $this->service->register($data);
            $this->otp->clear();
            session()->flash('auth_success', 'درخواست ثبت آموزشگاه با موفقیت ثبت شد. پس از تأیید می‌توانید وارد پنل مدیریت شوید.');
            return redirect('/login');
        } catch (ValidationException $e) {
            return $this->back($e->getErrors());
        } catch (\Throwable $e) {
            return $this->back(['academy_name' => 'ثبت درخواست انجام نشد. لطفاً دوباره تلاش کنید.']);
        }
    }

    public function sendOtp() {
        try {
            $data = $this->validatedData();
            $method = $data['register_method'];
            $result = $this->otp->send($method, (string)$data[$method], $this->otpData($data));
            return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503));
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => 'اطلاعات فرم را بررسی کنید.', 'errors' => $e->getErrors()], 422);
        }
    }

    private function validatedData(): array {
        $data = (new AcademyRegistrationRequest($_POST))->validated();
        if (empty($_POST['terms'])) throw new ValidationException(['terms' => 'پذیرش قوانین ثبت آموزشگاه الزامی است.']);
        $method = $data['register_method'];
        if ($method === 'email') {
            $data['email'] = strtolower(trim((string)($data['email'] ?? '')));
            $data['phone'] = null;
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new ValidationException(['email' => 'ایمیل معتبر را وارد کنید.']);
        } else {
            $data['phone'] = preg_replace('/\D+/', '', (string)($data['phone'] ?? ''));
            $data['email'] = null;
            if (!preg_match('/^09\d{9}$/', $data['phone'])) throw new ValidationException(['phone' => 'شماره موبایل معتبر را وارد کنید.']);
        }
        return $data;
    }

    private function otpData(array $data): array {
        unset($data['password2'], $data['terms'], $data['otp']);
        ksort($data);
        return $data;
    }

    private function back(array $errors) {
        return redirect('/analytics/send-academy-request')->withInput($_POST)->withErrors($errors);
    }
}
