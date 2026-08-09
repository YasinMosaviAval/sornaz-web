<?php

namespace Modules\System\Services;

class SmsService {
    private ?string $lastError = null;

    public function lastError(): ?string {
        return $this->lastError;
    }

    public function sendRegistrationOtp(string $phone, string $code, int $validMinutes): bool {
        $message = "کد فعال‌سازی برنامه آموزشی سرناز: {$code}\nاین کد تا {$validMinutes} دقیقه معتبر است.";
        return match (strtolower((string)config('system.sms.provider', 'kavenegar'))) {
            'kavenegar' => $this->sendKavenegar($phone, $message, $code),
            'melipayamak' => $this->sendMeliPayamak($phone, $message),
            default => false,
        };
    }

    public function sendPasswordResetOtp(string $phone, string $code, int $validMinutes): bool {
        $message = "کد بازیابی رمز عبور برنامه آموزشی سرناز: {$code}";
        return match (strtolower((string)config('system.sms.provider', 'kavenegar'))) {
            'kavenegar' => $this->sendKavenegar($phone, $message, $code, (string)config('system.sms.kavenegar_forgot_template', 'sornazforget')),
            'melipayamak' => $this->sendMeliPayamak($phone, $message),
            default => false,
        };
    }

    private function sendKavenegar(string $phone, string $message, string $code, ?string $templateOverride = null): bool {
        $apiKey = trim((string)config('system.sms.api_key', ''));
        if ($apiKey === '') {
            $this->lastError = 'کلید SMS_API_KEY تنظیم نشده است.';
            return false;
        }
        $template = trim($templateOverride ?? (string)config('system.sms.kavenegar_template', ''));
        if ($template !== '') {
            $url = "https://api.kavenegar.com/v1/{$apiKey}/verify/lookup.json";
            $params = ['receptor' => $phone, 'template' => $template, 'token' => $code];
        } else {
            $url = "https://api.kavenegar.com/v1/{$apiKey}/sms/send.json";
            $params = ['receptor' => $phone, 'message' => $message];
            if ($sender = trim((string)config('system.sms.sender', ''))) $params['sender'] = $sender;
        }
        $result = $this->postForm($url, $params, 'kavenegar');
        if (!$result['ok'] && $result['status'] === 427 && isset($params['sender'])) {
            unset($params['sender']);
            error_log('[Registration SMS Notice] Kavenegar sender is not authorized; retrying with the account default sender.');
            $result = $this->postForm($url, $params, 'kavenegar');
        }
        return $result['ok'];
    }

    private function sendMeliPayamak(string $phone, string $message): bool {
        $username = trim((string)config('system.sms.username', ''));
        $password = (string)config('system.sms.password', '');
        $from = trim((string)config('system.sms.from', ''));
        if ($username === '' || $password === '' || $from === '') {
            $this->lastError = 'اطلاعات حساب ملی‌پیامک کامل نیست.';
            return false;
        }
        $result = $this->postForm('https://rest.payamak-panel.com/api/SendSMS/SendSMS', [
            'username' => $username, 'password' => $password, 'to' => $phone,
            'from' => $from, 'text' => $message, 'isFlash' => 'false',
        ], 'melipayamak');
        return $result['ok'];
    }

    private function postForm(string $url, array $params, string $provider): array {
        if (!function_exists('curl_init')) {
            $this->lastError = 'کتابخانه cURL روی سرور فعال نیست.';
            error_log("[Registration SMS Error] {$this->lastError} Provider: {$provider}");
            return ['ok' => false, 'status' => 0, 'message' => $this->lastError];
        }
        $handle = curl_init($url);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);
        $json = json_decode((string)$response, true);
        $apiStatus = (int)($json['return']['status'] ?? $status);
        $apiMessage = trim((string)($json['return']['message'] ?? $error));
        $ok = $response !== false && $error === '' && $status >= 200 && $status < 300;
        if ($provider === 'kavenegar') {
            $ok = $ok && $apiStatus === 200;
        }
        if (!$ok) {
            $this->lastError = $apiMessage !== '' ? $apiMessage : "خطای HTTP {$status}";
            error_log("[Registration SMS Error] {$provider}: status {$apiStatus}; {$this->lastError}");
        }
        return ['ok' => $ok, 'status' => $apiStatus, 'message' => $apiMessage];
    }
}
