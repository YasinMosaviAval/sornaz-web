<?php
namespace Modules\Academy\Services;

use Modules\System\Requests\UserStoreRequest;
use Modules\System\Services\RegistrationOtpPolicy;
use Modules\System\Services\RegistrationOtpService;
use Modules\System\Services\UserService;
use Core\database\DB;
use RuntimeException;

final class StaffRegistrationService
{
    public function __construct(private UserService $users, private RegistrationOtpService $otp) {}

    public static function required(string $type, array $role): bool
    {
        return in_array($type, ['manager', 'receptionist'], true) || (bool) preg_match('/manager|receptionist/', (string) ($role['name'] ?? ''));
    }

    private function data(int $actor, array $staff): array
    {
        $input = is_array($staff['registration'] ?? null) ? $staff['registration'] : [];
        $method = (string) ($input['register_method'] ?? 'phone');
        $phone = preg_replace('/\D+/', '', strtr((string) ($staff['phone'] ?? ''), array_combine(preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY), array_merge(range(0, 9), range(0, 9)))));
        $email = $method === 'email' ? strtolower(trim((string) ($input['email'] ?? ''))) : null;
        if (!preg_match('/^09\d{9}$/', $phone) || ($method === 'email' && !filter_var($email, FILTER_VALIDATE_EMAIL))) throw new RuntimeException('شماره موبایل یا ایمیل ثبت‌نام معتبر نیست.');
        if (empty($input['terms'])) throw new RuntimeException('پذیرش قوانین ثبت‌نام الزامی است.');
        return (new UserStoreRequest([
            'username' => trim((string) ($input['username'] ?? '')), 'register_method' => $method,
            'email' => $email, 'phone' => $phone, 'password' => (string) ($input['password'] ?? ''), 'password2' => (string) ($input['password2'] ?? ''),
            'full_name' => trim((string) ($staff['name'] ?? '')), 'type' => 'human', 'status' => 'approved',
            'locale' => locale(), 'timezone' => 'Asia/Tehran', 'created_by' => $actor, 'updated_by' => $actor, 'skip_default_role' => true,
        ]))->validated();
    }

    private function binding(int $actor, array $staff, array $data): array
    {
        unset($staff['registration'], $data['password2']);
        return ['actor' => $actor, 'staff' => $staff, 'registration' => $data];
    }

    public function send(int $actor, array $staff): array
    {
        $data = $this->data($actor, $staff);
        if (RegistrationOtpPolicy::web()) return ['ok' => true, 'otp_required' => false, 'expires_in' => 0];
        return $this->otp->send($data['register_method'], $data[$data['register_method']], $this->binding($actor, $staff, $data));
    }

    public function register(int $actor, array $staff): int
    {
        $data = $this->data($actor, $staff);
        $verification = RegistrationOtpPolicy::web() ? ['ok' => true] : $this->otp->verify(trim((string) ($staff['registration']['otp'] ?? '')), $this->binding($actor, $staff, $data));
        if (!$verification['ok']) throw new RuntimeException($verification['message']);
        $id = $this->users->register($data);
        if (!$id) throw new RuntimeException('ثبت‌نام کارمند انجام نشد.');
        $verified = ['approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $actor];
        if ($data['register_method'] === 'phone') $verified['phone_verified_at'] = date('Y-m-d H:i:s');
        DB::table('users')->where('user_id', $id)->update($verified);
        return $id;
    }

    public function clear(): void { $this->otp->clear(); }
}
