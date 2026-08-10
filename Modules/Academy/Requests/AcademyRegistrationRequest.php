<?php

namespace Modules\Academy\Requests;

use Core\validation\FormRequest;

class AcademyRegistrationRequest extends FormRequest {
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'username' => 'required|min:3|max:100|unique:users,username',
            'register_method' => 'required|in:email,phone',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone',
            'password' => 'required|min:8|password_strength',
            'password2' => 'required|same:password',
            'academy_name' => 'required|max:255',
            'slogan' => 'nullable|max:255',
            'short_description' => 'nullable|max:500',
            'biography' => 'nullable|max:5000',
        ];
    }

    public function messages(): array {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.unique' => 'این نام کاربری قبلاً ثبت شده است.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'phone.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'password.password_strength' => 'رمز عبور بسیار ضعیف است؛ حداقل ۳ مورد از معیارهای قدرت رمز را رعایت کنید.',
            'password2.same' => 'رمز عبور و تکرار آن یکسان نیست.',
            'academy_name.required' => 'نام آموزشگاه الزامی است.',
        ];
    }
}
