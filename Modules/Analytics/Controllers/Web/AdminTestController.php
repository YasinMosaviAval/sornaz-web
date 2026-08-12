<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;

class AdminTestController {
    public function __construct(protected AdminTestDataService $tests) {}

    public function seedAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->seedAcademyManagers();
            session()->flash('admin_test_message', $result['message']);
        } catch (\Throwable $e) {
            session()->flash('admin_test_error', 'ایجاد مدیران آموزشگاه آزمایشی ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function deleteAcademyManagers() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->tests->deleteAcademyManagers();
            session()->flash('admin_test_message', $result['message']);
        } catch (\Throwable $e) {
            session()->flash('admin_test_error', 'حذف مدیران آموزشگاه آزمایشی ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel#tests');
    }

    public function deleteAvailability(string $id) {
        try { return ResponseFactory::json($this->tests->deleteAvailability((int)$id, (int)auth()->id())); }
        catch (\Throwable $e) { return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404); }
    }

    public function deleteAvailabilityException(string $id) {
        try { return ResponseFactory::json($this->tests->deleteAvailabilityException((int)$id, (int)auth()->id())); }
        catch (\Throwable $e) { return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404); }
    }
}
