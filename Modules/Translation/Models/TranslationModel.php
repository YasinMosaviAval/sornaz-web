<?php

namespace Modules\Translation\Models;

use Core\database\Model;

class TranslationModel extends Model {

    protected string $table = 'translations';
    protected string $primaryKey = 'translation_id';
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
