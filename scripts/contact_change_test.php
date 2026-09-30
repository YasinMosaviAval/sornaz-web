<?php
namespace Core\database {
    final class DB {
        public static array $users = [2 => ['user_id' => 2, 'email' => 'old@example.com', 'phone' => '09121111111']];
        public static function table(string $name): object {
            return new class {
                private array $conditions = [];
                public function where(string $field, mixed $operator, mixed $value = null): self {
                    $this->conditions[] = [$field, $value === null ? '=' : $operator, $value === null ? $operator : $value];
                    return $this;
                }
                public function whereNull(string $field): self { return $this; }
                public function first(): ?array {
                    foreach (DB::$users as $user) {
                        $matches = true;
                        foreach ($this->conditions as [$field, $operator, $value]) {
                            if ($operator === '!=' ? ($user[$field] ?? null) == $value : ($user[$field] ?? null) != $value) {
                                $matches = false;
                            }
                        }
                        if ($matches) return $user;
                    }
                    return null;
                }
            };
        }
    }
}
namespace {
    class ContactSession {
        public array $data = [];
        public function get(string $key, mixed $default = null): mixed { return $this->data[$key] ?? $default; }
        public function put(string $key, mixed $value): void { $this->data[$key] = $value; }
        public function forget(string $key): void { unset($this->data[$key]); }
    }
    function session(): ContactSession { static $session; return $session ??= new ContactSession(); }
    spl_autoload_register(function ($class) {
        $path = dirname(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
        if (is_file($path)) require_once $path;
    });
    class TestMail extends \Modules\System\Services\MailService {
        public string $code = '';
        public function sendRegistrationOtp(string $email, string $code, int $validMinutes): bool {
            $this->code = $code;
            return true;
        }
    }
    class TestSms extends \Modules\System\Services\SmsService {
        public function sendRegistrationOtp(string $phone, string $code, int $validMinutes): bool { return true; }
    }
    function check(bool $valid, string $message): void { if (!$valid) throw new \RuntimeException($message); }
    $mail = new TestMail();
    $service = new \Modules\Analytics\Services\ContactChangeService($mail, new TestSms());
    $service->send(2, 'email', ' NEW@EXAMPLE.COM ');
    try { $service->verify(2, 'email', 'new@example.com', '000000'); throw new \RuntimeException('Wrong code accepted'); }
    catch (\RuntimeException $e) { check(str_contains($e->getMessage(), 'نادرست'), 'Wrong code should be rejected'); }
    $service->verify(2, 'email', 'new@example.com', $mail->code);
    \Modules\Analytics\Services\ContactChangeService::assertVerified(2, 'email', 'new@example.com');
    try { \Modules\Analytics\Services\ContactChangeService::assertVerified(3, 'email', 'new@example.com'); throw new \RuntimeException('Other account reused code'); }
    catch (\RuntimeException $e) { check(str_contains($e->getMessage(), 'تأیید'), 'Code must belong to account'); }
    \Modules\Analytics\Services\ContactChangeService::clear('email');
    try { \Modules\Analytics\Services\ContactChangeService::assertVerified(2, 'email', 'new@example.com'); throw new \RuntimeException('Code was reused'); }
    catch (\RuntimeException $e) { check(str_contains($e->getMessage(), 'تأیید'), 'Code must be single-use'); }
    \Core\database\DB::$users[3] = ['user_id' => 3, 'email' => 'taken@example.com', 'phone' => '09122222222'];
    try { $service->send(2, 'email', 'taken@example.com'); throw new \RuntimeException('Duplicate email accepted'); }
    catch (\RuntimeException $e) { check(str_contains($e->getMessage(), 'دیگری'), 'Duplicate email must be rejected'); }
    echo "Contact changes: delivery, verification, account binding, single use and uniqueness passed.\n";
}
