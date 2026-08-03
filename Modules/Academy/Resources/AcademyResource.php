<?php

namespace Modules\Academy\Resources;

use Core\Http\Resources\JsonResource;

class AcademyResource extends JsonResource {

    public function toArray(): array {
        return [
            'academy_id'     => $this->academy_id,
            'user_id'        => $this->user_id,
            'id'             => $this->user_id, // اگر جای دیگری استفاده شده باشد
            'username'       => $this->username,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'status'         => $this->status,
            'locale'         => $this->locale,
            'timezone'       => $this->timezone,
            'avatar_file_id' => $this->avatar_file_id,
            'type'           => $this->type,
            'type_label'     => $this->typeLabel(),
            'created_at'     => $this->created_at,
        ];
    }

    protected function typeLabel(): string {
        return match ($this->type) {
            'academy' => 'آموزشگاه',
            'branch'  => 'شعبه',
            'teacher' => 'مدرس',
            'student' => 'دانش‌آموز',
            default   => 'نامشخص'
        };
    }
}