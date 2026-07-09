<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class ConfirmedRule implements Rule {


    protected array $data = [];



    public function setData(array $data): void {
        $this->data = $data;
    }



    public function validate(string $field, mixed $value): bool {
        return ($this->data[$field.'_confirmation'] ?? null) === $value;
    }



    public function message(string $field): string {
        return "{$field} با تکرار آن یکسان نیست.";
    }




}