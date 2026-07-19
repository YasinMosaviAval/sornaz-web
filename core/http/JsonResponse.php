<?php

namespace Core\Http;

class JsonResponse implements ResponseInterface {
    public function __construct(
        protected mixed $data,
        protected int $status = 200
    ) {}

    public function send(): void {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            [
                'status' => $this->status,
                'data' => $this->data
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}

