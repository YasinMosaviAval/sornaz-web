<?php
namespace Modules\System\Requests;

use Core\validation\FormRequest;

class UserStoreRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'username'  => 'required|min:3|max:100|unique:users,username',
            'email'     => 'nullable|email|unique:users,email',
            'phone'     => 'nullable|unique:users,phone',
            'password'  => 'required|min:6',
            // 'full_name' => 'required|max:255',
        ];
    }

    public function messages(): array {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.unique'   => 'این نام کاربری قبلاً ثبت شده است.',
            'email.unique'      => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'full_name.required'=> 'نام و نام‌خانوادگی الزامی است.',
        ];
    }

    public function attributes(): array {
        return [];
    }
}