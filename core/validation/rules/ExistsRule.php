<?php

namespace Core\Validation\Rules;

use Core\Database\DB;
use Core\Validation\Rule;

class ExistsRule implements Rule {



    public function __construct(protected string $table, protected string $column) {
    }



    public function validate(string $field, mixed $value): bool {
        if ($value === null || $value === '') {
            return true;
        }
        return DB::table($this->table)->where($this->column, $value)->exists();
    }



    public function message(string $field): string {
        return "{$field} وجود ندارد.";
    }



}