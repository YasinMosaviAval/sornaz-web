<?php

namespace Modules\Profile\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Profile\Services\ProfileService;
use Modules\Profile\Repositories\ProfileRepository;
use Modules\Profile\Requests\ProfileStoreRequest;
use Modules\Profile\Requests\ProfileUpdateRequest;

class ProfileController {

    protected ProfileService $service;



    public function __construct() {
        $this->service = new ProfileService(new ProfileRepository());
    }



    /**
     * لیست
     */
    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view(
                'Profile::index',
                [
                    'items' => $items
                ]
            )
            ->layout('main')
            ->title('Profile');
    }



    /**
     * فرم ایجاد
     */
    public function create() {
        return ResponseFactory::view(
                'Profile::create'
            )
            ->layout('main')
            ->title('ایجاد Profile');
    }



    /**
     * ذخیره
     */
    public function store() {
        $request = new ProfileStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/profiles');
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
                'Profile::show',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('نمایش Profile');
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
                'Profile::edit',
                [
                    'item' => $item
                ]
            )
            ->layout('main')
            ->title('ویرایش Profile');
    }



    /**
     * بروزرسانی
     */
    public function update(int $id) {
        $request = new ProfileUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/profiles');
    }



    /**
     * حذف
     */
    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/profiles');
    }

}
