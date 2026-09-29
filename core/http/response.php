<?php

namespace Core\http;

class Response implements ResponseInterface {

    protected string $content = '';

    public function __construct(string $content = '', protected int $status = 200) {
        $this->content = $content;
    }

    public function send(): void {
        http_response_code($this->status);
        echo $this->content;
    }

    public function json(mixed $data): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
    }



}
