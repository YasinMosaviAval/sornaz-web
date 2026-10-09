<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\MailService;
use Modules\System\Services\SmsService;
use PDO;
use RuntimeException;

final class MobileContactChangeService
{
    public function __construct(private MailService $mail, private SmsService $sms, private AdminAccountService $account)
    {
    }

    public function send(int $actor, string $field, string $destination): array
    {
        $destination = ContactChangeService::normalize($field, $destination);
        if ($destination === null) throw new RuntimeException('نشانی تماس جدید الزامی است.', 422);
        return transaction(function () use ($actor, $field, $destination): array {
            $previous = $this->locked($actor, $field);
            if ($previous && strtotime((string) $previous['sent_at']) + 60 > time()) throw new RuntimeException('برای ارسال دوبارهٔ کد کمی صبر کنید.', 429);
            $contact = new ContactChangeService($this->mail, $this->sms);
            $contact->send($actor, $field, $destination);
            $pending = session()->get('account_contact_change_' . $field, []);
            if (empty($pending['hash'])) throw new RuntimeException('ارسال کد تأیید ناموفق بود.', 422);
            $values = ['destination' => $destination, 'code_hash' => $pending['hash'], 'sent_at' => date('Y-m-d H:i:s'), 'expires_at' => date('Y-m-d H:i:s', time() + 300), 'attempts' => 0, 'verified_at' => null, 'consumed_at' => null];
            if ($previous) DB::table('mobile_contact_challenges')->where('user_id', $actor)->where('field', $field)->update($values);
            else DB::table('mobile_contact_challenges')->insert(['user_id' => $actor, 'field' => $field] + $values);
            ContactChangeService::clear($field);
            return ['expiresIn' => 300];
        });
    }

    public function verify(int $actor, string $field, string $destination, string $code): void
    {
        $destination = ContactChangeService::normalize($field, $destination);
        $result = transaction(function () use ($actor, $field, $destination, $code): bool {
            $row = $this->locked($actor, $field);
            if (!$row || $row['consumed_at'] || strtotime((string) $row['expires_at']) <= time() || (int) $row['attempts'] >= 5 || !hash_equals((string) $row['destination'], (string) $destination)) return false;
            $valid = preg_match('/^[0-9]{6}$/D', $code) === 1 && password_verify($code, (string) $row['code_hash']);
            DB::table('mobile_contact_challenges')->where('user_id', $actor)->where('field', $field)->update($valid ? ['verified_at' => date('Y-m-d H:i:s')] : ['attempts' => (int) $row['attempts'] + 1, 'verified_at' => null]);
            return $valid;
        });
        if (!$result) throw new RuntimeException('کد تأیید معتبر نیست یا منقضی شده است.', 422);
    }

    public function commit(int $actor, string $field, string $destination): void
    {
        $destination = ContactChangeService::normalize($field, $destination);
        transaction(function () use ($actor, $field, $destination): void {
            $row = $this->locked($actor, $field);
            if (!$row || !$row['verified_at'] || $row['consumed_at'] || (int) $row['attempts'] >= 5 || strtotime((string) $row['expires_at']) <= time() || !hash_equals((string) $row['destination'], (string) $destination)) {
                throw new RuntimeException('تأیید نشانی تماس معتبر نیست یا منقضی شده است.', 422);
            }
            session()->put('account_contact_change_' . $field, ['actor' => $actor, 'destination' => $destination, 'expires_at' => strtotime((string) $row['expires_at']), 'verified' => true]);
            try {
                $this->account->saveSecurity($actor, [$field => $destination]);
                DB::table('mobile_contact_challenges')->where('user_id', $actor)->where('field', $field)->update(['consumed_at' => date('Y-m-d H:i:s')]);
            } finally {
                ContactChangeService::clear($field);
            }
        });
    }

    private function locked(int $actor, string $field): ?array
    {
        $sql = 'SELECT * FROM mobile_contact_challenges WHERE user_id=? AND field=?';
        if (db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') $sql .= ' FOR UPDATE';
        $query = db()->prepare($sql);
        $query->execute([$actor, $field]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
