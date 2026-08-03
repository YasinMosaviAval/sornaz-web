<?php

namespace Core\validation\Rules;

use Core\validation\Rule;

class EmailRule implements Rule {


    public function validate(string $field, mixed $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }


    public function message(string $field): string {
        return "{$field} must be a valid email.";
    }



}