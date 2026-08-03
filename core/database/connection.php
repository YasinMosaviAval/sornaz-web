<?php

namespace Core\database;

use PDO;

class Connection {
    protected PDO $pdo;

    public function __construct(array $config) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $this->pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }


    public function pdo(): PDO {
        return $this->pdo;
    }


    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }


    public function commit(): bool {
        return $this->pdo->commit();
    }


    public function rollback(): bool {
        return $this->pdo->rollBack();
    }


}