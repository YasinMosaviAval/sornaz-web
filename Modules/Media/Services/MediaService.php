<?php

namespace Modules\Media\Services;

use Modules\Media\Repositories\MediaRepository;

class MediaService {

    public function __construct(protected MediaRepository $repository) {
    }

}
