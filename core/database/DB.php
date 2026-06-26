<?php

namespace Core\Database;

class DB {
    protected Connection $connection;


    public static function table(string $table): Builder {
        return (new Builder(db()))->table($table);
    }
}