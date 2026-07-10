<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\Resources\ResourceCollection;
use Modules\Academy\Services\AcademyService;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\Academy\Requests\AcademyStoreRequest;
use Modules\Academy\Requests\AcademyUpdateRequest;

use Core\Http\ResponseFactory;
use Core\Validation\ValidationException;
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
        $academy = $this->service->findById($id);
        if (!$academy) {
            abort(404);
        }
        return ResponseFactory::view('Academy::show', ['academy'=>$academy])
            ->layout('dashboard')
            ->title('مشاهده آموزشگاه');
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
        try {
            $request = new AcademyStoreRequest($_POST);
            $data = $request->validated();
            $this->service->create($data);
            return redirect('/academy');
        }
        catch (ValidationException $e) {
            return redirect('/academy/create')->withInput($_POST)->withErrors($e->getErrors());
        }
    }


    
    public function edit(int $id) {
        $academy = $this->service->findById($id);
        if (!$academy) {
            abort(404);
        }
        return ResponseFactory::view('Academy::edit', ['academy' => $academy])
            ->layout('dashboard')
            ->title('ویرایش آموزشگاه');
    }




    public function destroy(int $id) {
        $this->service->delete($id);
        return redirect('/academy');
    }









}