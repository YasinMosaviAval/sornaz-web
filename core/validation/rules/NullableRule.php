<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class NullableRule implements Rule {

    public function validate(string $field, mixed $value): bool {
        return true;
    }

    public function message(string $field): string {
        return '';
    }
}