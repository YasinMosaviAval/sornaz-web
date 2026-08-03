<?php

namespace Core\translation;

use Core\database\Model;

class Translation extends Model {

    protected static string $table = 'translations';
    protected static string $primaryKey = 'translation_id';
    protected array $fillable = [
        'table_name',
        'table_id',
        'locale',
        'field',
        'value',
        'version',
    ];



}