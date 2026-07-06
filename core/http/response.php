<?php

namespace Core\Http;

class Response
{
    public function send(string $content = ''): void
    {
        echo $content;
    }

    public function json(mixed $data): void
    {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
