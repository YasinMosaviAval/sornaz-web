<?php

namespace Modules\Blog\Repositories;

use Modules\Blog\Contracts\BlogRepositoryInterface;
use Modules\Blog\DTO\BlogDTO;

class BlogRepository implements BlogRepositoryInterface {


    public function paginate(int $page = 1, int $perPage = 15) {
    }


    public function latest(int $limit = 10) {
    }


    public function popular(int $limit = 10) {
    }


    public function find(int $id): ?BlogDTO {
        return null;
    }


    public function findBySlug(string $slug): ?BlogDTO {
        return null;
    }


    public function create(BlogDTO $dto): int {
        return 0;
    }


    public function update(int $id, BlogDTO $dto): bool {
        return false;
    }


    public function delete(int $id): bool {
        return false;
    }


}