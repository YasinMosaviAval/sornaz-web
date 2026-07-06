<?php

namespace Modules\Academy\Controllers\Api;

use Core\http\Resources\ResourceCollection;
use Modules\Academy\Resources\AcademyResource;
use Modules\Academy\Services\AcademyService;
use Modules\Academy\Requests\AcademyIndexRequest;

use Core\Http\ResponseFactory;

class AcademyController {


    public function __construct(protected AcademyService $service) {
    }



    public function index() {
        $request = new AcademyIndexRequest($_GET);
        $items = $this->service->paginate($request);
        return ResponseFactory::json(
            (new ResourceCollection($items, AcademyResource::class))->resolve()
        );
    }





}