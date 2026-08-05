<?php

namespace Modules\Media\Models;

use Core\database\Model;

class MediaModel extends Model {

    protected string $table = 'medias';
    protected string $primaryKey = 'media_id';
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
