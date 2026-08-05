<?php

namespace Modules\World\Repositories;

use Core\database\Repository;
use Modules\World\Models\WorldModel;

class WorldRepository extends Repository {

    protected ?string $model = WorldModel::class;
    protected string $table = 'worlds';
    protected string $primaryKey = 'world_id';

}
