<?php

namespace Core\Database;

use Core\Database\Scopes\SoftDeletingScope;

trait SoftDeletes
{
    // protected static array $globalScopes = [];

    public function delete(): bool {
        return DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function restore(): bool {
        return DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->update(['deleted_at' => null]);
    }

    public function forceDelete(): bool {
        return DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->delete();
    }


    protected static function bootSoftDeletes(): void {
        static::addGlobalScope(
            new SoftDeletingScope()
        );
    }




}