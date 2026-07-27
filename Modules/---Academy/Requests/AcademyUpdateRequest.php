<?php

namespace Modules\Academy\Requests;

use Core\Validation\FormRequest;

class AcademyUpdateRequest extends FormRequest {

    public function authorize(): bool { return true; }
    public function messages(): array { return []; }



    public function rules(): array {
        return [
            'username'=>'required|min:3|max:100',
            'email'=>'nullable|email',
            'phone'=>'nullable',
            'status'=>'required|in:approved,pending',
            'locale'=>'nullable|max:10',
            'timezone'=>'nullable|max:100',

            'name_fa' => 'required|max:255',
            'name_en' => 'nullable|max:255',

            'slogan_fa' => 'nullable|max:255',
            'slogan_en' => 'nullable|max:255',

            'short_description_fa' => 'nullable|max:500',
            'short_description_en' => 'nullable|max:500',

            'description_fa' => 'nullable',
            'description_en' => 'nullable',

            'rules_fa' => 'nullable',
            'rules_en' => 'nullable',

            'registration_fa' => 'nullable',
            'registration_en' => 'nullable',

            'meta_title_fa' => 'nullable|max:255',
            'meta_title_en' => 'nullable|max:255',

            'meta_description_fa' => 'nullable|max:255',
            'meta_description_en' => 'nullable|max:255',

            'keywords_fa' => 'nullable|max:255',
            'keywords_en' => 'nullable|max:255',
        ];
    }




}