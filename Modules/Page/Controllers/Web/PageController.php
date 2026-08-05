<?php

namespace Modules\Page\Controllers\Web;

use Core\http\ResponseFactory;
// use Modules\Page\Services\PageService;
// use Modules\Page\Repositories\PageRepository;
// use Modules\Page\Requests\PageStoreRequest;
// use Modules\Page\Requests\PageUpdateRequest;

class PageController {

    public function home() { return ResponseFactory::view('Page::home')->layout('main')->title('سُرناز | رابط کاربری'); }
    public function aboutUs() { return ResponseFactory::view('Page::about-us')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function contactUs() { return ResponseFactory::view('Page::contact-us')->layout('main')->title('سُرناز | صفحه اصلی'); }


/*
    protected PageService $service;



    public function __construct() {
        $this->service = new PageService(new PageRepository());
    }




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




    public function create() {
        return ResponseFactory::view(
                'Page::create'
            )
            ->layout('main')
            ->title('ایجاد Page');
    }




    public function store() {
        $request = new PageStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/pages');
    }




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




    public function update(int $id) {
        $request = new PageUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/pages');
    }




    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/pages');
    }
*/


}
