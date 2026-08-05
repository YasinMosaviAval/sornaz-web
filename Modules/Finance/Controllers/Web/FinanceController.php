<?php

namespace Modules\Finance\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Finance\Services\FinanceService;
use Modules\Finance\Repositories\FinanceRepository;
use Modules\Finance\Requests\FinanceStoreRequest;
use Modules\Finance\Requests\FinanceUpdateRequest;

class FinanceController {

    protected FinanceService $service;



    public function __construct() {
        $this->service = new FinanceService(new FinanceRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Finance::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Finance');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Finance::create'
            )
            ->layout('main')
            ->title('ایجاد Finance');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new FinanceStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/finances');
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
                'Finance::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Finance');
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
                'Finance::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Finance');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new FinanceUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/finances');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/finances');
    }

}
