<?php

namespace Modules\System\Models;

use Core\Database\Builder;
use Core\Database\Model;
use Core\Database\SoftDeletes;
use Modules\Content\Models\Post;
use Modules\System\Models\Role;

class User extends Model {
    protected static string $table = 'users';
    protected static string $primaryKey = 'user_id';
    protected array $casts = [
        'user_id' => 'int',
        'status' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected array $fillable = [
        'username',
        'email',
        // 'password',
        // 'status'
    ];

    use SoftDeletes;

    public function posts() {
        return $this->hasMany(
            Post::class,
            'author_id',
            'user_id'
        );
    }



    public function roles() {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',
            'role_id',
            'user_id',
            'role_id'
        );
    }

    public function scopeActive(Builder $query) {
        return $query->where(
            'status',
            'approved'
            // 'active'
        );
    }


    public function scopePending(Builder $query) {
        return $query->where(
            'status',
            'pending'
        );
    }

    public function scopeVisible(Builder $query) {
        return $query->where(
            'visibility',
            'public'
        );
    }


    public function profile() {
        return $this->hasOne(
            Profile::class,
            'user_id',
            'user_id'
        );
    }
}