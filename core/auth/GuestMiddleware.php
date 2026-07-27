<?php

namespace Core\Auth;

use Core\Http\Request;

class GuestMiddleware {

    public function handle(Request $request, callable $next) {
        if (auth()->check()) {
            return redirect('/');
        }
        return $next($request);
    }



}