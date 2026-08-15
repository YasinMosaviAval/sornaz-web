<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminDashboardService;

class AdminDashboardController
{
    public function __construct(private AdminDashboardService $service) {}

    public function index()
    {
        try {
            return ResponseFactory::json(['success'=>true,'data'=>$this->service->data((int)auth()->id(),$_GET)]);
        } catch (\Throwable $exception) {
            return ResponseFactory::json(['success'=>false,'message'=>$exception->getMessage()],422);
        }
    }
}
