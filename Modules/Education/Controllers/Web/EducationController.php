<?php

namespace Modules\Education\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Education\Services\EducationService;
use Modules\Education\Repositories\EducationRepository;
use Modules\Education\Requests\EducationStoreRequest;
use Modules\Education\Requests\EducationUpdateRequest;

class EducationController {

    protected EducationService $service;



    public function __construct() {
        $this->service = new EducationService(new EducationRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Education::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Education');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Education::create'
            )
            ->layout('main')
            ->title('ایجاد Education');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new EducationStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/educations');
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
                'Education::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Education');
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
                'Education::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Education');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new EducationUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/educations');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/educations');
    }

}
