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
            'phone' => $this->phone,
            'status' => $this->status,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'avatar_file_id' => $this->avatar_file_id,
            'type' => $this->type,
            'type_label' => $this->typeLabel(),
            
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
