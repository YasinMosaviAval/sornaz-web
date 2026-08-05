<?php

namespace Modules\Communication\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Communication\Services\CommunicationService;
use Modules\Communication\Repositories\CommunicationRepository;
use Modules\Communication\Requests\CommunicationStoreRequest;
use Modules\Communication\Requests\CommunicationUpdateRequest;

class CommunicationController {

    protected CommunicationService $service;



    public function __construct() {
        $this->service = new CommunicationService(new CommunicationRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Communication::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Communication');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Communication::create'
            )
            ->layout('main')
            ->title('ایجاد Communication');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new CommunicationStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/communications');
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
                'Communication::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Communication');
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
                'Communication::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Communication');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new CommunicationUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/communications');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/communications');
    }

}
