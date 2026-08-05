<?php

namespace Modules\Blog\Contracts;

use Modules\Blog\DTO\CategoryDTO;

interface CategoryRepositoryInterface {
    public function all(): array;
    public function find(int $id): ?CategoryDTO;
    public function findBySlug(string $slug): ?CategoryDTO;
    public function findMany(array $ids): array;
}