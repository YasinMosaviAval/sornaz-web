<?php

namespace Modules\Analytics\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\ArticleApiService;

class ArticleController
{
    public function __construct(private ArticleApiService $service) {}

    public function index()
    {
        return $this->run(fn() => $this->service->articles($_GET, locale()));
    }

    public function categories()
    {
        return $this->run(fn() => $this->service->categories(locale()));
    }

    public function related(int $id)
    {
        return $this->run(fn() => $this->service->related($id, $_GET, locale()));
    }

    public function comments(int $id)
    {
        return $this->run(fn() => $this->service->comments($id, $_GET, locale()));
    }

    public function storeComment(int $id)
    {
        $payload = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($payload)) $payload = $_POST;
        return $this->run(fn() => [
            'success' => true,
            'message' => 'نظر شما ثبت شد و پس از بررسی نمایش داده می‌شود.',
            'id' => $this->service->storeComment($id, $payload, locale()),
        ], 201);
    }

    private function run(callable $callback, int $status = 200)
    {
        try {
            return ResponseFactory::json($callback(), $status);
        } catch (\Throwable $e) {
            $notFound = str_contains($e->getMessage(), 'یافت نشد');
            return ResponseFactory::json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $notFound ? 404 : 422);
        }
    }
}
