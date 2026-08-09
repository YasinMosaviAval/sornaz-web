<?php

namespace Core\auth;

use Modules\System\Contracts\UserRepositoryInterface;

class Auth {

    protected string $sessionKey = '_auth_user';
    protected string $rememberCookie = 'sornaz_remember';


    public function __construct(protected UserRepositoryInterface $users) {
    }


    public function check(): bool {
        return $this->id() !== null;
    }


    public function user(): ?array {
        $id = $this->id();
        if (!$id) {
            return null;
        }
        return $this->users->find($id);
    }


    public function id(): ?int {
        $sessionId = session()->get($this->sessionKey);
        if ($sessionId) return (int)$sessionId;
        return $this->restoreRememberedUser();
    }


    public function login(int $userId, bool $remember = false): void {
        session()->put($this->sessionKey, $userId);
        if ($remember) $this->setRememberCookie($userId);
        else $this->clearRememberCookie();
    }


    public function logout(): void {
        session()->forget($this->sessionKey);
        $this->clearRememberCookie();
    }

    protected function restoreRememberedUser(): ?int {
        $cookie = $_COOKIE[$this->rememberCookie] ?? '';
        $parts = explode('.', $cookie);
        if (count($parts) !== 4) return null;
        [$userId, $expires, $fingerprint, $signature] = $parts;
        if (!ctype_digit($userId) || !ctype_digit($expires) || (int)$expires < time()) {
            $this->clearRememberCookie();
            return null;
        }
        $payload = "{$userId}.{$expires}.{$fingerprint}";
        if (!hash_equals($this->sign($payload), $signature)) {
            $this->clearRememberCookie();
            return null;
        }
        $user = $this->users->find((int)$userId);
        if (!$user || !hash_equals($fingerprint, $this->passwordFingerprint((string)($user['password'] ?? '')))) {
            $this->clearRememberCookie();
            return null;
        }
        session()->put($this->sessionKey, (int)$userId);
        return (int)$userId;
    }

    protected function setRememberCookie(int $userId): void {
        $user = $this->users->find($userId);
        if (!$user) return;
        $expires = time() + 60 * 60 * 24 * 30;
        $fingerprint = $this->passwordFingerprint((string)$user['password']);
        $payload = "{$userId}.{$expires}.{$fingerprint}";
        setcookie($this->rememberCookie, $payload . '.' . $this->sign($payload), [
            'expires' => $expires, 'path' => '/', 'secure' => $this->isSecure(),
            'httponly' => true, 'samesite' => 'Lax',
        ]);
    }

    protected function clearRememberCookie(): void {
        setcookie($this->rememberCookie, '', [
            'expires' => time() - 3600, 'path' => '/', 'secure' => $this->isSecure(),
            'httponly' => true, 'samesite' => 'Lax',
        ]);
        unset($_COOKIE[$this->rememberCookie]);
    }

    protected function sign(string $payload): string {
        $key = (string)config('app.key', '');
        if ($key === '') $key = hash('sha256', base_path() . (string)config('system.mail.password', ''));
        return hash_hmac('sha256', $payload, $key);
    }

    protected function passwordFingerprint(string $hash): string {
        return substr(hash('sha256', $hash), 0, 24);
    }

    protected function isSecure(): bool {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }




}
