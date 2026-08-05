<?php

namespace Modules\Communication\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Communication\Requests\CommunicationStoreRequest;
use Modules\Communication\Requests\CommunicationUpdateRequest;
use Modules\Communication\Services\CommunicationService;

class CommunicationController {

    protected CommunicationService $service;

    public function __construct() {
        $this->service = new CommunicationService();
    }

    /**
     * GET /api/communications
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/communications/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Communication not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/communications
     */
    public function store() {
        $request = new CommunicationStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/communications/{id}
     */
    public function update(int $id) {
        $request = new CommunicationUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/communications/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}