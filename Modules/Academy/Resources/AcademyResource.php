<?php

namespace Core\Http\Resources;

class AcademyResource extends Resource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->user_id,
            'username' => $this->resource->username,
            'email' => $this->resource->email,
            'type' => $this->resource->type,
            'type_label' => $this->typeLabel(),
            'locale' => $this->resource->locale,
        ];
    }

    protected function typeLabel(): string
    {
        return match ($this->resource->type) {
            'academy' => 'آموزشگاه',
            'branch' => 'شعبه',
            'teacher' => 'مدرس',
            'student' => 'دانش‌آموز',
            default => 'نامشخص'
        };
    }
}

