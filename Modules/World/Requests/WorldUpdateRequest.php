<?php

namespace Modules\World\Requests;

use Core\validation\FormRequest;

class WorldUpdateRequest extends FormRequest {

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
