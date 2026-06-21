<?php

namespace Core\Validation;

class ValidationException extends \Exception
{
    protected array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation Failed');

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}