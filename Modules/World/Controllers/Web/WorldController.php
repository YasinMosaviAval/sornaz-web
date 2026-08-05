<?php

namespace Modules\World\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\World\Services\WorldService;
use Modules\World\Repositories\WorldRepository;
use Modules\World\Requests\WorldStoreRequest;
use Modules\World\Requests\WorldUpdateRequest;

class WorldController {

    protected WorldService $service;



    public function __construct() {
        $this->service = new WorldService(new WorldRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'World::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('World');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'World::create'
            )
            ->layout('main')
            ->title('ایجاد World');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new WorldStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/worlds');
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
                'World::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش World');
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
                'World::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش World');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new WorldUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/worlds');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/worlds');
    }

}
