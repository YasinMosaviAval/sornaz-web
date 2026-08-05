<?php

namespace Modules\Academy\Services;

use Modules\Academy\Repositories\AcademyRepository;

class AcademyService {

    public function __construct(protected AcademyRepository $repository) {
    }

}
