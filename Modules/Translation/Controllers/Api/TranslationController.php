<?php

namespace Modules\Translation\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Translation\Requests\TranslationStoreRequest;
use Modules\Translation\Requests\TranslationUpdateRequest;
use Modules\Translation\Services\TranslationService;

class TranslationController {

    protected TranslationService $service;

    public function __construct() {
        $this->service = new TranslationService();
    }

    /**
     * GET /api/translations
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/translations/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Translation not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/translations
     */
    public function store() {
        $request = new TranslationStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/translations/{id}
     */
    public function update(int $id) {
        $request = new TranslationUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/translations/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}