<?php

namespace Modules\Education\Models;

use Core\database\Model;

class EducationModel extends Model {

    protected string $table = 'educations';
    protected string $primaryKey = 'education_id';
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
