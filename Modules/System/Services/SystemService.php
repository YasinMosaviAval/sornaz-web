<?php

namespace Modules\System\Services;

use Modules\System\Repositories\SystemRepository;

class SystemService {

    public function __construct(protected SystemRepository $repository) {
    }


    public function getByPage(string $page): array {
        return $this->repository->findByPage($page);
    }


}
