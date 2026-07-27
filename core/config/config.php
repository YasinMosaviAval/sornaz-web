<?php

namespace Core\Config;

class Config {

    protected static array $items = [];


    public static function load(string $path) {
        foreach (glob($path.'/*.php') as $file) {
            $key = basename($file, '.php');
            static::$items[$key] = require $file;
        }
    }


    public static function get(string $key) {
        $keys = explode('.', $key);
        $value = static::$items;
        foreach ($keys as $segment) {
            $value = $value[$segment] ?? null;
        }
        return $value;
    }



}