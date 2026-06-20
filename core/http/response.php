<?php

namespace Core\Http;

class Response {
    public function send(mixed $content) {
        echo $content;
    }
}