<?php

namespace Modules\Availability\Controllers\Web;

class AvailabilityController {



    public function saveException(){
        $this->service->saveException(auth()->id(), $_POST);
        return back();
    }



    public function deleteException(int $id) {
        $this->service->deleteException($id);
        return back();
    }



}