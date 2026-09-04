<?php

namespace Core\validation\Rules;

use Core\validation\Rule;

class NumericRule implements Rule {



    public function validate(string $field, mixed $value): bool {
        return is_numeric($value);
    }



    public function message(string $field): string {
        return "{$field} باید عدد باشد.";
    }





}