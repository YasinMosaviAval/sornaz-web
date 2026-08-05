<?php

namespace Modules\Enrollment\Models;

use Core\database\Model;

class EnrollmentModel extends Model {

    protected string $table = 'enrollments';
    protected string $primaryKey = 'enrollment_id';
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
