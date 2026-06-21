<?php

namespace Core\View;

class View {
    protected string $view;
    protected array $data;
    protected ?string $layout = null;

    public function __construct(
        string $view,
        array $data = []
    ) {
        $this->view = $view;
        $this->data = $data;
    }

    public function render(): string
    {
        $path = base_path(
            'views/' .
            str_replace('.', '/', $this->view)
            . '.php'
        );

        if (!file_exists($path)) {
            throw new \Exception(
                "View [{$this->view}] not found."
            );
        }

        extract($this->data);

        ob_start();

        require $path;

        $content = ob_get_clean();

        if (!$this->layout) {
            return $content;
        }

        $layoutPath = base_path(
            'views/layouts/' .
            $this->layout .
            '.php'
        );

        ob_start();

        require $layoutPath;

        return ob_get_clean();
    }


    public function layout(
        string $layout
    ): static {

        $this->layout = $layout;

        return $this;
    }
}