<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\MailService;
use Modules\System\Services\SmsService;
use RuntimeException;

final class ContactChangeService
{
    private const TTL = 300;
    private const KEY = 'account_contact_change';

    public function __construct(private MailService $mail, private SmsService $sms)
    {
    }

    public function send(int $actor, string $field, string $destination): array
    {
        $destination = self::normalize($field, $destination);
        if ($destination === null) {
            throw new RuntimeException('نشانی تماس جدید الزامی است.');
        }
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) {
            throw new RuntimeException('حساب یافت نشد.');
        }
        if ($destination === self::normalize($field, $user[$field] ?? null)) {
            throw new RuntimeException('نشانی تماس تغییر نکرده است.');
        }
        self::assertUnique($actor, $field, $destination);
        $key = self::KEY . '_' . $field;
        $previous = session()->get($key, []);
        if ((int) ($previous['sent_at'] ?? 0) + 60 > time()) {
            throw new RuntimeException('برای ارسال دوبارهٔ کد کمی صبر کنید.');
        }
        $code = (string) random_int(100000, 999999);
        $sent = $field === 'email'
            ? $this->mail->sendRegistrationOtp($destination, $code, 5)
            : $this->sms->sendRegistrationOtp($destination, $code, 5);
        if (!$sent) {
            throw new RuntimeException('ارسال کد تأیید انجام نشد.');
        }
        session()->put($key, ['actor' => $actor, 'destination' => $destination,
            'hash' => password_hash($code, PASSWORD_DEFAULT), 'sent_at' => time(),
            'expires_at' => time() + self::TTL, 'attempts' => 0, 'verified' => false]);
        return ['message' => 'کد تأیید به نشانی جدید ارسال شد.', 'expiresIn' => self::TTL];
    }

    public function verify(int $actor, string $field, string $destination, string $code): void
    {
        $destination = self::normalize($field, $destination);
        $key = self::KEY . '_' . $field;
        $pending = session()->get($key, []);
        if ((int) ($pending['actor'] ?? 0) !== $actor ||
            !hash_equals((string) ($pending['destination'] ?? ''), (string) $destination) ||
            (int) ($pending['expires_at'] ?? 0) <= time() ||
            (int) ($pending['attempts'] ?? 0) >= 5) {
            session()->forget($key);
            throw new RuntimeException('کد تأیید معتبر نیست یا منقضی شده است.');
        }
        if (!preg_match('/^[0-9]{6}$/D', $code) || !password_verify($code, (string) $pending['hash'])) {
            $pending['attempts']++;
            session()->put($key, $pending);
            throw new RuntimeException('کد تأیید نادرست است.');
        }
        $pending['verified'] = true;
        session()->put($key, $pending);
    }

    public static function assertVerified(int $actor, string $field, ?string $destination): void
    {
        $key = self::KEY . '_' . $field;
        $pending = session()->get($key, []);
        if (empty($pending['verified']) || (int) ($pending['actor'] ?? 0) !== $actor ||
            (int) ($pending['expires_at'] ?? 0) <= time() ||
            !hash_equals((string) ($pending['destination'] ?? ''), (string) $destination)) {
            throw new RuntimeException('ابتدا نشانی تماس جدید را با کد تأیید کنید.');
        }
        self::assertUnique($actor, $field, $destination);
    }

    public static function clear(string $field): void
    {
        session()->forget(self::KEY . '_' . $field);
    }

    public static function normalize(string $field, mixed $value): ?string
    {
        if (!in_array($field, ['email', 'phone'], true)) {
            throw new RuntimeException('نوع اطلاعات تماس معتبر نیست.');
        }
        $value = trim((string) $value);
        if ($field === 'email') {
            $value = strtolower($value);
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('ایمیل معتبر نیست.');
            }
        } else {
            $value = preg_replace('/[^0-9+]/', '', $value);
            if ($value !== '' && !preg_match('/^\+?[0-9]{8,15}$/D', $value)) {
                throw new RuntimeException('شماره تلفن معتبر نیست.');
            }
        }
        return $value === '' ? null : $value;
    }

    private static function assertUnique(int $actor, string $field, string $destination): void
    {
        if (DB::table('users')->where($field, $destination)->where('user_id', '!=', $actor)->whereNull('deleted_at')->first()) {
            throw new RuntimeException('این نشانی تماس در حساب دیگری ثبت شده است.');
        }
    }
}
