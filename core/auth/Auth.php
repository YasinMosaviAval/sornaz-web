<?php

namespace Core\auth;

use Core\database\DB;
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
        if ($sessionId) {
            $user = $this->users->find((int)$sessionId);
            $fingerprint = (string)session()->get('_auth_password_fingerprint', '');
            if (!$user || !$this->accountAllowed($user) || $fingerprint === ''
                || !hash_equals($fingerprint, $this->passwordFingerprint((string)$user['password']))) {
                $this->clearLocalAuthentication();
                return null;
            }
            $rememberToken = (string)session()->get('_auth_remember_token', '');
            if ($rememberToken !== '' && !(new \Modules\System\Services\AccountSecurityStore())->valid($rememberToken, (int)$sessionId)) {
                $this->clearLocalAuthentication(); return null;
            }
            $phpSessionId=session_id();
            if($phpSessionId!==''&&DB::table('tracking_user_sessions')->where('session_id',$phpSessionId)->whereNotNull('auth_revoked_at')->first()){
                $this->clearLocalAuthentication();return null;
            }
            return (int)$sessionId;
        }
        return $this->restoreRememberedUser();
    }


    public function login(int $userId, bool $remember = false): void {
        $user = $this->users->find($userId);
        if (!$user || !$this->accountAllowed($user)) throw new \RuntimeException('حساب کاربری فعال نیست.');
        $previous = (string)session()->get('_auth_remember_token', '');
        if ($previous !== '') (new \Modules\System\Services\AccountSecurityStore())->revoke($previous);
        if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
        session()->forget('_auth_remember_token');
        session()->put($this->sessionKey, $userId);
        session()->put('_auth_password_fingerprint', $this->passwordFingerprint((string)$user['password']));
        if ($remember) $this->setRememberCookie($userId);
        else $this->clearRememberCookie();
    }


    public function logout(): void {
        $token = (string)session()->get('_auth_remember_token', '');
        if ($token === '') $token = explode('.', (string)($_COOKIE[$this->rememberCookie] ?? ''))[3] ?? '';
        if (preg_match('/^[a-f0-9]{64}$/D', $token)) (new \Modules\System\Services\AccountSecurityStore())->revoke($token);
        $locale = session()->get('locale', 'fa');
        session()->flush();
        session()->put('locale', $locale);
        if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
        $this->clearRememberCookie();
    }

    protected function restoreRememberedUser(): ?int {
        $cookie = $_COOKIE[$this->rememberCookie] ?? '';
        $parts = explode('.', $cookie);
        if (count($parts) !== 5) { if ($cookie !== '') $this->clearRememberCookie(); return null; }
        [$userId, $expires, $fingerprint, $token, $signature] = $parts;
        if (!ctype_digit($userId) || !ctype_digit($expires) || (int)$expires <= time()) {
            $this->clearRememberCookie();
            return null;
        }
        if (!preg_match('/^[a-f0-9]{64}$/D', $token)) return null;
        $payload = "{$userId}.{$expires}.{$fingerprint}.{$token}";
        if (!hash_equals($this->sign($payload), $signature)) {
            $this->clearRememberCookie();
            return null;
        }
        $user = $this->users->find((int)$userId);
        if (!$user || !$this->accountAllowed($user) || !hash_equals($fingerprint, $this->passwordFingerprint((string)($user['password'] ?? '')))
            || !(new \Modules\System\Services\AccountSecurityStore())->valid($token, (int)$userId)) {
            $this->clearRememberCookie();
            return null;
        }
        if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
        session()->put($this->sessionKey, (int)$userId);
        session()->put('_auth_password_fingerprint', $fingerprint);
        session()->put('_auth_remember_token', $token);
        return (int)$userId;
    }

    protected function setRememberCookie(int $userId): void {
        $user = $this->users->find($userId);
        if (!$user) return;
        $expires = time() + 60 * 60 * 24 * 30;
        $fingerprint = $this->passwordFingerprint((string)$user['password']);
        $token = (new \Modules\System\Services\AccountSecurityStore())->issue($userId, $expires);
        session()->put('_auth_remember_token', $token);
        $payload = "{$userId}.{$expires}.{$fingerprint}.{$token}";
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

    private function accountAllowed(array $user): bool {
        return in_array($user['status'] ?? '', ['active','approved'], true) && empty($user['deleted_at']);
    }

    private function clearLocalAuthentication(): void {
        session()->forget($this->sessionKey);
        session()->forget('_auth_password_fingerprint');
        session()->forget('_auth_remember_token');
        $this->clearRememberCookie();
        if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
    }

    protected function isSecure(): bool {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || parse_url((string)env('APP_URL', ''), PHP_URL_SCHEME) === 'https';
    }




}
