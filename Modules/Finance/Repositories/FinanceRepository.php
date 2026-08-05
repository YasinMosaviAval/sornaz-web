<?php

namespace Modules\Finance\Repositories;

use Core\database\Repository;
use Modules\Finance\Models\FinanceModel;

class FinanceRepository extends Repository {

    protected ?string $model = FinanceModel::class;
    protected string $table = 'finances';
    protected string $primaryKey = 'finance_id';

}
