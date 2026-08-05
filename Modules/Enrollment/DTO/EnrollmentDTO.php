<?php

namespace Modules\Enrollment\DTO;

class EnrollmentDTO {

    public function __construct(public array $attributes=[]){
    }

    public static function fromArray(array $data): static {
        return new static($data);
    }

    public function toArray(): array {
        return $this->attributes;
    }

}
