<?php
namespace Modules\System\Middleware;

use Core\http\Request;
use Core\http\ResponseFactory;
use Modules\System\Services\AccountSecurityStore;

final class AuthRateLimitMiddleware
{
    public function handle(Request $request, callable $next)
    {
        $path = $request->uri();
        $login = str_ends_with($path, '/login');
        $send = str_ends_with($path, '/send-otp');
        $group = str_ends_with($path, '/contact') ? 'contact' : ($login ? 'login' : ($send ? 'otp-send' : 'auth-action'));
        // Only use the connection address; arbitrary forwarded headers are untrusted.
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $method = in_array($_POST['register_method'] ?? '', ['email', 'phone'], true) ? $_POST['register_method'] : 'email';
        $value = $_POST['identifier'] ?? $_POST['destination'] ?? $_POST[$method] ?? '';
        $target = is_scalar($value) ? mb_strtolower(trim((string) $value)) : '';
        if (!$login && (($_POST['method'] ?? $method) === 'phone')) {
            $target = preg_replace('/\D+/', '', $target);
        }
        $rules = [[$group . ':ip:' . $ip, $send ? 20 : 60, 900]];
        if ($target !== '') {
            $rules[] = [$group . ':target:' . $target, $send ? 5 : 30, 900];
            if ($send) {
                $rules[] = [$group . ':cooldown:' . $target, 1, 60];
            }
        }
        try {
            $store = new AccountSecurityStore();
            foreach ($rules as [$key,$limit,$seconds]) {
                if (!$store->allow($key, $limit, $seconds)) {
                    header('Retry-After: ' . $seconds);
                    return ResponseFactory::json(['success' => false, 'message' => 'تعداد درخواست‌ها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'retry_after' => $seconds], 429);
                }
            }
        } catch (\Throwable $e) {
            error_log('Account security storage unavailable: ' . $e->getMessage());
            return ResponseFactory::json(['success' => false, 'message' => 'سرویس ورود موقتاً در دسترس نیست.'], 503);
        }
        return $next($request);
    }
}
