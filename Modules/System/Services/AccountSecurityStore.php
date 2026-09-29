<?php
namespace Modules\System\Services;

final class AccountSecurityStore
{
    public function allow(string $key, int $limit, int $seconds): bool
    {
        $bucket = hash('sha256', $key . ':' . intdiv(time(), $seconds));
        $expires = (intdiv(time(), $seconds) + 1) * $seconds;
        $sqlite = db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite';
        $sql = $sqlite
            ? 'INSERT INTO auth_rate_limits (bucket_key,attempts,expires_at) VALUES (?,1,?) ON CONFLICT(bucket_key) DO UPDATE SET attempts=attempts+1'
            : 'INSERT INTO auth_rate_limits (bucket_key,attempts,expires_at) VALUES (?,1,?) ON DUPLICATE KEY UPDATE attempts=attempts+1';
        db()->prepare($sql)->execute([$bucket, $expires]);
        $query = db()->prepare('SELECT attempts FROM auth_rate_limits WHERE bucket_key=?');
        $query->execute([$bucket]);
        if (random_int(1, 100) === 1) {
            db()->prepare('DELETE FROM auth_rate_limits WHERE expires_at < ?')->execute([time() - 86400]);
        }
        return (int) $query->fetchColumn() <= $limit;
    }

    public function issue(int $userId, int $expires): string
    {
        $token = bin2hex(random_bytes(32));
        db()->prepare('INSERT INTO auth_remember_tokens (token_hash,user_id,expires_at) VALUES (?,?,?)')->execute([hash('sha256', $token), $userId, $expires]);
        db()->prepare('DELETE FROM auth_remember_tokens WHERE expires_at < ?')->execute([time()]);
        return $token;
    }

    public function valid(string $token, int $userId): bool
    {
        $query = db()->prepare('SELECT user_id FROM auth_remember_tokens WHERE token_hash=? AND user_id=? AND expires_at>?');
        $query->execute([hash('sha256', $token), $userId, time()]);
        return (bool) $query->fetchColumn();
    }

    public function revoke(string $token): void
    {
        db()->prepare('DELETE FROM auth_remember_tokens WHERE token_hash=?')->execute([hash('sha256', $token)]);
    }
}
