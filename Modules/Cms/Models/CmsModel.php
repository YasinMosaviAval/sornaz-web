<?php

namespace Modules\Cms\Models;

use Core\database\Model;

class CmsModel extends Model {

    protected string $table = 'cmss';
    protected string $primaryKey = 'cms_id';
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
