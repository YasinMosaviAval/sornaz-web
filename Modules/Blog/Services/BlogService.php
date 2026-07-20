<?php

namespace Modules\Blog\Services;

use Modules\Blog\Repositories\BlogRepository;

class BlogService {

    public function __construct(protected BlogRepository $repository) {
    }

}