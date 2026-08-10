<?php

namespace Modules\Academy\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Academy\Requests\AcademyStoreRequest;
use Modules\Academy\Requests\AcademyUpdateRequest;
use Modules\Academy\Services\AcademyService;

class AcademyController {

    protected AcademyService $service;

    public function __construct() {
        $this->service = new AcademyService();
    }

    /**
     * GET /api/academy
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/academy/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Academy not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/academy
     */
    public function store() {
        $request = new AcademyStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/academy/{id}
     */
    public function update(int $id) {
        $request = new AcademyUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/academy/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}
