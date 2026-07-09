<?php

namespace Modules\Academy\Requests;

use Core\Validation\ValidationException;
use Core\Validation\Validator;

class AcademyStoreRequest {


    protected array $data;



    public function __construct(array $data) {
        $this->data = $data;
    }



    public function authorize(): bool {
        return true;
    }



    public function rules(): array {
        // return [
        //     'username'=>'required',
        //     'email'=>'email',
        //     'status'=>'required',
        // ];
        return [
            // 'username' => 'required|min:3|max:100|unique:users,username',
            'username' => 'required|min:3|max:100',
            'email'    => 'nullable|email',
            // 'email'    => 'nullable|email|unique:users,email',
            'phone'    => 'nullable|unique:users,phone',
            'status'   => 'required|in:approved,pending',
            'locale'   => 'nullable|max:10',
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



    public function validate(): array {
        $validator = new Validator();
        if (!$validator->validate($this->data, $this->rules(), $this->messages())) {
            throw new ValidationException($validator->errors());
        }
        $this->data['type'] = 'academy';
        return $this->data;
    }







}



