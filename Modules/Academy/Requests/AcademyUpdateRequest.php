<?php

namespace Modules\Academy\Requests;

use Core\Validation\Validator;
use Core\Validation\ValidationException;

class AcademyUpdateRequest
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(): array
    {
        $validator = new Validator();

        $rules = [
            'username' => 'required|min:3',
            'email'    => 'email',
        ];

        if (!$validator->validate($this->data, $rules)) {
            throw new ValidationException(
                $validator->errors()
            );
        }

        return $this->data;
    }
}