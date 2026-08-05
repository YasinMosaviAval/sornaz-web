<?php

namespace Modules\Media\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Media\Requests\MediaStoreRequest;
use Modules\Media\Requests\MediaUpdateRequest;
use Modules\Media\Services\MediaService;

class MediaController {

    protected MediaService $service;

    public function __construct() {
        $this->service = new MediaService();
    }

    /**
     * GET /api/medias
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/medias/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Media not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/medias
     */
    public function store() {
        $request = new MediaStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/medias/{id}
     */
    public function update(int $id) {
        $request = new MediaUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/medias/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}