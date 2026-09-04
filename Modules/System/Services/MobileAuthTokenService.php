<?php

namespace Modules\System\Services;

use Modules\System\Repositories\UserRepository;

class MobileAuthTokenService
{
    private const TTL = 2592000;

    public function __construct(protected UserRepository $users)
    {
    }

    public function issue(array $user): string
    {
        $payload = $this->encode(json_encode([
            'sub' => (int)$user['user_id'],
            'exp' => time() + self::TTL,
            'pwd' => $this->fingerprint((string)$user['password']),
        ], JSON_UNESCAPED_SLASHES));

        return $payload . '.' . $this->encode(hash_hmac('sha256', $payload, $this->key(), true));
    }

    public function userFromRequest(): ?array
    {
        $header = (string)($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches)) return null;
        $parts = explode('.', $matches[1]);
        if (count($parts) !== 2) return null;
        [$payload, $signature] = $parts;
        if (!hash_equals($this->encode(hash_hmac('sha256', $payload, $this->key(), true)), $signature)) return null;
        $data = json_decode((string)$this->decode($payload), true);
        if (!is_array($data) || (int)($data['exp'] ?? 0) < time() || empty($data['sub'])) return null;
        $user = $this->users->find((int)$data['sub']);
        if (!$user || !hash_equals((string)($data['pwd'] ?? ''), $this->fingerprint((string)$user['password']))) return null;
        return $user;
    }

    private function key(): string
    {
        $key = (string)config('app.key', '');
        return $key !== '' ? $key : hash('sha256', base_path());
    }

    private function fingerprint(string $hash): string
    {
        return substr(hash('sha256', $hash), 0, 24);
    }

    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function decode(string $value): string|false
    {
        return base64_decode(strtr($value, '-_', '+/'));
    }
}
