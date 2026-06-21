<?php

namespace Core\Auth;

use Core\Http\Request;

class AuthMiddleware
{
    public function handle(
        Request $request,
        callable $next
    ) {

        if (
            !auth()->check()
        ) {

            return redirect(
                '/login'
            );
        }

        return $next(
            $request
        );
    }
}