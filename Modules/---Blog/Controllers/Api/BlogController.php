<?php

namespace Modules\Blog\Controllers\Api;

use Core\Http\ResponseFactory;
use Modules\Blog\Requests\BlogStoreRequest;
use Modules\Blog\Requests\BlogUpdateRequest;
use Modules\Blog\Services\BlogService;

class BlogController {

    protected BlogService $service;

    public function __construct() {
        $this->service = new BlogService();
    }

    /**
     * GET /api/blogs
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/blogs/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Blog not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/blogs
     */
    public function store() {
        $request = new BlogStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/blogs/{id}
     */
    public function update(int $id) {
        $request = new BlogUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/blogs/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}