<?php

namespace Modules\Enrollment\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Enrollment\Requests\EnrollmentStoreRequest;
use Modules\Enrollment\Requests\EnrollmentUpdateRequest;
use Modules\Enrollment\Services\EnrollmentService;

class EnrollmentController {

    protected EnrollmentService $service;

    public function __construct() {
        $this->service = new EnrollmentService();
    }

    /**
     * GET /api/enrollments
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/enrollments/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Enrollment not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/enrollments
     */
    public function store() {
        $request = new EnrollmentStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/enrollments/{id}
     */
    public function update(int $id) {
        $request = new EnrollmentUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/enrollments/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}