<?php

namespace Core\Middleware;

use Core\Http\Request;

class AuthMiddleware implements MiddlewareInterface {

    public function handle(Request $request, callable $next) {
        echo "Auth Passed<br>";
        return $next($request);
    }


}