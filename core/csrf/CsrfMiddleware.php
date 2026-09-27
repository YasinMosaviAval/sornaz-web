<?php

namespace Core\csrf;

use Core\http\Request;
use Exception;

class CsrfMiddleware {


    public function handle(Request $request, callable $next) {
        if (in_array(strtoupper($request->method()), ['POST','PUT','PATCH','DELETE'], true)) {
            $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            $csrf = app()->container()->make(Csrf::class);
            if (!is_string($token) || !$csrf->verify($token)) {
                return \Core\http\ResponseFactory::json(['success'=>false,'message'=>'نشست فرم معتبر نیست. صفحه را تازه‌سازی و دوباره تلاش کنید.'], 419);
            }
        }
        return $next($request);
    }



}
