<?php

namespace Modules\Profile\Models;

use Core\database\Model;

class ProfileModel extends Model {

    protected string $table = 'profiles';
    protected string $primaryKey = 'profile_id';
    protected array $fillable = [
        // 'title',
        // 'status',
    ];
    protected array $casts = [
        // 'created_at' => 'datetime',
    ];
    protected bool $timestamps = true;
    protected bool $softDeletes = true;


}
