<?php

namespace Core\localization\Contracts;

use Core\localization\TranslationCollection;

interface TranslationRepositoryInterface {


    public function load(
        string $table,
        int|string $tableId,
        ?string $locale = null,
        int $version = 1
    ): TranslationCollection;


    public function loadMany(
        string $table,
        array $ids,
        ?string $locale = null,
        int $version = 1
    ): array;


    public function save(
        string $table,
        int|string $tableId,
        array $translations,
        ?string $locale = null,
        int $version = 1
    ): bool;


    public function delete(
        string $table,
        int|string $tableId,
        ?string $locale = null,
        int $version = 1
    ): bool;


}