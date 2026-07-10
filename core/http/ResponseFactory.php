<?php

namespace Core\Http;

class ResponseFactory {

    public static function json(mixed $data, int $status = 200): JsonResponse {
        return new JsonResponse($data, $status);
    }

    public static function view(string $view, array $data = []): ViewResponse {
        return new ViewResponse($view, $data);
    }

}