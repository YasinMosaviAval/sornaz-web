<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class InRule implements Rule {


    public function __construct(protected array $items) {
    }

    public function validate(string $field, mixed $value): bool {
        return in_array($value, $this->items, true);
    }

    public function message(string $field): string {
        return "{$field} مقدار معتبری ندارد.";
    }



}