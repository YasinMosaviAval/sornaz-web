<?php

namespace Core\validation\Rules;

use Core\validation\Rule;

class MinRule implements Rule {


    public function __construct(protected int $min) {}


    public function validate(string $field, mixed $value): bool {
        return mb_strlen((string)$value) >= $this->min;
    }


    public function message(string $field): string {
        return "{$field} must be at least {$this->min} characters.";
    }



}