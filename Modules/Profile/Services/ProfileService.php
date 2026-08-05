<?php

namespace Modules\Profile\Services;

use Modules\Profile\Repositories\ProfileRepository;

class ProfileService {

    public function __construct(protected ProfileRepository $repository) {
    }

}
