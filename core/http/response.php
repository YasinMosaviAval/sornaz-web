<?php

namespace Core\Http;

class Response implements ResponseInterface
{
    protected string $content = '';

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function send(): void
    {
        echo $this->content;
    }

    public function json(mixed $data): void
    {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
    }
}