<?php

namespace Modules\Blog\Services;

use Modules\Blog\Contracts\BlogRepositoryInterface;
use Modules\Blog\DTO\BlogDTO;

class BlogService
{
    public function __construct(
        protected BlogRepositoryInterface $repository
    ) {
    }

    public function paginate(int $page = 1)
    {
        return $this->repository->paginate($page);
    }

    public function find(int $id): ?BlogDTO
    {
        return $this->repository->find($id);
    }

    public function findBySlug(string $slug): ?BlogDTO
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(BlogDTO $dto): int
    {
        return $this->repository->create($dto);
    }

    public function update(int $id, BlogDTO $dto): bool
    {
        return $this->repository->update($id, $dto);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}