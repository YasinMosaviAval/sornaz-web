<?php

namespace Modules\System\Repositories;

use Core\Database\Repository;
use Modules\System\Contracts\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface {
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

}