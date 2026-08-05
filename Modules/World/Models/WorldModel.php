<?php

namespace Modules\World\Models;

use Core\database\Model;

class WorldModel extends Model {

    protected string $table = 'worlds';
    protected string $primaryKey = 'world_id';
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
