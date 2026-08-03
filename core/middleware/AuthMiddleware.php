<?php

namespace Core\middleware;

use Core\http\Request;

class AuthMiddleware implements MiddlewareInterface {

    public function handle(Request $request, callable $next) {
        echo "Auth Passed<br>";
        return $next($request);
    }


}