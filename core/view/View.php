<?php

namespace Core\view;

use Exception;
use RuntimeException;

class View {


    protected string $view;
    protected array $data;
    protected ?string $layout = null;
    protected ?string $title = null;
    protected mixed $breadcrumb = null;
    protected mixed $toolbar = null;
    protected ?string $resolvedPath = null;
    protected static array $styles = [];
    protected static array $scripts = [];
    protected static ?string $currentModule = null;
    protected static ?string $currentView = null;



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
            // $this->module = $module;
            // $this->viewFile = $view;
            // $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
            // $path = base_path("Modules/{$module}/Resources/Views/{$view}.php");
            self::$currentModule = $module;
            self::$currentView = $view;
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
        throw new RuntimeException("View [{$this->view}] not found.");
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
        $layoutPath = null;
        /*
        |--------------------------------------------------------------------------
        | Module Layout
        |--------------------------------------------------------------------------
        */
        if (self::$currentModule) {
            $moduleLayout = base_path("Modules/" . self::$currentModule . "/Resources/Views/layouts/{$this->layout}.php");
            if (file_exists($moduleLayout)) {
                $layoutPath = $moduleLayout;
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Global Layout
        |--------------------------------------------------------------------------
        */
        if (!$layoutPath) {
            $globalLayout = base_path("resources/views/layouts/{$this->layout}.php");
            if (file_exists($globalLayout)) {
                $layoutPath = $globalLayout;
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Legacy Layout
        |--------------------------------------------------------------------------
        */
        if (!$layoutPath) {
            $legacyLayout = base_path("views/layouts/{$this->layout}.php");
            if (file_exists($legacyLayout)) {
                $layoutPath = $legacyLayout;
            }
        }
        if (!$layoutPath) {
            throw new RuntimeException("Layout [{$this->layout}] not found.");
        }
        ob_start();
        $title = $this->title;
        $breadcrumb = $this->breadcrumb;
        $toolbar = $this->toolbar;
        $slot = $content;
        // $content = $content; // برای سازگاری با Layoutهای قدیمی
        require $layoutPath;
        return ob_get_clean();
    }



    public function layout(string $layout): static {
        $this->layout = $layout;
        return $this;
    }



    public static function component(string $view, array $data = []): void {
        $paths = [];
    /*
    |--------------------------------------------------------------------------
    | Module::component
    |--------------------------------------------------------------------------
    */
        if (str_contains($view, '::')) {
            [$module, $view] = explode('::', $view, 2);
            $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
            $paths[] = base_path("Modules/{$module}/Resources/Views/{$view}.php");
        }
        else {
            $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
            /*
            |--------------------------------------------------------------------------
            | Current Module
            |--------------------------------------------------------------------------
            */
            if (self::$currentModule) {
                $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/partials/{$view}.php");
                $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/sections/{$view}.php");
                $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/components/{$view}.php");
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Global
        |--------------------------------------------------------------------------
        */
        $paths[] = base_path("resources/views/components/{$view}.php");
        $paths[] = base_path("views/components/{$view}.php");
        foreach ($paths as $path) {
            if (file_exists($path)) {
                extract($data);
                require $path;
                return;
            }
        }
        throw new Exception("Component [{$view}] not found.");
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
        return file_exists(base_path('views/' . str_replace('.', '/', $view) . '.php'));
    }



    public static function componentExists(string $view): bool {
        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $paths = [];
        if (self::$currentModule) {
            $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/partials/{$view}.php");
            $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/sections/{$view}.php");
            $paths[] = base_path("Modules/" . self::$currentModule . "/Resources/Views/components/{$view}.php");
        }
        $paths[] = base_path("resources/views/components/{$view}.php");
        $paths[] = base_path("views/components/{$view}.php");
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }
        return false;
    }



    public static function styles(): array {
        return self::$styles;
    }



    public static function scripts(): array {
        return self::$scripts;
    }



    public static function currentModule(): ?string {
        return self::$currentModule;
    }


    public static function currentView(): ?string {
        return self::$currentView;
    }


    public static function pushStyle(string $file): void {
        self::$styles[] = ['module'=>self::$currentModule, 'file'=>$file];
    }



    public static function pushScript(string $file): void {
        self::$scripts[] = ['module'=>self::$currentModule, 'file'=>$file];
    }





}