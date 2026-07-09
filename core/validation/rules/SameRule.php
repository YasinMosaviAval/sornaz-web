<?php

namespace Core\Validation\Rules;

use Core\Validation\Rule;

class SameRule implements Rule {


    protected array $data = [];



    public function __construct(protected string $other) {
    }



    public function setData(array $data): void {
        $this->data = $data;
    }



    public function validate(string $field, mixed $value): bool {
        return ($this->data[$this->other] ?? null) === $value;
    }



    public function message(string $field): string {
        return "{$field} با {$this->other} برابر نیست.";
    }




}