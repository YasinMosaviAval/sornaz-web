<?php

namespace Core\csrf;

class Csrf {

    public function token(): string {
        if (!session()->has('_csrf_token')) {
            session()->put('_csrf_token', bin2hex(random_bytes(32)));
        }
        return session()->get('_csrf_token');
    }


    public function verify(?string $token): bool {
        $stored = session()->get('_csrf_token', '');
        return is_string($stored) && $stored !== '' && is_string($token) && $token !== '' && hash_equals($stored, $token);
    }



}
