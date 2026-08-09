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
            'register_method' => 'required|in:email,phone',
            'email'     => 'nullable|email|unique:users,email',
            'phone'     => 'nullable|unique:users,phone',
            'password'  => 'required|min:8',
            'password2' => 'required|same:password',
            // 'full_name' => 'required|max:255',
        ];
    }

    public function messages(): array {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.unique'   => 'این نام کاربری قبلاً ثبت شده است.',
            'email.unique'      => 'این ایمیل قبلاً ثبت شده است.',
            'phone.unique'      => 'این شماره موبایل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'password2.same'    => 'رمز عبور و تکرار آن یکسان نیست.',
            'full_name.required'=> 'نام و نام‌خانوادگی الزامی است.',
        ];
    }

    public function attributes(): array {
        return [];
    }
}
