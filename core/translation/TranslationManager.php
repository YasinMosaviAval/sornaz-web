<?php

namespace Core\Translation;

use Core\Database\Model;

class TranslationManager {


    protected TranslationRepository $repository;
    protected array $cache = [];
    // protected ?Model $currentModel = null;



    public function __construct() {
        $this->repository = new TranslationRepository();
    }


    public function for(Model $model): static {
        $this->currentModel = $model;
        return $this;
    }


    // protected function table(Model|string $model): string {
    //     if (is_string($model)) {
    //         return $this->table($model);
    //     }
    //     return $this->table($model);
    // }
    protected function table(Model|string $model): string {
        if (is_string($model)) {
            return $model;
        }
        return $model::getTable();
    }



    protected function id(Model|int|string $model): int|string {
        if ($model instanceof Model) {
            $primaryKey = $model::getPrimaryKey();
            return $model->$primaryKey;
        }
        return $model;
    }


    public function get(
        Model|string $model,
        string|int|null $id,
        string $field,
        ?string $locale = null,
        int $version = 1
    ): mixed {

        if ($model instanceof Model) {
            $id = $this->id($model);
        }

        $locale ??= app()->getLocale();

        $key = implode(':', [
            $this->table($model),
            $id,
            $field,
            $locale,
            $version
        ]);

        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $translation = $this->repository->find(
            $this->table($model),
            $id,
            $field,
            $locale,
            $version
        );

        $this->cache[$key] = $translation?->value;

        return $this->cache[$key];
    }



    public function set(Model|string $model, int|string|null $id, string $field, mixed $value, ?string $locale = null, int $version = 1): bool {
        if ($model instanceof Model) {
            $id = $this->id($model);
        }
        
        $locale ??= app()->getLocale();
        $result = $this->repository->updateOrCreate(
            $this->table($model),
            $id,
            $field,
            $locale,
            $value,
            $version
        );
        if ($result && $model instanceof Model) {
            $key = $this->cacheKey(
                $model,
                $field,
                $locale,
                $version
            );
            $this->cache[$key] = $value;
        }
        return $result;
    }



    public function exists(Model|string $model, int|string|null $id, string $field, ?string $locale = null, int $version = 1): bool {
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



    public function delete(Model|string $model, int|string|null $id, string $field, ?string $locale = null, int $version = 1): bool {
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



    protected function cacheKey(Model $model, string $field, string $locale, int $version): string {
        return implode(
            ':',
            [
                $this->table($model),
                $this->id($model),
                $field,
                $locale,
                $version
            ]
        );
    }



    public function warmup(
        string $table,
        array $models,
        ?string $locale = null,
        int $version = 1
    ): void {

        if (empty($models)) {
            return;
        }

        
        $locale ??= app()->getLocale();

        $ids = [];

        foreach ($models as $model) {
            $ids[] = $this->id($model);
        }

        $rows = $this->repository->loadMany(
            $table,
            $ids,
            $locale,
            $version
        );

        foreach ($rows as $row) {

            $key = implode(':',[
                $row->table_name,
                $row->table_id,
                $row->field,
                $row->locale,
                $row->version
            ]);

            $this->cache[$key] = $row->value;
        }
    }

}