<?php

namespace Modules\Page\Models;

use Core\database\Model;

class PageModel extends Model {

    protected string $table = 'pages';
    protected string $primaryKey = 'page_id';
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
