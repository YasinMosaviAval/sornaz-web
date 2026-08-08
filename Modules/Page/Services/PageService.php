<?php

namespace Modules\Page\Services;

use Modules\Page\Repositories\PageRepository;

class PageService {


    public function __construct(protected PageRepository $repository) {
    }


    public function getByPage(string $page): array {
        return $this->repository->findByPage($page);
    }


}