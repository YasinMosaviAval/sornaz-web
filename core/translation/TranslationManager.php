<?php

namespace Core\Translation;

use Core\Database\Model;

class TranslationManager {

    protected TranslationRepository $repository;

    public function __construct()
    {
        $this->repository = new TranslationRepository();
    }



    protected function table(Model|string $model): string
    {
        if (is_string($model)) {
            return $model::getTable();
        }

        return $model::getTable();
    }



    protected function id(Model|int|string $model): int|string
    {
        if ($model instanceof Model) {

            $primaryKey = $model::getPrimaryKey();

            return $model->$primaryKey;

        }

        return $model;
    }


    public function get(
        Model $model,
        string $field,
        ?string $locale = null,
        int $version = 1
    ): mixed {

        $locale ??= app()->getLocale();

        $primaryKey = $model::getPrimaryKey();

        $translation = $this->repository->find(
            $model::getTable(),
            $model->{$primaryKey},
            $field,
            $locale,
            $version
        );

        return $translation?->value;
    }



    public function set(
        Model|string $model,
        int|string|null $id,
        string $field,
        mixed $value,
        ?string $locale = null,
        int $version = 1
    ): bool {

        if ($model instanceof Model) {
            $id = $this->id($model);
        }

        $locale ??= app()->getLocale();

        return $this->repository->save(
            $this->table($model),
            $id,
            $field,
            $locale,
            $value,
            $version
        );

    }



    public function exists(
        Model|string $model,
        int|string|null $id,
        string $field,
        ?string $locale = null,
        int $version = 1
    ): bool {

        if ($model instanceof Model) {
            $id = $this->id($model);
        }

        $locale ??= app()->getLocale();

        return $this->repository->exists(
            $this->table($model),
            $id,
            $field,
            $locale,
            $version
        );

    }



    public function delete(
        Model|string $model,
        int|string|null $id,
        string $field,
        ?string $locale = null,
        int $version = 1
    ): bool {

        if ($model instanceof Model) {
            $id = $this->id($model);
        }

        $locale ??= app()->getLocale();

        return $this->repository->delete(
            $this->table($model),
            $id,
            $field,
            $locale,
            $version
        );

    }

}