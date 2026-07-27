<?php

namespace Core\Translation;

class TranslationRepository {


    public function find(string $table, int|string $id, string $field, string $locale, int $version = 1): ?Translation {
        return Translation::query()
            ->where('table_name', $table)
            ->where('table_id', $id)
            ->where('field', $field)
            ->where('locale', $locale)
            ->where('version', $version)
            ->first();
    }



    public function exists(string $table, int|string $id, string $field, string $locale, int $version = 1): bool {
        return $this->find(
            $table,
            $id,
            $field,
            $locale,
            $version
        ) !== null;
    }



    public function updateOrCreate(string $table, int|string $id, string $field, string $locale, mixed $value, int $version = 1): bool {
        $translation = $this->find(
            $table,
            $id,
            $field,
            $locale,
            $version
        );
        if ($translation) {
            return $translation->update(['value' => $value]);
        }
        Translation::create([
            'table_name' => $table,
            'table_id'   => $id,
            'field'      => $field,
            'locale'     => $locale,
            'value'      => $value,
            'version'    => $version,
        ]);
        return true;
    }


    public function delete(string $table, int|string $id, string $field, string $locale, int $version = 1): bool {
        $translation = $this->find(
            $table,
            $id,
            $field,
            $locale,
            $version
        );
        if (!$translation) {
            return false;
        }
        return $translation->delete();
    }



    public function loadMany(string $table, array $ids, ?string $locale = null, int $version = 1): array {
        if (empty($ids)) {
            return [];
        }
        $locale ??= app()->getLocale();
        return Translation::query()
            ->where('table_name', $table)
            ->whereIn('table_id', $ids)
            ->where('locale', $locale)
            ->where('version', $version)
            ->get();
    }



}