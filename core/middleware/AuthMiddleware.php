<?php

namespace Core\middleware;

use Core\http\Request;
use Core\http\ResponseFactory;

class AuthMiddleware implements MiddlewareInterface {

    public function handle(Request $request, callable $next) {
        if (!auth()->check()) {
            $expectsJson = str_contains(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
                || strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
            if ($expectsJson) {
                return ResponseFactory::json(['success'=>false,'message'=>'برای دریافت لینک دعوت ابتدا وارد حساب خود شوید.'],401);
            }
            return redirect('/system/login');
        }
        return $next($request);
    }


}
