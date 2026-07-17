<?php

namespace Modules\Availability\Controllers\Web;

use Modules\Availability\Services\AvailabilityService;


class AvailabilityController {


    protected AvailabilityService $service;



    public function __construct() {
        $this->service=app()->container()->make(AvailabilityService::class);
    }



    public function saveException() {
        $this->service->saveException(auth()->id(), $_POST);
        return back();
    }



    public function deleteException(int $id) {
        $this->service->deleteException($id);
        return back();
    }



}