<?php

namespace Modules\Blog\Requests;

class BlogStoreRequest {

    /**
     * آیا کاربر اجازه اجرای این درخواست را دارد؟
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * قوانین اعتبارسنجی
     */
    public function rules(): array
    {
        return [

            'slug' => [
                'required',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
            ],

            'visibility' => [
                'required',
            ],

            'category_id' => [
                'required',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'content' => [
                'required',
                'string',
            ],

        ];
    }

    /**
     * پیام‌های اعتبارسنجی
     */
    public function messages(): array
    {
        return [

            'slug.required' => 'اسلاگ الزامی است.',

            'title.required' => 'عنوان الزامی است.',

            'content.required' => 'متن مقاله الزامی است.',

        ];
    }


    /**
     * نام‌های فارسی فیلدها
     */
    public function attributes(): array {
        return [

        ];
    }





}