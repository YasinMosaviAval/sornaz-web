<?php

namespace Modules\Enrollment\Services;

use Modules\Enrollment\Repositories\EnrollmentRepository;

class EnrollmentService {

    public function __construct(protected EnrollmentRepository $repository) {
    }

}
