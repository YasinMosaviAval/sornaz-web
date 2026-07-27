<?php

namespace Modules\Blog\Services;

use Modules\Blog\Repositories\CategoryRepository;

class CategoryService {
    protected CategoryRepository $repository;
    public function __construct() { $this->repository = new CategoryRepository(); }
    public function all() { return $this->repository->all(); }
    public function find(int $id) { return $this->repository->find($id); }
    public function findBySlug(string $slug) { return $this->repository->findBySlug($slug); }
}
