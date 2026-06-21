<?php

namespace Core\Session;

class Session {
    public function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public function forget(string $key): void {
        unset($_SESSION[$key]);
    }

    public function flush(): void {
        $_SESSION = [];
    }

    public function destroy(): void {
        session_destroy();
    }


    public function flash(string $key, mixed $value): void {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }


    public function peekFlash(string $key, mixed $default = null): mixed {
        return $_SESSION['_flash'][$key] ?? $default;
    }



    public function clearFlash(): void {
        unset($_SESSION['_flash']);
    }
}