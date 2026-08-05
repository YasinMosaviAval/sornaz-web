<?php

namespace Modules\Page\Controllers\Api;

use Core\Http\ResponseFactory;
use Modules\Page\Requests\PageStoreRequest;
use Modules\Page\Requests\PageUpdateRequest;
use Modules\Page\Services\PageService;

class PageController {

    protected PageService $service;

    public function __construct() {
        $this->service = new PageService();
    }

    /**
     * GET /api/pages
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/pages/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Page not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/pages
     */
    public function store() {
        $request = new PageStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/pages/{id}
     */
    public function update(int $id) {
        $request = new PageUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/pages/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}