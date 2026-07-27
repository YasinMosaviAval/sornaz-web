<?php

namespace Modules\Blog\Requests;

class BlogStoreRequest {

    public function authorize(): bool {
        return true;
    }


    public function rules(): array {
        return [
            'slug' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'visibility' => ['required'],
            'category_id' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
        ];
    }


    public function messages(): array {
        return [
            'slug.required' => 'اسلاگ الزامی است.',
            'title.required' => 'عنوان الزامی است.',
            'content.required' => 'متن مقاله الزامی است.',
        ];
    }

    public function attributes(): array {
        return [];
    }



}