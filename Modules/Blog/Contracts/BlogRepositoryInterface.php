<?php

namespace Modules\Blog\Contracts;

use Modules\Blog\DTO\BlogDTO;

interface BlogRepositoryInterface {

    public function paginate(int $page = 1, int $perPage = 15);
    public function latest(int $limit = 10);
    public function popular(int $limit = 10);
    public function find(int $id): ?BlogDTO;
    public function findBySlug(string $slug): ?BlogDTO;
    public function create(BlogDTO $dto): int;
    public function update(int $id, BlogDTO $dto): bool;
    public function delete(int $id): bool;


}