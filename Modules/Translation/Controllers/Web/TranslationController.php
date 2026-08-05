<?php

namespace Modules\Translation\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Translation\Services\TranslationService;
use Modules\Translation\Repositories\TranslationRepository;
use Modules\Translation\Requests\TranslationStoreRequest;
use Modules\Translation\Requests\TranslationUpdateRequest;

class TranslationController {

    protected TranslationService $service;



    public function __construct() {
        $this->service = new TranslationService(new TranslationRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Translation::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Translation');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Translation::create'
            )
            ->layout('main')
            ->title('ایجاد Translation');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new TranslationStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/translations');
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
                'Translation::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Translation');
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
                'Translation::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Translation');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new TranslationUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/translations');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/translations');
    }

}
