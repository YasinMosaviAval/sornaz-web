<?php

namespace Modules\Profile\Requests;

use Core\validation\FormRequest;

class ProfileUpdateRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [

        ];
    }

    public function messages(): array {
        return [

        ];
    }

    public function attributes(): array {
        return [

        ];
    }

}
