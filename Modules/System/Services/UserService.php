<?php

namespace Modules\System\Services;

use Modules\System\Repositories\UserRepository;

class UserService {
    public function __construct(protected UserRepository $users) {

    }
}