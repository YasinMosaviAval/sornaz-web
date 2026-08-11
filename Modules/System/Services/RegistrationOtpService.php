<?php

namespace Modules\System\Services;

class RegistrationOtpService {
    private const SESSION_KEY = 'registration_otp';
    private const TTL = 120;
    private const RESEND_DELAY = 60;
    private const MAX_ATTEMPTS = 5;

    public function __construct(protected MailService $mail, protected SmsService $sms) {
    }

    public function send(string $method, string $destination, array $registrationData): array {
        $previous = session()->get(self::SESSION_KEY, []);
        $lastSentAt = (int)($previous['sent_at'] ?? 0);
        if ($lastSentAt && time() - $lastSentAt < self::RESEND_DELAY) {
            return ['ok' => false, 'message' => trans('otp.wait_before_resend', 'برای ارسال مجدد کد کمی صبر کنید.'), 'retry_after' => self::RESEND_DELAY - (time() - $lastSentAt)];
        }

        $code = (string)random_int(100000, 999999);
        if (!$this->deliver($method, $destination, $code)) {
            return ['ok' => false, 'message' => $method === 'email'
                ? trans('otp.email_failed', 'ارسال ایمیل انجام نشد. تنظیمات سرویس ایمیل را بررسی کنید.')
                : trans('otp.sms_failed', 'ارسال پیامک انجام نشد. تنظیمات سرویس پیامک را بررسی کنید.')];
        }

        session()->put(self::SESSION_KEY, [
            'method' => $method,
            'destination' => $destination,
            'code_hash' => password_hash($code, PASSWORD_DEFAULT),
            'data_hash' => hash('sha256', serialize($registrationData)),
            'expires_at' => time() + self::TTL,
            'sent_at' => time(),
            'attempts' => 0,
        ]);

        return ['ok' => true, 'message' => trans('otp.sent', 'کد تأیید ارسال شد.'), 'expires_in' => self::TTL];
    }

    public function verify(string $code, array $registrationData): array {
        $otp = session()->get(self::SESSION_KEY);
        if (!$otp) return ['ok' => false, 'message' => trans('otp.request_first', 'ابتدا کد تأیید را دریافت کنید.')];
        if (($otp['expires_at'] ?? 0) < time()) {
            session()->forget(self::SESSION_KEY);
            return ['ok' => false, 'message' => trans('otp.expired', 'کد تأیید منقضی شده است.')];
        }
        if (($otp['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            session()->forget(self::SESSION_KEY);
            return ['ok' => false, 'message' => trans('otp.too_many_attempts', 'تعداد تلاش‌های ناموفق بیش از حد مجاز است. کد جدید بگیرید.')];
        }
        if (!hash_equals((string)$otp['data_hash'], hash('sha256', serialize($registrationData)))) {
            return ['ok' => false, 'message' => trans('otp.data_changed', 'اطلاعات ثبت‌نام تغییر کرده است. دوباره کد دریافت کنید.')];
        }
        if (!password_verify($code, (string)$otp['code_hash'])) {
            $otp['attempts'] = ($otp['attempts'] ?? 0) + 1;
            session()->put(self::SESSION_KEY, $otp);
            return ['ok' => false, 'message' => trans('otp.invalid', 'کد تأیید نادرست است.')];
        }
        return ['ok' => true];
    }

    public function clear(): void {
        session()->forget(self::SESSION_KEY);
    }

    private function deliver(string $method, string $destination, string $code): bool {
        if (env('APP_ENV', 'production') === 'local' && filter_var(env('OTP_FAKE_IN_LOCAL', true), FILTER_VALIDATE_BOOLEAN)) return true;
        return $method === 'email'
            ? $this->mail->sendRegistrationOtp($destination, $code, intdiv(self::TTL, 60))
            : $this->sms->sendRegistrationOtp($destination, $code, intdiv(self::TTL, 60));
    }
}
