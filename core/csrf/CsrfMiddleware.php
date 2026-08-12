<?php

namespace Core\csrf;

use Core\http\Request;
use Exception;

class CsrfMiddleware {


    public function handle(Request $request, callable $next) {
        if ($request->method() === 'POST') {
            $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            $csrf = app()->container()->make(Csrf::class);
            if (!$csrf->verify($token) && !$this->isSameOriginAjax()) {
                throw new Exception('CSRF Token Mismatch');
            }
        }
        return $next($request);
    }

    private function isSameOriginAjax(): bool {
        if (strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) !== 'xmlhttprequest') return false;
        $source = (string)($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '');
        $sourceHost = strtolower((string)parse_url($source, PHP_URL_HOST));
        $requestHost = strtolower(preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
        return $sourceHost !== '' && hash_equals($requestHost, $sourceHost);
    }


}
