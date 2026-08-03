<?php

namespace Core\validation;

use Exception;

abstract class FormRequest {



    protected array $data;



    public function __construct(?array $data = null) {
        $this->data = $data ?? $_POST;
    }



    abstract public function authorize(): bool;



    abstract public function rules(): array;



    public function messages(): array {
        return [];
    }



    public function validated(): array {
        return $this->validate();
    }



    public function validate(): array {
        if (!$this->authorize()) {
            throw new Exception('Unauthorized.');
        }
        $validator = new Validator();
        if (!$validator->validate($this->data, $this->rules(), $this->messages())) {
            throw new ValidationException($validator->errors());
        }
        /*
        |--------------------------------------------------------------------------
        | حذف فیلدهای سیستمی
        |--------------------------------------------------------------------------
        */
        unset($this->data['_method']);
        unset($this->data['_token']);
        return $this->data;
    }


}