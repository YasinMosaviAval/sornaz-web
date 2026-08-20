<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyService;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\Academy\Requests\AcademyStoreRequest;
use Modules\Academy\Requests\AcademyUpdateRequest;

class AcademyController {

    protected AcademyService $service;



    public function __construct() {
        $this->service = new AcademyService(new AcademyRepository());
    }



    public function index() {
        $items = $this->service->all();
        return ResponseFactory::view('Academy::index', ['items' => $items])->layout('main')->title('Academy');
    }



    public function create() {
        return ResponseFactory::view('Academy::create')->layout('main')->title('ایجاد Academy');
    }



    public function store() {
        $request = new AcademyStoreRequest($_POST);
        $data = $request->validated();
        $this->service->create($data);
        return redirect('/academy');
    }



    public function show(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view('Academy::show', ['item' => $item])->layout('main')->title('نمایش Academy');
    }



    public function edit(int $id) {
        $item = $this->service->findById($id);
        if (!$item) {
            abort(404);
        }
        return ResponseFactory::view('Academy::edit', ['item' => $item])->layout('main')->title('ویرایش Academy');
    }



    public function update(int $id) {
        $request = new AcademyUpdateRequest($_POST);
        $data = $request->validated();
        $this->service->update($id, $data);
        return redirect('/academy');
    }



    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/academy');
    }



}
