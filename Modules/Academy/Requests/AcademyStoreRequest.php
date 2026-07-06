<?php

namespace Modules\Academy\Requests;

use Core\Validation\ValidationException;
use Core\Validation\Validator;

class AcademyStoreRequest {
// class AcademyStoreRequest extends FormRequest {


    protected array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }



    public function authorize(): bool {
        return true;
    }


    public function rules(): array {
        return [
            'username' => 'required|min:3|max:100|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone',
            'status' => 'required|in:0,1',
            'locale' => 'nullable|max:10',
            'timezone' => 'nullable|max:100',
        ];
    }


    public function messages(): array {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.unique' => 'نام کاربری تکراری است.',
            'email.email' => 'ایمیل معتبر نیست.',
            'email.unique' => 'ایمیل قبلاً ثبت شده است.',
            'phone.unique' => 'شماره موبایل قبلاً ثبت شده است.',
        ];
    }

    // public function validated(): array {
    //     $data = parent::validated();
    //     $data['type'] = 'academy';
    //     return $data;
    // }



    public function validate(): array {
        $validator = new Validator();
        $rules = [
            'username' => 'required|min:3',
            'email' => 'email',
        ];
        if (!$validator->validate($this->data, $rules)) {
            throw new ValidationException($validator->errors());
        }
        $this->data['type'] = 'academy';
        return $this->data;
    }

}



