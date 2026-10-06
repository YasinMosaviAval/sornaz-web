<?php

namespace Core\database;

use PDO;
use PDOStatement;

/** Keeps all native PDO binding, transactions, locks and exception semantics. */
class PrefixedPDO extends PDO
{
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        return parent::prepare(TableNames::sql($query), $options);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        return parent::query(TableNames::sql($query), $fetchMode, ...$fetchModeArgs);
    }

    public function exec(string $statement): int|false
    {
        return parent::exec(TableNames::sql($statement));
    }
}
