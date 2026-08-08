<?php

namespace Modules\System\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\System\Services\SystemService;


class SystemController {



    public function __construct(protected SystemService $service) {
    }

    public function login() {
        return ResponseFactory::view('System::login', ['authentication' => $this->service->getByPage('authentication')])->layout('auth')->title('سُرناز | ورود');
    }

    public function register() {
        return ResponseFactory::view('System::register', ['authentication' => $this->service->getByPage('authentication')])->layout('auth')->title('سُرناز | ثبت نام');
    }

    public function forgotPassword() {
        return ResponseFactory::view('System::forgot-password', ['authentication' => $this->service->getByPage('authentication')])->layout('auth')->title('سُرناز | فراموشی رمز عبور');
    }

}

/*
    protected SystemService $service;



    public function __construct() {
        $this->service = new SystemService(new SystemRepository());
    }




    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'System::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('System');
    }




    public function create() {
        return ResponseFactory::view(
                'System::create'
            )
            ->layout('main')
            ->title('ایجاد System');
    }




    public function store() {
        $request = new SystemStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/systems');
    }




    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view(
                'System::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش System');
    }




    public function edit(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view(
                'System::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش System');
    }




    public function update(int $id) {
        $request = new SystemUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/systems');
    }




    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/systems');
    }
*/