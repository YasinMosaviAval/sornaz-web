<?php

namespace Core\Database\Concerns;

use Core\Translation\TranslationService;

trait HasTranslations {


    protected array $translated = [];



    public function translate(string $field, ?string $locale = null, int $version = 1): mixed {
        return TranslationService::manager()->get(
            $this,
            $field,
            $locale,
            $version
        );
    }



    public function setTranslation(string $field, mixed $value, ?string $locale = null, int $version = 1): bool {
        return TranslationService::manager()->set(
            $this,
            $field,
            $value,
            $locale,
            $version
        );
    }



    public function hasTranslation(string $field, ?string $locale = null, int $version = 1): bool {
        return TranslationService::manager()->exists(
            $this,
            $field,
            $locale,
            $version
        );
    }



    public function removeTranslation(string $field, ?string $locale = null, int $version = 1): bool {
        return TranslationService::manager()->delete(
            $this,
            $field,
            $locale,
            $version
        );
    }




}