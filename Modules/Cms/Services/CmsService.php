<?php

namespace Modules\Cms\Services;

use Modules\Cms\Repositories\CmsRepository;

class CmsService {

    public function __construct(protected CmsRepository $repository) {
    }

}
