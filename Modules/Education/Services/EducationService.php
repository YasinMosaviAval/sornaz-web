<?php

namespace Modules\Education\Services;

use Modules\Education\Repositories\EducationRepository;

class EducationService {

    public function __construct(protected EducationRepository $repository) {
    }

}
