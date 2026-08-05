<?php

namespace Modules\Media\Requests;

use Core\validation\FormRequest;

class MediaUpdateRequest extends FormRequest {

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
