<?php

namespace Modules\Finance\Services;

use Modules\Finance\Repositories\FinanceRepository;

class FinanceService {

    public function __construct(protected FinanceRepository $repository) {
    }

}
