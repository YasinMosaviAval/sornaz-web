<?php

namespace Modules\Academy\Controllers\Web;

use Modules\Academy\Services\AcademyService;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\Academy\Repositories\AcademyRepository;

use Core\Http\ResponseFactory;
use Core\Http\Resources\AcademyResource;
use Core\Http\Resources\ResourceCollection;

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
            'academy.index',
            ['academies' => (new ResourceCollection($items, AcademyResource::class))->resolve()]
        );
    }



    public function show(int $id) {
        return $this->service->find($id);
    }



    public function store(array $request) {
        $request=(new AcademyIndexRequest())->validate($request);
        return $this->service->create($request);
    }



    public function update(int $id, array $request){
        $request=(new AcademyIndexRequest())->validate($request);
        return $this->service->update($id,$request);
    }



    public function destroy(int $id) {
        return $this->service->delete($id);
    }







}