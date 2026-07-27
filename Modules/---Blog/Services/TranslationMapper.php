<?php

namespace Modules\Blog\Services;

class TranslationMapper {

    public static function map(array $rows): array {
        $translations = [];
        foreach ($rows as $row) {
            $translations[$row['field']] = $row['value'];
        }
        return $translations;
    }


}