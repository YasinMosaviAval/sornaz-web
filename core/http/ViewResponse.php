<?php

namespace Core\Http;

use RuntimeException;

class ViewResponse implements ResponseInterface
{
    public function __construct(
        protected string $view,
        protected array $data = []
    ) {
    }

    public function send(): void
    {
        extract($this->data);

        include $this->resolveViewPath();
    }

    protected function resolveViewPath(): string
    {
        /*
        |--------------------------------------------------------------------------
        | Module View
        |--------------------------------------------------------------------------
        |
        | Academy::index
        | Academy::teacher.profile
        |
        */

        if (str_contains($this->view, '::')) {

            [$module, $view] = explode(
                '::',
                $this->view,
                2
            );

            $view = str_replace(
                '.',
                DIRECTORY_SEPARATOR,
                $view
            );

            $path = base_path(
                "Modules/{$module}/Resources/Views/{$view}.php"
            );

            if (file_exists($path)) {
                return $path;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Default Resources View
        |--------------------------------------------------------------------------
        */

        $view = str_replace(
            '.',
            DIRECTORY_SEPARATOR,
            $this->view
        );

        $path = base_path(
            "resources/views/{$view}.php"
        );

        if (file_exists($path)) {
            return $path;
        }

        throw new RuntimeException(
            "View [{$this->view}] not found."
        );
    }
}