<?php

namespace Modules\Academy\Models;

use Core\database\Model;

class AcademyModel extends Model {

    protected string $table = 'academys';
    protected string $primaryKey = 'academy_id';
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
