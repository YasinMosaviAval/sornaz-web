<?php

namespace Modules\Finance\Models;

use Core\database\Model;

class FinanceModel extends Model {

    protected string $table = 'finances';
    protected string $primaryKey = 'finance_id';
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
