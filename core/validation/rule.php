<?php

namespace Core\validation;

interface Rule {


    public function validate(
        string $field,
        mixed $value
    ): bool;


    public function message(
        string $field
    ): string;



}