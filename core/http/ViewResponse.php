<?php

namespace Core\Http;

class ViewResponse implements ResponseInterface
{
    public function __construct(
        protected string $view,
        protected array $data = []
    ) {}

    public function send(): void
    {
        extract($this->data);

        include base_path("resources/views/{$this->view}.php");
    }
}