<?php

namespace Modules\Academy\Middleware;

use Core\database\DB;
use Core\http\Request;
use Core\http\ResponseFactory;
use Modules\System\Services\SiteAdminAccess;

class AcademyPanelMiddleware {
    public function handle(Request $request, callable $next) {
        $user = auth()->user();
        if (!$user) {
            if ($this->expectsJson()) {
                return ResponseFactory::json([
                    'success' => false,
                    'message' => 'نشست شما منقضی شده است. لطفاً دوباره وارد شوید.',
                    'loginUrl' => '/system/login',
                ], 401);
            }
            return redirect('/system/login');
        }
        if (SiteAdminAccess::allows($user)) return $next($request);
        $userId = (int) $user['user_id'];
        $academy = DB::table('academies')->where('user_id', $userId)->whereNull('deleted_at')->first()
            ?: DB::table('academies')->where('created_by', $userId)->whereNull('deleted_at')->first();
        $branch = DB::table('academy_branches')->where('user_id', $userId)->whereNull('deleted_at')->first();
        if (!$academy && !$branch) {
            if ($this->expectsJson()) {
                return ResponseFactory::json([
                    'success' => false,
                    'message' => 'آموزشگاه یا شعبه مرتبط با این حساب یافت نشد.',
                ], 403);
            }
            abort(403, 'آموزشگاه یا شعبه مرتبط با این حساب یافت نشد.');
        }
        return $next($request);
    }

    private function expectsJson(): bool
    {
        return str_contains(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
            || strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }
}
