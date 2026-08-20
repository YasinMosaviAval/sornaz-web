<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyClassScheduleService;
use Throwable;

class AcademyWeeklyScheduleBoundsController {


    public function __construct(private AcademyClassScheduleService $service) {}


    public function index() {
        try {
            $data = $this->service->weeklyBounds((int) auth()->id(), (int) ($_GET['branch'] ?? 0));
            return ResponseFactory::json(['success' => true, 'data' => $data]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
