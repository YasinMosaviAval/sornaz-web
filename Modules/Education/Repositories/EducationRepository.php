<?php

namespace Modules\Education\Repositories;

use Core\database\Repository;
use Modules\Education\Models\EducationModel;

class EducationRepository extends Repository {

    protected ?string $model = EducationModel::class;
    protected string $table = 'educations';
    protected string $primaryKey = 'education_id';

}
