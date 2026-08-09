<?php

namespace Modules\System\Services;

use Modules\System\Repositories\UserRepository;

class PasswordResetOtpService {
    private const KEY = 'password_reset_otp';
    private const TTL = 120;
    private const RESEND_DELAY = 60;
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        protected UserRepository $users,
        protected MailService $mail,
        protected SmsService $sms
    ) {}

    public function send(string $method, string $destination): array {
        $current = session()->get(self::KEY, []);
        if (!empty($current['sent_at']) && time() - $current['sent_at'] < self::RESEND_DELAY) {
            return ['ok' => false, 'message' => 'برای ارسال مجدد کمی صبر کنید.', 'retry_after' => self::RESEND_DELAY - (time() - $current['sent_at'])];
        }
        $user = $this->users->findByContact($method, $destination);
        if (!$user) return ['ok' => false, 'message' => 'حسابی با این ایمیل یا شماره موبایل پیدا نشد.'];
        $code = (string)random_int(100000, 999999);
        $sent = $method === 'email'
            ? $this->mail->sendPasswordResetOtp($destination, $code, intdiv(self::TTL, 60))
            : $this->sms->sendPasswordResetOtp($destination, $code, intdiv(self::TTL, 60));
        if (!$sent) return ['ok' => false, 'message' => $method === 'email'
            ? 'ارسال ایمیل بازیابی انجام نشد.'
            : 'ارسال پیامک انجام نشد. ' . ($this->sms->lastError() ?: '')];
        session()->put(self::KEY, [
            'user_id' => (int)$user['user_id'], 'method' => $method, 'destination' => $destination,
            'hash' => password_hash($code, PASSWORD_DEFAULT), 'expires_at' => time() + self::TTL,
            'sent_at' => time(), 'attempts' => 0, 'verified' => false,
        ]);
        return ['ok' => true, 'message' => 'کد بازیابی ارسال شد.', 'expires_in' => self::TTL];
    }

    public function verify(string $code): array {
        $data = session()->get(self::KEY);
        if (!$data) return ['ok' => false, 'message' => 'ابتدا کد بازیابی دریافت کنید.'];
        if (($data['expires_at'] ?? 0) < time()) { $this->clear(); return ['ok' => false, 'message' => 'کد بازیابی منقضی شده است.']; }
        if (($data['attempts'] ?? 0) >= self::MAX_ATTEMPTS) { $this->clear(); return ['ok' => false, 'message' => 'تعداد تلاش‌ها بیش از حد مجاز است.']; }
        if (!password_verify($code, $data['hash'])) {
            $data['attempts']++; session()->put(self::KEY, $data);
            return ['ok' => false, 'message' => 'کد بازیابی نادرست است.'];
        }
        $data['verified'] = true;
        session()->put(self::KEY, $data);
        return ['ok' => true, 'message' => 'کد تأیید شد.'];
    }

    public function reset(string $password): array {
        $data = session()->get(self::KEY);
        if (!$data || empty($data['verified'])) return ['ok' => false, 'message' => 'کد بازیابی تأیید نشده است.'];
        if (!$this->users->updatePassword((int)$data['user_id'], password_hash($password, PASSWORD_DEFAULT))) {
            return ['ok' => false, 'message' => 'ذخیره رمز عبور انجام نشد.'];
        }
        $this->clear();
        return ['ok' => true, 'message' => 'رمز عبور با موفقیت تغییر کرد.'];
    }

    public function clear(): void { session()->forget(self::KEY); }
}
