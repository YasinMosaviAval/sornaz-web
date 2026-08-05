<?php

namespace Modules\Profile\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Profile\Requests\ProfileStoreRequest;
use Modules\Profile\Requests\ProfileUpdateRequest;
use Modules\Profile\Services\ProfileService;

class ProfileController {

    protected ProfileService $service;

    public function __construct() {
        $this->service = new ProfileService();
    }

    /**
     * GET /api/profiles
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/profiles/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Profile not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/profiles
     */
    public function store() {
        $request = new ProfileStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/profiles/{id}
     */
    public function update(int $id) {
        $request = new ProfileUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/profiles/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}