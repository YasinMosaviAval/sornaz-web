<?php

namespace Modules\Academy\Requests;

use Core\Validation\FormRequest;

class AcademyStoreRequest extends FormRequest {



    public function authorize(): bool {
        return true;
    }



    public function rules(): array {
        return [
            'username' => 'required|min:3|max:100|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
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



    public function validated(): array {
        $data = parent::validated();
        $data['type']='academy';
        return $data;
    }





}



