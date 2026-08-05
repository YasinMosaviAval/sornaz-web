<?php

namespace Modules\Education\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Education\Requests\EducationStoreRequest;
use Modules\Education\Requests\EducationUpdateRequest;
use Modules\Education\Services\EducationService;

class EducationController {

    protected EducationService $service;

    public function __construct() {
        $this->service = new EducationService();
    }

    /**
     * GET /api/educations
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/educations/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Education not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/educations
     */
    public function store() {
        $request = new EducationStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/educations/{id}
     */
    public function update(int $id) {
        $request = new EducationUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/educations/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}