<?php

namespace Modules\Media\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Media\Services\MediaService;
use Modules\Media\Repositories\MediaRepository;
use Modules\Media\Requests\MediaStoreRequest;
use Modules\Media\Requests\MediaUpdateRequest;

class MediaController {

    protected MediaService $service;



    public function __construct() {
        $this->service = new MediaService(new MediaRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Media::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Media');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Media::create'
            )
            ->layout('main')
            ->title('ایجاد Media');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new MediaStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/medias');
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
                'Media::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Media');
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
                'Media::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Media');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new MediaUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/medias');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/medias');
    }

}
