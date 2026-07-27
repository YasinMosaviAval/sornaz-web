<?php

namespace Core\Translation;

use Core\Database\Model;

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