<?php

namespace Modules\Blog\DTO;

class CategoryDTO {

    public ?int $category_id = null;
    public ?string $slug = null;
    public ?string $group = null;
    public ?string $name = null;
    public array $translations = [];

    public static function fromArray(array $row): static {
        $dto = new static();
        foreach ($row as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }
        $dto->translations = $row['translations'] ?? [];
        return $dto;
    }

    public function title(?string $locale = 'fa'): string {
        return $this->translations[$locale]['title'] ?? $this->name ?? '';
    }

    public function description(?string $locale = 'fa'): string {
        return $this->translations[$locale]['description'] ?? '';
    }
}