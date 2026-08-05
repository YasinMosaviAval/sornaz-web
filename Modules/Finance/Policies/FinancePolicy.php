<?php

namespace Modules\Finance\Policies;

class FinancePolicy {

    public function view($user,$model): bool {
        return true;
    }

    public function create($user): bool {
        return true;
    }

    public function update($user,$model): bool {
        return true;
    }

    public function delete($user,$model): bool {
        return true;
    }

}
