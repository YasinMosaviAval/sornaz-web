<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\Resources\ResourceCollection;
use Modules\Academy\Services\AcademyService;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\Academy\Requests\AcademyStoreRequest;
use Modules\Academy\Requests\AcademyUpdateRequest;

use Core\Http\ResponseFactory;
use Modules\Academy\Resources\AcademyResource;

class AcademyController {


    protected AcademyService $service;



    public function __construct() {
        $this->service=new AcademyService(
            new AcademyRepository()
        );
    }



    public function index() {
        $request = new AcademyIndexRequest($_GET);
        $items = $this->service->paginate($request);
        return ResponseFactory::view(
            'Academy::index',
            ['academies'=>(new ResourceCollection($items, AcademyResource::class))->resolve()]
        )
        ->layout('dashboard')
        ->title('مدیریت آموزشگاه‌ها');
    }



    public function show(int $id) {
        return ResponseFactory::view(
            'Academy::show',
            ['academy' => AcademyResource::make($this->service->findById($id))->resolve()]
        );
    }



    public function update(int $id) {
        $request = new AcademyUpdateRequest($_POST);
        $data = $request->validate();
        $this->service->update($id, $data);
        return redirect('/academy');
    }



    public function create() {
        return ResponseFactory::view('Academy::create')->layout('dashboard')->title('ایجاد آموزشگاه');
    }



    public function store() {
        $request = new AcademyStoreRequest($_POST);
        $data = $request->validate();
        $this->service->create($data);
        return redirect('/academy');
    }



    public function edit(int $id) {
        return ResponseFactory::view(
            'Academy::edit',
            ['academy' => AcademyResource::make($this->service->findById($id))->resolve()]
        );
    }



    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/academy');
    }









}