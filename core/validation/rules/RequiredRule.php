<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class RequiredRule implements Rule
{
    public function validate(
        string $field,
        mixed $value
    ): bool {

        return !(
            $value === null
            || $value === ''
        );
    }

    public function message(
        string $field
    ): string {

        return "{$field} is required.";
    }
}