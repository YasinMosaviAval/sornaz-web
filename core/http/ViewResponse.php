<?php

namespace Core\Http;

use Core\View\View;

class ViewResponse implements ResponseInterface {


    protected View $view;


    public function __construct(string $view, array $data = []) {
        $this->view = new View($view, $data);
    }

    public function send(): void {
        echo $this->view->render();
    }

    public function layout(string $layout): static {
        $this->view->layout($layout);
        return $this;
    }


    public function title(string $title): static {
        $this->view->title($title);
        return $this;
    }


    public function breadcrumb(mixed $breadcrumb): static {
        $this->view->breadcrumb($breadcrumb);
        return $this;
    }


    public function toolbar(mixed $toolbar): static {
        $this->view->toolbar($toolbar);
        return $this;
    }





}