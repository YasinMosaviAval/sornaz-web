<?php

namespace Core\Database\Concerns;

trait HasAttributes {


    protected array $attributes = [];
    protected array $casts = [];


    public function __construct(array $attributes = []){if ($attributes) {$this->forceFill($attributes);}}


    public function __get(string $key) {
        /*
        |--------------------------------------------------------------------------
        | Loaded Relation
        |--------------------------------------------------------------------------
        */

        if ($this->relationLoaded($key)) {
            return $this->getRelation($key);
        }

        /*
        |--------------------------------------------------------------------------
        | Translated Attribute
        |--------------------------------------------------------------------------
        */

        if ($this->isTranslatedAttribute($key)) {
            $translated = $this->translate($key);

            /*
            |--------------------------------------------------------------------------
            | اگر ترجمه وجود داشت همان برگردد
            |--------------------------------------------------------------------------
            */

            if ($translated !== null) {
                return $translated;
            }

            /*
            |--------------------------------------------------------------------------
            | اگر ترجمه وجود نداشت از مقدار اصلی استفاده کن
            |--------------------------------------------------------------------------
            */

            if (array_key_exists($key, $this->attributes)) {
                return $this->castAttribute($key, $this->attributes[$key]);
            }
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Attribute
        |--------------------------------------------------------------------------
        */

        if (array_key_exists($key, $this->attributes)) {
            return $this->castAttribute($key, $this->attributes[$key]);
        }
        return null;
    }



    public function __set(string $key, mixed $value): void {

        /*
        |--------------------------------------------------------------------------
        | Translation Attribute
        |--------------------------------------------------------------------------
        */

        if ($this->isTranslatedAttribute($key)) {
            $this->setTranslatedAttribute($key, $value);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Attribute
        |--------------------------------------------------------------------------
        */
        $this->attributes[$key] = $value;
    }


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


    public function __isset(string $key): bool {
        if (array_key_exists($key, $this->attributes)) {
            return true;
        }
        return $this->isTranslatedAttribute($key);
    }



    protected function isTranslatedAttribute(string $key): bool {
        return in_array($key, $this->getTranslatedAttributes(), true);
    }


    protected function setTranslatedAttribute(string $key, mixed $value): void {
        $this->setTranslation($key, $value);
    }


}