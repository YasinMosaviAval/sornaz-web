<?php

namespace Modules\System\Models;

use Core\Database\Model;
use Modules\System\Models\User;

class Role extends Model {
    protected static string $table = 'access_system_roles';
    protected static string $primaryKey = 'role_id';

    public function users() {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id',
            'role_id',
            'user_id'
        );
    }

}