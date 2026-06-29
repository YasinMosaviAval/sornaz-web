<?php

namespace Core\Database\Concerns;

trait HasAttributes {


    protected array $attributes = [];
    protected array $casts = [];


    public function __construct(array $attributes = []){if ($attributes) {$this->forceFill($attributes);}}

    public function __get(string $key) {
        $value = $this->attributes[$key] ?? null;
        if ($this->relationLoaded($key)) {
            return $this->getRelation($key);
        }
        return $this->castAttribute($key, $value);
    }

    public function __set(string $key, mixed $value): void {$this->attributes[$key] = $value;}



    public function toArray(): array {return $this->attributes;}


    protected function castAttribute(string $key, mixed $value): mixed{
        if ($value === null) {return null;}
        $cast = $this->casts[$key] ?? null;
        if ($cast === null) {return $value;}
        return match ($cast) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array' => (array) $value,
            'json' => json_decode($value, true),
            'object' => json_decode($value),
            'datetime' => new \DateTime($value),
            default => $value
        };
    }




}