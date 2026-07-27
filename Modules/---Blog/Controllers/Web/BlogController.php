<?php

namespace Modules\Blog\Controllers\Web;

use Core\Http\ResponseFactory;
use Modules\Blog\Services\BlogService;
use Modules\Blog\Requests\BlogStoreRequest;
use Modules\Blog\Requests\BlogUpdateRequest;
use Modules\Blog\Services\CategoryService;

class BlogController {

    protected BlogService $service;
    protected CategoryService $categories;

    public function __construct(BlogService $service, CategoryService $categories){
        $this->service=$service;
        $this->categories=$categories;
    }


    public function index() {
        return ResponseFactory::view(
            'Blog::index',
            [
                'posts'=>$this->service->paginate(),
                'categories'=>$this->categories->all(),
                'latestPosts'=>$this->service->latest(),
                'popularPosts'=>$this->service->popular(),
                'page'=>1,
                'pages'=>1
            ]
        )
        ->layout('main')
        ->title('وبلاگ');
    }



    public function create() {
        return ResponseFactory::view('Blog::create')
            ->layout('main')
            ->title('ایجاد Blog');
    }



    public function store() {
        $request = new BlogStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/blogs');
    }



    public function show(string $slug) {
        $item = $this->service->findBySlug($slug);
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
        ->title($item->title());
    }



    public function edit(int $id) {
        $item = $this->service->find($id);
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



    public function update(int $id) {
        $request = new BlogUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/blogs');
    }



    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/blogs');
    }

}