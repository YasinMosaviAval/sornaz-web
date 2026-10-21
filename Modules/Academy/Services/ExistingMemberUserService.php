<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use RuntimeException;

/** Session-bound selections reveal presence, never private identity values. */
final class ExistingMemberUserService
{
    public function find(int $actor, int $organization, string $query): array
    {
        $this->assertOrganization($actor, $organization);
        $query = trim($query);
        if (mb_strlen($query) < 3 || mb_strlen($query) > 100) {
            throw new RuntimeException('نام کاربری یا شماره موبایل کامل را وارد کنید.');
        }
        $user = DB::table('users')->where('username', $query)->where('type', 'human')->whereNull('deleted_at')->first();
        if (!$user && preg_match('/^09\d{9}$/', $query)) {
            $user = DB::table('users')->where('phone', $query)->where('type', 'human')->whereNull('deleted_at')->first();
        }
        if (!$user || in_array($user['status'], ['blocked', 'banned', 'inactive'], true)) return ['users' => []];
        $profile = $this->profile($user);
        $token = bin2hex(random_bytes(24));
        $selections = array_filter(session()->get('member_user_selections', []), static fn ($s) => $s['expires'] > time());
        $selections = array_slice($selections, -19, null, true);
        $selections[$token] = ['actor' => $actor, 'organization' => $organization, 'user' => (int) $user['user_id'], 'expires' => time() + 1800];
        session()->put('member_user_selections', $selections);
        $fields = array_map(static fn ($value) => $value !== '', $profile);
        $under18 = $profile['birthDate'] !== '' && (new \DateTimeImmutable($profile['birthDate']))->diff(new \DateTimeImmutable('today'))->y < 18;
        return ['users' => [['token' => $token, 'username' => $user['username'], 'name' => $profile['name'], 'fields' => $fields, 'under18' => $under18]]];
    }

    public function resolve(int $actor, array $data): array
    {
        unset($data['_existingUserId']);
        if (empty($data['existingUserToken'])) return $data;
        $selection = session()->get('member_user_selections', [])[(string) $data['existingUserToken']] ?? null;
        if (!$selection || $selection['actor'] !== $actor || $selection['organization'] !== (int) ($data['organizationUserId'] ?? 0) || $selection['expires'] <= time()) {
            throw new RuntimeException('انتخاب کاربر معتبر نیست؛ کاربر را دوباره انتخاب کنید.');
        }
        $this->assertOrganization($actor, $selection['organization']);
        $sql = 'SELECT * FROM users WHERE user_id = ? AND deleted_at IS NULL';
        if (db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql' && db()->inTransaction()) $sql .= ' FOR UPDATE';
        $statement = db()->prepare($sql);
        $statement->execute([$selection['user']]);
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$user || $user['type'] !== 'human' || in_array($user['status'], ['blocked', 'banned', 'inactive'], true)) throw new RuntimeException('کاربر انتخاب‌شده در دسترس نیست.');
        foreach ($this->profile($user) as $field => $value) {
            if ($value !== '') $data[$field] = $value;
        }
        $data['_existingUserId'] = (int) $user['user_id'];
        return $data;
    }

    private function profile(array $user): array
    {
        $tr = TranslationService::manager();
        $id = (int) $user['user_id'];
        return ['name' => $tr->get('users', $id, 'full_name', 'fa') ?: '', 'fatherName' => $tr->get('users', $id, 'father_name', 'fa') ?: '',
            'phone' => (string) ($user['phone'] ?? ''), 'nationalId' => (string) ($user['national_code'] ?? ''),
            'gender' => (string) ($user['gender'] ?? ''), 'birthDate' => (string) ($user['birthday'] ?? '')];
    }

    private function assertOrganization(int $actor, int $organization): void
    {
        $catalog = (new AcademyTermService())->discountOrganizationCatalog($actor);
        foreach ($catalog['manageable'] as $row) {
            if ((int) $row['user_id'] === $organization) return;
        }
        throw new RuntimeException('شما به سازمان انتخاب‌شده دسترسی ندارید.', 403);
    }

    public function complete(int $actor, array $data): int
    {
        $id = (int) ($data['_existingUserId'] ?? 0);
        if (!$id) return 0;
        foreach (['phone' => 'phone', 'national_code' => 'nationalId'] as $column => $field) {
            $duplicate = DB::table('users')->where($column, $data[$field])->whereNull('deleted_at')->first();
            if ($duplicate && (int) $duplicate['user_id'] !== $id) throw new RuntimeException('شماره تماس یا کد ملی متعلق به کاربر دیگری است.');
        }
        DB::table('users')->where('user_id', $id)->update(['phone' => $data['phone'], 'national_code' => $data['nationalId'], 'birthday' => $data['birthDate'], 'gender' => $data['gender'] ?? 'other', 'updated_by' => $actor]);
        foreach (['full_name' => 'name', 'father_name' => 'fatherName'] as $field => $key) {
            if (!empty($data[$key])) TranslationService::manager()->set('users', $id, $field, $data[$key], 'fa');
        }
        return $id;
    }

    public function assertNotMember(int $user, int $academy, ?int $branch): void
    {
        $query = DB::table('academy_branch_members')->where('user_id', $user)->where('academy_id', $academy)->whereNull('deleted_at');
        $query = $branch === null ? $query->whereNull('branch_id') : $query->where('branch_id', $branch);
        if ($query->first()) throw new RuntimeException('این کاربر قبلاً عضو سازمان انتخاب‌شده است.');
    }

    public function staffMembership(int $user, int $academy, ?int $branch, string $type): ?array
    {
        $query = DB::table('academy_branch_members')->where('user_id', $user)->where('academy_id', $academy)->whereNull('deleted_at');
        $member = ($branch === null ? $query->whereNull('branch_id') : $query->where('branch_id', $branch))->first();
        if (!$member) return null;
        if (!in_array($member['status'], ['active', 'pending'], true)) throw new RuntimeException('عضویت این کارمند فعال نیست.');
        if (DB::table('academy_branch_member_contracts')->where('member_id', (int) $member['member_id'])->where('type', $type)->whereNull('deleted_at')->first()) {
            throw new RuntimeException('این نوع قرارداد برای کارمند ثبت شده است؛ قرارداد موجود را ویرایش کنید.');
        }
        return $member;
    }
}
