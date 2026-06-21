<?php

namespace Core\Http;

use Core\View\View;

class Response {


    public function send(mixed $content): void {
        if ($content instanceof RedirectResponse) {
            $content->send();
            return;
        }

        if ($content instanceof View) {
            echo $content->render();
            return;
        }

        echo $content;
    }
}