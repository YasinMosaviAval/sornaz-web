<?php

namespace Core\Database;

abstract class Repository
{
    protected string $table;

    protected string $primaryKey = 'id';

    public function all(): array
    {
        return DB::table(
            $this->table
        )->get();
    }

    public function find(
        int $id
    ): ?array {

        return DB::table(
            $this->table
        )->find(
            $id,
            $this->primaryKey
        );
    }

    public function create(
        array $data
    ): bool {

        return DB::table(
            $this->table
        )->insert($data);
    }

    public function update(
        int $id,
        array $data
    ): bool {

        return DB::table(
            $this->table
        )
        ->where(
            $this->primaryKey,
            $id
        )
        ->update($data);
    }

    public function delete(
        int $id
    ): bool {

        return DB::table(
            $this->table
        )
        ->where(
            $this->primaryKey,
            $id
        )
        ->delete();
    }
}