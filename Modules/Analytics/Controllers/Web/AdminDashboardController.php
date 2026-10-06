<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminDashboardService;

class AdminDashboardController
{
    public function __construct(private AdminDashboardService $service)
    {
    }

    public function index()
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->service->data((int) auth()->id(), $_GET)]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function approveWaiting(int $id)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->service->approveWaiting((int) auth()->id(), $id, $_POST)]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function updateMyWaiting(int $id)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->service->updateMyWaiting((int) auth()->id(), $id, $_POST)]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function ensureWaitingChat(int $id)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->service->ensureWaitingChat((int) auth()->id(), $id)]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function finalizeWaiting(int $id)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->service->finalizeWaiting((int) auth()->id(), $id, (int) ($_POST['teacher_id'] ?? 0), (int) ($_POST['session_id'] ?? 0), (string) ($_POST['first_date'] ?? ''), (string) ($_POST['start_time'] ?? ''))]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
