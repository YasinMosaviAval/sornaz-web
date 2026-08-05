<?php

namespace Modules\Enrollment\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Enrollment\Services\EnrollmentService;
use Modules\Enrollment\Repositories\EnrollmentRepository;
use Modules\Enrollment\Requests\EnrollmentStoreRequest;
use Modules\Enrollment\Requests\EnrollmentUpdateRequest;

class EnrollmentController {

    protected EnrollmentService $service;



    public function __construct() {
        $this->service = new EnrollmentService(new EnrollmentRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Enrollment::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Enrollment');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Enrollment::create'
            )
            ->layout('main')
            ->title('ایجاد Enrollment');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new EnrollmentStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/enrollments');
    }



    /**
     * نمایش
     */
    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view(
                'Enrollment::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Enrollment');
    }



    /**
     * فرم ویرایش
     */
    public function edit(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view(
                'Enrollment::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Enrollment');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new EnrollmentUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/enrollments');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/enrollments');
    }

}
