<?php

namespace Modules\Academy\Requests;

use Core\validation\FormRequest;

class AcademyUpdateRequest extends FormRequest {

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
