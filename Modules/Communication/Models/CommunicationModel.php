<?php

namespace Modules\Communication\Models;

use Core\database\Model;

class CommunicationModel extends Model {

    protected string $table = 'communications';
    protected string $primaryKey = 'communication_id';
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
