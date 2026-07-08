<?php

namespace Core\View;

class View {


    protected string $view;
    protected array $data;
    protected ?string $layout = null;
    protected ?string $title = null;
    protected mixed $breadcrumb = null;
    protected mixed $toolbar = null;
    protected ?string $resolvedPath = null;


    public function __construct(string $view, array $data = []) {
        $this->view = $view;
        $this->data = $data;
    }



    protected function resolveViewPath(): string {
        if ($this->resolvedPath !== null) {
            return $this->resolvedPath;
        }

        /*
        |--------------------------------------------------------------------------
        | Module View
        |--------------------------------------------------------------------------
        */

        if (str_contains($this->view, '::')) {
            [$module, $view] = explode('::', $this->view, 2);
            $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
            $path = base_path("Modules/{$module}/Resources/Views/{$view}.php");
            if (file_exists($path)) {
                return $this->resolvedPath = $path;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | resources/views
        |--------------------------------------------------------------------------
        */

        $view = str_replace('.', DIRECTORY_SEPARATOR, $this->view);
        $path = base_path("resources/views/{$view}.php");
        if (file_exists($path)) {
            return $this->resolvedPath = $path;
        }

        /*
        |--------------------------------------------------------------------------
        | legacy views
        |--------------------------------------------------------------------------
        */

        $path = base_path("views/{$view}.php");
        if (file_exists($path)) {
            return $this->resolvedPath = $path;
        }
        throw new \RuntimeException("View [{$this->view}] not found.");
    }



    public function render(): string {
        $path = $this->resolveViewPath();
        extract($this->data);
        ob_start();
        require $path;
        $content = ob_get_clean();
        if (!$this->layout) {
            return $content;
        }
        $layoutPath = base_path("resources/views/layouts/{$this->layout}.php");
        if (!file_exists($layoutPath)) {
            $layoutPath = base_path("views/layouts/{$this->layout}.php");
        }
        ob_start();
        $title = $this->title;
        $breadcrumb = $this->breadcrumb;
        $toolbar = $this->toolbar;
        require $layoutPath;
        return ob_get_clean();
    }



    public function layout(string $layout): static {
        $this->layout = $layout;
        return $this;
    }



    public static function component(string $view, array $data = []): void {
        $path = base_path('resources/views/components/' . str_replace('.', '/', $view) . '.php');
        if (!file_exists($path)) {
            $path = base_path('views/components/' . str_replace('.', '/', $view) . '.php');
        }
        if (!file_exists($path)) {
            throw new \Exception("Component [$view] not found.");
        }
        extract($data);
        require $path;
    }



    public function title(string $title): static {
        $this->title = $title;
        return $this;
    }



    public function breadcrumb(mixed $breadcrumb): static {
        $this->breadcrumb = $breadcrumb;
        return $this;
    }



    public function toolbar(mixed $toolbar): static {
        $this->toolbar = $toolbar;
        return $this;
    }



    public static function exists(string $view): bool {
        return file_exists(
            base_path('views/' . str_replace('.', '/', $view) . '.php')
        );
    }



    public static function componentExists(string $component): bool {
        return file_exists(
            base_path('views/components/' . str_replace('.', '/', $component) . '.php')
        );
    }








}