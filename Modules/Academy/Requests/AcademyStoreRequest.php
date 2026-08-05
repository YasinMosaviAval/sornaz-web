<?php

namespace Modules\Academy\Requests;

use Core\validation\FormRequest;

class AcademyStoreRequest extends FormRequest {

    /**
     * آیا کاربر اجازه اجرای این درخواست را دارد؟
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * قوانین اعتبارسنجی
     */
    public function rules(): array {
        return [

        ];
    }

    /**
     * پیام‌های اعتبارسنجی
     */
    public function messages(): array {
        return [

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