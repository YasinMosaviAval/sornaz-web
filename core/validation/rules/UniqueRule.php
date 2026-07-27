<?php

namespace Core\Validation\Rules;

use Core\Database\DB;
use Core\Validation\Rule;

class UniqueRule implements Rule {


    protected string $table;
    protected string $column;



    public function __construct(string $table, string $column) {
        $this->table = $table;
        $this->column = $column;
    }



    public function validate(string $field, mixed $value): bool {
        if ($value === null || $value === '') {
            return true;
        }
        return !DB::table($this->table)->where($this->column, $value)->exists();
    }



    public function message(string $field): string {
        return "{$field} قبلاً ثبت شده است.";
    }




}