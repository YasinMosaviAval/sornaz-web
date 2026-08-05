<?php

namespace Modules\Communication\Repositories;

use Core\database\Repository;
use Modules\Communication\Models\CommunicationModel;

class CommunicationRepository extends Repository {

    protected ?string $model = CommunicationModel::class;
    protected string $table = 'communications';
    protected string $primaryKey = 'communication_id';

}
