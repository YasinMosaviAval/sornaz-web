<?php

namespace Modules\System\Models;

use Core\database\Model;

class SystemModel extends Model {

    protected string $table = 'systems';
    protected string $primaryKey = 'system_id';
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
