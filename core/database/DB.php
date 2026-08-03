<?php

namespace Core\database;

class DB {


    public static function table(string $table): Builder {
        return (new Builder(db()))->table($table);
    }


    public function lastInsertId(): string {
        return $this->connection
            ->pdo()
            ->lastInsertId();
    }




}