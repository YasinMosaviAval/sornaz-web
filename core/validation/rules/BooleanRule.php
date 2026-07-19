<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class BooleanRule implements Rule {



    public function validate(string $field, mixed $value): bool {
        return in_array(
            $value,
            [true, false, 0, 1, '0', '1'],
            true
        );
    }



    public function message(string $field): string {
        return "{$field} باید مقدار منطقی باشد.";
    }




}