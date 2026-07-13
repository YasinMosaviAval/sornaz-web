<?php

namespace Modules\Teacher\Repositories;

use Core\Database\Repository;

class TeacherRepository extends Repository
{

    protected string $table='teachers';

    protected string $primaryKey='teacher_id';

    protected ?string $model=null;

}