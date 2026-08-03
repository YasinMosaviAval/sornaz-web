<?php

namespace Modules\Blog\Services;

use Modules\Blog\Contracts\BlogRepositoryInterface;

class BlogService {
    public function __construct(protected BlogRepositoryInterface $repository) { }
    public function paginate(int $page = 1, int $perPage = 15) { return $this->repository->paginate($page, $perPage); }
    public function latest(int $limit = 5) { return $this->repository->latest($limit); }
    public function popular(int $limit = 5) { return $this->repository->popular($limit); }
    public function find(int $id) { return $this->repository->find($id); }
    public function findBySlug(string $slug) { return $this->repository->findBySlug($slug); }
    public function create($dto) { return $this->repository->create($dto); }
    public function update($id, $dto) { return $this->repository->update($id, $dto); }
    public function delete($id) { return $this->repository->delete($id); }

}