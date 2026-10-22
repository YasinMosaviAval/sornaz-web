<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyClassScheduleService;
use Throwable;

class AcademyWeeklyScheduleBoundsController
{
    public function __construct(private AcademyClassScheduleService $service)
    {
    }

    public function index()
    {
        try {
            $branch = (string) ($_GET['branch'] ?? '');
            $data = $this->service->weeklyBounds((int) auth()->id(), $branch === 'academy' ? 'academy' : (int) $branch);
            return ResponseFactory::json(['success' => true, 'data' => $data]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
