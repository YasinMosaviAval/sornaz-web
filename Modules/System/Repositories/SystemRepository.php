<?php

namespace Modules\System\Repositories;

use Core\database\Repository;
use Modules\System\Models\SystemModel;

class SystemRepository extends Repository {

    protected ?string $model = SystemModel::class;
    protected string $table = 'systems';
    protected string $primaryKey = 'system_id';

}
