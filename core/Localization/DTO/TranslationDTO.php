<?php

namespace Core\Localization\DTO;

class TranslationDTO
{
    public ?int $translation_id = null;

    public string $table_name;

    public int|string $table_id;

    public string $locale;

    public string $field;

    public string $value;

    public int $version = 1;

    public static function fromArray(array $row): self
    {
        $dto = new self();

        foreach ($row as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}