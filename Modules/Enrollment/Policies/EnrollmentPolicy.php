<?php

namespace Modules\Enrollment\Policies;

class EnrollmentPolicy {

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
