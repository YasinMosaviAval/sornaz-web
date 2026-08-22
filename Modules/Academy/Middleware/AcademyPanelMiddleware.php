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
        $path = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
        if ($path === '/analytics/admin-panel' || str_starts_with($path, '/analytics/admin-account') || $path === '/analytics/admin-dashboard') {
            return $next($request);
        }
        if (SiteAdminAccess::allows($user)) return $next($request);
        $userId = (int)$user['user_id'];
        $academy = DB::table('academies')->where('user_id', $userId)->whereNull('deleted_at')->first()
            ?: DB::table('academies')->where('created_by', $userId)->whereNull('deleted_at')->first();
        $branch = DB::table('academy_branches')->where('user_id', $userId)->whereNull('deleted_at')->first();
        $manager = DB::table('academy_branch_members')
            ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
            ->where('academy_branch_members.user_id', $userId)->whereIn('academy_branch_member_roles.role_id', [7, 16])
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->first();
        if (!$academy && !$branch && !$manager) {
            if ($this->expectsJson()) return ResponseFactory::json(['success'=>false,'message'=>'دسترسی لازم برای این بخش را ندارید.'], 403);
            abort(403, 'دسترسی لازم برای این بخش را ندارید.');
        }
        return $next($request);
    }

    private function expectsJson(): bool
    {
        return str_contains(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
            || strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }
}
