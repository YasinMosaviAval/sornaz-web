<?php

namespace Modules\System\Middleware;

use Core\http\Request;
use Modules\System\Services\SiteAdminAccess;

class SiteAdminMiddleware {
    public function handle(Request $request, callable $next) {
        $user = auth()->user();
        if (!$user) return redirect('/system/login');
        if (!SiteAdminAccess::allows($user)) abort(403, 'این عملیات فقط برای مدیر سایت مجاز است.');
        return $next($request);
    }
}
