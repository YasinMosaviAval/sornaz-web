<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class IntegerRule implements Rule {



    public function validate(string $field, mixed $value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }



    public function message(string $field): string {
        return "{$field} باید عدد صحیح باشد.";
    }




}