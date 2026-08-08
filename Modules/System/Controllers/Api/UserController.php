<?php
namespace Modules\System\Controllers\Api;

use Modules\System\Services\UserService;

class UserController {

    public function __construct(protected UserService $service) {
    }

}