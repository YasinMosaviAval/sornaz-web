<?php

namespace Modules\Academy\Resources;

use Core\Http\Resources\JsonResource;

class AcademyResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->user_id,
            'username' => $this->username,
            'email' => $this->email,
            'type' => $this->type,
            'type_label' => $this->typeLabel(),
            'locale' => $this->locale,
        ];
    }

    protected function typeLabel(): string
    {
        return match ($this->type) {
            'academy' => 'آموزشگاه',
            'branch' => 'شعبه',
            'teacher' => 'مدرس',
            'student' => 'دانش‌آموز',
            default => 'نامشخص'
        };
    }
}
