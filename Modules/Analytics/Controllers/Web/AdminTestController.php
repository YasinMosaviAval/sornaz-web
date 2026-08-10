<?php

namespace Modules\Analytics\Controllers\Web;

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
}
