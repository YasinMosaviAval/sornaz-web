<?php

namespace Modules\Translation\Services;

use Modules\Translation\Repositories\TranslationRepository;

class TranslationService {

    public function __construct(protected TranslationRepository $repository) {
    }

}
