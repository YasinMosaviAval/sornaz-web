<?php

namespace Modules\System\Controllers;

use Modules\System\Services\UserService;

class UserController {
    public function __construct(protected UserService $service) {

    }

    public function index() {
        return 'Users Page';
    }

    public function show(int $id, int $post_id) {
        return "User: {$id} | Post: {$post_id}";
    }
}