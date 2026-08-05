<?php

namespace Modules\Page\Controllers\Web;

use Core\Http\ResponseFactory;
use Modules\Page\Services\PageService;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Requests\PageStoreRequest;
use Modules\Page\Requests\PageUpdateRequest;

class PageController {

    protected PageService $service;



    public function __construct() {
        $this->service = new PageService(new PageRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Page::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Page');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Page::create'
            )
            ->layout('main')
            ->title('ایجاد Page');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new PageStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/pages');
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
                'Page::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Page');
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
                'Page::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Page');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new PageUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/pages');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/pages');
    }

}
