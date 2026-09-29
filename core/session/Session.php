<?php

namespace Core\session;

class Session
{
    public static function secureCookies(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        // Local HTTP development must not inherit the production URL's cookie policy.
        if (in_array((string) env('APP_ENV', 'production'), ['local', 'development', 'testing'], true)) {
            return false;
        }
        return parse_url((string) env('APP_URL', ''), PHP_URL_SCHEME) === 'https';
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            $secure = self::secureCookies();
            session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax']);
            $sessionPath = storage_path('sessions');
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0775, true);
            }
            if (is_writable($sessionPath)) {
                session_save_path($sessionPath);
            }
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function safeInput(array $input): array
    {
        foreach ($input as $key => $value) {
            if (preg_match('/password|passwd|token|secret|otp|^code$|^csrf$/i', (string) $key)) {
                unset($input[$key]);
            } elseif (is_array($value)) {
                $input[$key] = self::safeInput($value);
            }
        }
        return $input;
    }

    public function flush(): void
    {
        $_SESSION = [];
    }

    public function destroy(): void
    {
        session_destroy();
    }

    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function peekFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public function clearFlash(): void
    {
        unset($_SESSION['_flash']);
    }
}
