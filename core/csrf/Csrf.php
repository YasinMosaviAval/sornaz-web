<?php

namespace Core\Csrf;

class Csrf {

    public function token(): string {
        if (!session()->has('_csrf_token')) {
            session()->put('_csrf_token', bin2hex(random_bytes(32)));
        }
        return session()->get('_csrf_token');
    }


    public function verify(?string $token): bool {
        return hash_equals(session()->get('_csrf_token', ''), $token ?? '');
    }



}