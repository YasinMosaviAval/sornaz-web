<?php

namespace Modules\World\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\World\Requests\WorldStoreRequest;
use Modules\World\Requests\WorldUpdateRequest;
use Modules\World\Services\WorldService;

class WorldController {

    protected WorldService $service;

    public function __construct() {
        $this->service = new WorldService();
    }

    /**
     * GET /api/worlds
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/worlds/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'World not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/worlds
     */
    public function store() {
        $request = new WorldStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/worlds/{id}
     */
    public function update(int $id) {
        $request = new WorldUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/worlds/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}