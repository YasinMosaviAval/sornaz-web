<?php

namespace Core\Support;

class Env {
    protected static array $data = [];

    public static function load(string $path) {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path);

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line === '') continue;

            if (str_starts_with($line, '#')) continue;

            [$key, $value] = explode('=', $line, 2);

            static::$data[$key] = $value;
        }
    }

    public static function get(
        string $key,
        mixed $default = null
    ) {
        return static::$data[$key] ?? $default;
    }
}