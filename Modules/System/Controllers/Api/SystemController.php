<?php

namespace Modules\System\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\System\Requests\SystemStoreRequest;
use Modules\System\Requests\SystemUpdateRequest;
use Modules\System\Services\SystemService;

class SystemController {

    protected SystemService $service;

    public function __construct() {
        $this->service = new SystemService();
    }

    /**
     * GET /api/systems
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/systems/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'System not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/systems
     */
    public function store() {
        $request = new SystemStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/systems/{id}
     */
    public function update(int $id) {
        $request = new SystemUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/systems/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}