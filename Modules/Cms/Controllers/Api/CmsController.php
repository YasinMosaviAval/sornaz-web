<?php

namespace Modules\Cms\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Cms\Requests\CmsStoreRequest;
use Modules\Cms\Requests\CmsUpdateRequest;
use Modules\Cms\Services\CmsService;

class CmsController {

    protected CmsService $service;

    public function __construct() {
        $this->service = new CmsService();
    }

    /**
     * GET /api/cmss
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/cmss/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Cms not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/cmss
     */
    public function store() {
        $request = new CmsStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/cmss/{id}
     */
    public function update(int $id) {
        $request = new CmsUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/cmss/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}