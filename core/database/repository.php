<?php

namespace Core\Database;

use RuntimeException;

abstract class Repository {


    protected ?string $model = null;
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * Builder مشترک
     */
    protected function query(): Builder {
        if ($this->model !== null) {
            return $this->model::query();
        }
        return DB::table($this->table);
    }

    /**
     * دریافت همه رکوردها
     */
    public function all(): array {
        return $this->query()->get();
    }

    /**
     * اولین رکورد
     */
    public function first(): mixed {
        return $this->query()->first();
    }

    /**
     * یافتن بر اساس کلید اصلی
     */
    public function find(int $id): ?array {
        return $this->query()->find($id, $this->primaryKey);
    }

    /**
     * یافتن یا Exception
     */
    public function findOrFail(int|string $id): mixed {
        $record = $this->find($id);
        if (!$record) {
            throw new RuntimeException("Record [$id] not found.");
        }
        return $record;
    }

    /**
     * ایجاد رکورد
     */
    public function create(array $data): bool {
        if ($this->model !== null) {
            return $this->query()->insert($data);
        }
        return $this->query()->insert($data);
    }

    /**
     * بروزرسانی
     */
    public function update(int|string $id, array $data): bool {
        if ($this->model !== null) {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
            return $model->update($data);
        }
        return $this->query()->where($this->primaryKey, $id)->update($data);
    }

    /**
     * حذف
     */
    public function delete(int|string $id): bool {
        if ($this->model !== null) {
            $model = $this->find($id);
            if (!$model) {
                return false;
            }
            return $model->delete();
        }
        return $this->query()->where($this->primaryKey, $id)->delete();
    }

    /**
     * تعداد رکوردها
     */
    public function count(): int {
        return $this->query()->count();
    }

    /**
     * وجود رکورد
     */
    public function exists(): bool {
        return $this->count() > 0;
    }

    /**
     * صفحه‌بندی
     */
    public function paginate(int $page = 1, int $perPage = 20): array {
        return $this->query()->paginate($page, $perPage);
    }

    /**
     * دسترسی مستقیم به Builder
     */
    public function builder(): Builder {
        return $this->query();
    }








}