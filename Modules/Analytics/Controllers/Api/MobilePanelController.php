<?php
namespace Modules\Analytics\Controllers\Api;

use Core\http\ResponseFactory;
use Core\http\Request;
use Core\router\Router;
use Modules\System\Services\MobileAuthTokenService;
use Modules\Analytics\Services\MobilePanelAccess;
use Modules\Analytics\Services\MobilePanelCatalog;

/** Native clients invoke an explicit catalog of existing application operations.
 * No HTML, web session, arbitrary URL or client-supplied role is accepted.
 */
final class MobilePanelController {
    public function __construct(private MobileAuthTokenService $tokens, private MobilePanelAccess $access, private MobilePanelCatalog $catalog) {}

    public function index() {
        return $this->authenticated(function(array $user) {
            $capabilities = $this->access->capabilities($user);
            $sections = array_filter($this->catalog->sections(), fn(array $section) => empty($section['hidden']) && ($capabilities[$section['access']] ?? false));
            foreach ($sections as &$section) {
                $section['actions'] = array_filter($section['actions'], fn(array $action) => empty($action['admin']) || $capabilities['admin']);
            } unset($section);
            return ResponseFactory::json(['sections'=>array_values($sections)]);
        });
    }

    public function execute(string $section, string $operation) {
        return $this->authenticated(function(array $user) use ($section, $operation) {
            $definition = $this->catalog->sections()[$section] ?? null;
            $action = $definition['actions'][$operation] ?? null;
            if (!$definition || !$action) return $this->error(404);
            $capabilities = $this->access->capabilities($user);
            if (empty($capabilities[$definition['access']]) || (!empty($action['admin']) && !$capabilities['admin'])) return $this->error(403);
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $action['method']) return $this->error(405);
            $valid = true;
            $path = preg_replace_callback('/\{(\w+)\}/', function(array $match) use (&$valid) {
                $value = (string)($_GET[$match[1]] ?? '');
                $pattern = $match[1] === 'value' ? '/^[a-z][a-z0-9_]{1,49}$/D' : '/^[1-9][0-9]*$/D';
                if (!preg_match($pattern, $value)) $valid = false;
                return $value;
            }, $action['path']);
            if (!$valid) return $this->error(422);
            $route = Router::dispatch($action['method'], $path);
            if (!$route || !is_array($route['action'])) return $this->error(404);
            $originalUri = $_SERVER['REQUEST_URI'] ?? '';
            $originalPost = $_POST;
            $originalRequest = $_REQUEST;
            if ($action['method'] === 'POST' && isset($_POST['payload_b64'])) {
                $raw = base64_decode((string)$_POST['payload_b64'], true);
                $payload = $raw === false ? null : json_decode($raw, true);
                if (!is_array($payload)) return $this->error(422);
                $_POST = array_replace($_POST, $payload);
                $_REQUEST = array_replace($_REQUEST, $_POST);
            }
            $_SERVER['REQUEST_URI'] = $path;
            try {
                $request = new Request();
                $next = function($request) use ($route) {
                    [$class, $method] = $route['action'];
                    return app()->container()->make($class)->$method(...array_values($route['params']));
                };
                $middlewareMap = require base_path('config/middleware.php');
                foreach (array_reverse($route['middlewares']) as $alias) {
                    // CSRF protects cookie authentication. This endpoint accepts only
                    // a validated Authorization bearer and restores the prior session.
                    if ($alias === 'csrf') continue;
                    if (!isset($middlewareMap[$alias])) return $this->error(403);
                    $middleware = app()->container()->make($middlewareMap[$alias]);
                    $downstream = $next;
                    $next = fn($request) => $middleware->handle($request, $downstream);
                }
                return $next($request);
            } finally { $_SERVER['REQUEST_URI'] = $originalUri; $_POST = $originalPost; $_REQUEST = $originalRequest; }
        });
    }

    private function authenticated(callable $callback) {
        header('Cache-Control: no-store, private');
        $user = $this->tokens->userFromRequest();
        if (!$user) return $this->error(401);
        $previous = $_SESSION;
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $previousLocale = app()->getLocale();
        $_SESSION['_auth_user'] = (int)$user['user_id'];
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        app()->setLocale(str_starts_with(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 'en') ? 'en' : 'fa');
        try { return $callback($user); }
        catch (\Throwable $error) { return $this->error(500); }
        finally { $_SESSION = $previous; $_SERVER['HTTP_ACCEPT'] = $accept; app()->setLocale($previousLocale); }
    }
    private function error(int $status) {
        return ResponseFactory::json(['success'=>false,'message'=>match($status) {
            401=>'برای ادامه وارد حساب شوید.', 403=>'دسترسی لازم برای این بخش را ندارید.',
            404=>'این بخش پیدا نشد.', 422=>'اطلاعات را بررسی کنید.', default=>'انجام عملیات ناموفق بود. دوباره تلاش کنید.'
        }], $status);
    }
}
