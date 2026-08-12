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
        $userId = (int) $user['user_id'];
        $academy = DB::table('academies')->where('user_id', $userId)->whereNull('deleted_at')->first()
            ?: DB::table('academies')->where('created_by', $userId)->whereNull('deleted_at')->first();
        $branch = DB::table('academy_branches')->where('user_id', $userId)->whereNull('deleted_at')->first();
        if (!$academy && !$branch) abort(403, 'آموزشگاه یا شعبه مرتبط با این حساب یافت نشد.');
        return $next($request);
    }
}
