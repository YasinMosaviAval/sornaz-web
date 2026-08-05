<?php

namespace Modules\Academy\Repositories;

use Core\database\Repository;
use Modules\Academy\Models\AcademyModel;

class AcademyRepository extends Repository {

    protected ?string $model = AcademyModel::class;
    protected string $table = 'academys';
    protected string $primaryKey = 'academy_id';

}
