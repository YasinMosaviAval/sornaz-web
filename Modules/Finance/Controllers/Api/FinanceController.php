<?php

namespace Modules\Finance\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Finance\Requests\FinanceStoreRequest;
use Modules\Finance\Requests\FinanceUpdateRequest;
use Modules\Finance\Services\FinanceService;

class FinanceController {

    protected FinanceService $service;

    public function __construct() {
        $this->service = new FinanceService();
    }

    /**
     * GET /api/finances
     */
    public function index() {
        return ResponseFactory::json($this->service->all());
    }

    /**
     * GET /api/finances/{id}
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            return ResponseFactory::json(['message' => 'Finance not found.'], 404);
        }
        return ResponseFactory::json($item);
    }

    /**
     * POST /api/finances
     */
    public function store() {
        $request = new FinanceStoreRequest($_POST);
        $id = $this->service->create($request->validated());
        return ResponseFactory::json([
            'success' => true,
            'id' => $id
        ], 201);
    }

    /**
     * PUT /api/finances/{id}
     */
    public function update(int $id) {
        $request = new FinanceUpdateRequest($_POST);
        $result = $this->service->update($id, $request->validated());
        return ResponseFactory::json(['success'=>$result]);
    }

    /**
     * DELETE /api/finances/{id}
     */
    public function destroy(int $id) {
        $result = $this->service->delete($id);
        return ResponseFactory::json(['success'=>$result]);
    }


}