<?php

namespace Modules\Academy\Requests;

use Core\Validation\FormRequest;

class AcademyUpdateRequest extends FormRequest {



    public function authorize(): bool {
        return true;
    }



    public function rules(): array {
        return [
            'username'=>'required|min:3|max:100',
            'email'=>'nullable|email',
            'phone'=>'nullable',
            'status'=>'required|in:approved,pending',
            'locale'=>'nullable|max:10',
            'timezone'=>'nullable|max:100',
        ];
    }



    public function messages(): array {
        return [];
    }




}