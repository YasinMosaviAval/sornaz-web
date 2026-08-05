<?php

namespace Modules\Profile\DTO;

class ProfileDTO {

    public function __construct(public array $attributes=[]){
    }

    public static function fromArray(array $data): static {
        return new static($data);
    }

    public function toArray(): array {
        return $this->attributes;
    }

}
