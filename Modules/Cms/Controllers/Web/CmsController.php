<?php

namespace Modules\Cms\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Cms\Services\CmsService;
use Modules\Cms\Repositories\CmsRepository;
use Modules\Cms\Requests\CmsStoreRequest;
use Modules\Cms\Requests\CmsUpdateRequest;

class CmsController {

    protected CmsService $service;



    public function __construct() {
        $this->service = new CmsService(new CmsRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Cms::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Cms');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Cms::create'
            )
            ->layout('main')
            ->title('ایجاد Cms');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new CmsStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/cmss');
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
                'Cms::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Cms');
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
                'Cms::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Cms');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new CmsUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/cmss');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/cmss');
    }

}
