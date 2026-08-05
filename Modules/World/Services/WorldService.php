<?php

namespace Modules\World\Services;

use Modules\World\Repositories\WorldRepository;

class WorldService {

    public function __construct(protected WorldRepository $repository) {
    }

}
