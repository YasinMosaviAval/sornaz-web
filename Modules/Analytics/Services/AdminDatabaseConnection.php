<?php

namespace Modules\Analytics\Services;

use PDO;
use PDOStatement;
use RuntimeException;

/** Physical database names must never pass through the application's logical-name mapper. */
final class AdminDatabaseConnection
{
    public function __construct(private PDO $pdo) {}

    public function driver(): string { return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME); }

    public function statement(string $sql, array $bindings = []): PDOStatement
    {
        $statement = (new \ReflectionMethod(PDO::class, 'prepare'))->invoke($this->pdo, $sql);
        if (!$statement || !$statement->execute($bindings)) throw new RuntimeException('خواندن دیتابیس ناموفق بود.', 503);
        return $statement;
    }

    public function all(string $sql, array $bindings = []): array
    {
        $statement = $this->statement($sql, $bindings);
        try { return $statement->fetchAll(PDO::FETCH_ASSOC); }
        finally { $statement->closeCursor(); }
    }

    public function scalar(string $sql, array $bindings = []): mixed
    {
        $statement = $this->statement($sql, $bindings);
        try { return $statement->fetchColumn(); }
        finally { $statement->closeCursor(); }
    }

    public function snapshot(callable $work): mixed
    {
        if ($this->pdo->inTransaction()) throw new RuntimeException('دیتابیس در حال انجام عملیات دیگری است؛ دوباره تلاش کنید.', 409);
        if ($this->driver() === 'mysql') {
            $this->statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ')->closeCursor();
            $this->statement('START TRANSACTION WITH CONSISTENT SNAPSHOT, READ ONLY')->closeCursor();
        } else $this->pdo->beginTransaction();
        try { return $work(); }
        finally { if ($this->pdo->inTransaction()) $this->pdo->rollBack(); }
    }

    public function each(string $sql, callable $work): void
    {
        $buffered = $this->driver() === 'mysql' ? $this->pdo->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY) : null;
        $emulated = $this->driver() === 'mysql' ? $this->pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES) : null;
        $stringified = null;
        $statement = null;
        try {
            if ($buffered !== null) $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            if ($emulated !== null) $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            if ($emulated !== null) {
                // PDO MySQL supports setting this attribute but not reading it on all versions.
                $probe = $this->statement('SELECT CAST(1 AS SIGNED)');
                $stringified = is_string($probe->fetchColumn());
                $probe->closeCursor();
            }
            if ($stringified !== null) $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $statement = $this->statement($sql);
            while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) $work($row);
        } finally {
            $statement?->closeCursor();
            if ($buffered !== null) $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $buffered);
            if ($emulated !== null) $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulated);
            if ($stringified !== null) $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, $stringified);
        }
    }

    public static function identifier(string $name): string { return '`' . str_replace('`', '``', $name) . '`'; }
}
