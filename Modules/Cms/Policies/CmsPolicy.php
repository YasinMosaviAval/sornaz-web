<?php

namespace Modules\Cms\Policies;

class CmsPolicy {

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
