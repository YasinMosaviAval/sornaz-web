<?php

namespace Modules\Blog\Controllers\Web;

use Core\Http\ResponseFactory;
use Modules\Blog\Services\BlogService;
use Modules\Blog\Repositories\BlogRepository;
use Modules\Blog\Requests\BlogStoreRequest;
use Modules\Blog\Requests\BlogUpdateRequest;

class BlogController {

    protected BlogService $service;



    public function __construct() {
        $this->service = new BlogService(new BlogRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Blog::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Blog');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Blog::create'
            )
            ->layout('main')
            ->title('ایجاد Blog');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new BlogStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/blogs');
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
                'Blog::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Blog');
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
                'Blog::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Blog');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new BlogUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/blogs');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/blogs');
    }

}