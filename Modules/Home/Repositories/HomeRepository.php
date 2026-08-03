<?php

namespace Modules\Home\Repositories;

use Core\Database\Repository;

class HomeRepository extends Repository {
    
    protected string $table='homes';
    protected string $primaryKey='home_id';
    protected ?string $model=null;

}