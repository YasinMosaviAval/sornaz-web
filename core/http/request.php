<?php

namespace Core\http;

use Core\validation\ValidationException;
use Core\validation\Validator;

class Request {


    public function method() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        return $method;
    }


    public function uri() {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $script = dirname($_SERVER['SCRIPT_NAME']);
        if ($script !== '/') {
            $uri = str_replace($script, '', $uri);
        }
        return $uri ?: '/';
    }


    public function input(string $key, mixed $default = null) {
        return $_REQUEST[$key] ?? $default;
    }


    public function all() {
        return $_REQUEST;
    }


    public function validate(array $rules): array {
        $validator = new Validator();
        if (!$validator->validate($_POST, $rules)) {
            throw new ValidationException(
                $validator->errors()
            );
        }
        return $_POST;
    }

}