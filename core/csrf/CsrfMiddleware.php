<?php

namespace Core\Csrf;

use Core\Http\Request;
use Exception;

class CsrfMiddleware {


    public function handle(Request $request, callable $next) {
        if ($request->method() === 'POST') {
            $token = $_POST['_token'] ?? null;
            $csrf = app()->container()->make(Csrf::class);
            if (!$csrf->verify($token)) {
                throw new Exception('CSRF Token Mismatch');
            }
        }
        return $next($request);
    }


}