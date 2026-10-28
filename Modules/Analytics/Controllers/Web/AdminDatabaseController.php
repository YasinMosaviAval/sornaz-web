<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminDatabaseService;

final class AdminDatabaseController
{
    public function __construct(private AdminDatabaseService $service) {}

    public function tables() { return $this->run(fn () => ResponseFactory::json(['success' => true, 'data' => $this->service->tables($_GET)])); }
    public function rows() { return $this->run(fn () => ResponseFactory::json(['success' => true, 'data' => $this->service->rows($_GET)])); }
    public function download() { return $this->run(fn () => $this->service->download($_GET)); }

    private function run(callable $work)
    {
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        try {
            $this->service->authorize();
            $bearer = preg_match('/^Bearer\s+/i', (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? ''));
            if (!$bearer && session_status() === PHP_SESSION_ACTIVE && !session_write_close()) throw new \RuntimeException('آزادسازی نشست ناموفق بود؛ دوباره تلاش کنید.', 503);
            return $work();
        }
        catch (\Throwable $error) {
            $safe = $error instanceof \RuntimeException && !($error instanceof \PDOException);
            $status = $safe && in_array($error->getCode(), [401, 403, 404, 409, 422, 503], true) ? $error->getCode() : 500;
            return ResponseFactory::json(['success' => false, 'message' => $safe ? $error->getMessage() : 'خواندن دیتابیس یا تهیه خروجی ناموفق بود. دوباره تلاش کنید.'], $status);
        }
    }
}
