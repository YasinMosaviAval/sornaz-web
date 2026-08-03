<?php

namespace Core\validation\Rules;

use Core\validation\Rule;

class MaxRule implements Rule {


    public function __construct(protected int $max) {
    }


    public function validate(string $field, mixed $value): bool {
        return mb_strlen((string)$value) <= $this->max;
    }


    public function message(string $field): string {
        return "{$field} نباید بیشتر از {$this->max} کاراکتر باشد.";
    }



}