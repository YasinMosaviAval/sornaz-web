<?php

namespace Modules\Enrollment\Repositories;

use Core\database\Repository;
use Modules\Enrollment\Models\EnrollmentModel;

class EnrollmentRepository extends Repository {

    protected ?string $model = EnrollmentModel::class;
    protected string $table = 'enrollments';
    protected string $primaryKey = 'enrollment_id';

}
