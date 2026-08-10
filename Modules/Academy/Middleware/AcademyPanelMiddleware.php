<?php

namespace Modules\Academy\Middleware;

use Core\database\DB;
use Core\http\Request;
use Modules\System\Services\SiteAdminAccess;

class AcademyPanelMiddleware {
    public function handle(Request $request, callable $next) {
        $user = auth()->user();
        if (!$user) return redirect('/system/login');
        if (SiteAdminAccess::allows($user)) return $next($request);
        if (($user['type'] ?? null) !== 'academy') abort(403, 'دسترسی به پنل مدیریت مجاز نیست.');

        $academy = DB::table('academies')
            ->where('user_id', (int)$user['user_id'])
            ->whereNull('deleted_at')
            ->first();
        if (!$academy) abort(403, 'آموزشگاه مرتبط با این حساب یافت نشد.');
        return $next($request);
    }
}
