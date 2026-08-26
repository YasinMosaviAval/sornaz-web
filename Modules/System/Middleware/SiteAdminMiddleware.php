<?php

namespace Modules\System\Middleware;

use Core\http\Request;
use Core\http\ResponseFactory;
use Modules\System\Services\SiteAdminAccess;

class SiteAdminMiddleware {
    public function handle(Request $request, callable $next) {
        $user = auth()->user();
        if (!$user) return redirect('/system/login');
        if (!SiteAdminAccess::allows($user)) {
            $expectsJson = str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
                || strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
            if ($expectsJson) return ResponseFactory::json(['success' => false, 'message' => 'این عملیات فقط برای مدیر سایت مجاز است.'], 403);
            abort(403, 'این عملیات فقط برای مدیر سایت مجاز است.');
        }
        return $next($request);
    }
}
