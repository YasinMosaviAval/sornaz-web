<?php

namespace Core\Localization;

use Core\Localization\DTO\TranslationDTO;

class TranslationCollection {
    /**
     * @var TranslationDTO[]
     */
    protected array $items = [];



    public function add(TranslationDTO $translation): void {
        $this->items[$translation->field] = $translation;
    }



    public function get(string $field, mixed $default = null): mixed {
        return $this->items[$field]->value ?? $default;
    }



    public function has(string $field): bool {
        return isset($this->items[$field]);
    }



    public function all(): array {
        return $this->items;
    }



    public function toArray(): array {
        $result = [];
        foreach ($this->items as $field => $translation) {
            $result[$field] = $translation->value;
        }
        return $result;
    }


}