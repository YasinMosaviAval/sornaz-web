<?php

namespace Core\Http\Resources;

final class MissingValue {

    private static ?self $instance = null;


    private function __construct() {}


    public static function make(): self {
        if (!static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }


    public function __toString(): string {
        return '';
    }


}