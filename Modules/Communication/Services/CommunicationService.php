<?php

namespace Modules\Communication\Services;

use Modules\Communication\Repositories\CommunicationRepository;

class CommunicationService {

    public function __construct(protected CommunicationRepository $repository) {
    }

}
