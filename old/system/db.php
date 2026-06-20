<?php

class Db {

  private mysqli $connection;
  private static ?Db $instance = null;


  // ── Singleton ─────────────────────────────────────────────────────────────

  public static function getInstance(array $option = null): Db {
    if (self::$instance === null) {
      self::$instance = new Db($option);
    }
    return self::$instance;
  }


  // ── Constructor ───────────────────────────────────────────────────────────

  public function __construct(array $option = null) {
    if ($option !== null) {
      $host = $option['host'];
      $user = $option['user'];
      $pass = $option['pass'];
      $name = $option['name'];
    } else {
      global $config;
      $host = $config['db']['host'];
      $user = $config['db']['user'];
      $pass = $config['db']['pass'];
      $name = $config['db']['name'];
    }

    $this->connection = new mysqli($host, $user, $pass, $name);

    if ($this->connection->connect_error) {
      $this->fail("Connection failed: " . $this->connection->connect_error);
    }

    $this->connection->set_charset('utf8mb4');
  }


  // ── Core: Prepared Statement Runner ──────────────────────────────────────

  /**
   * Run any SQL with named (:key) or positional (?) placeholders.
   * Named placeholders are converted to positional before binding.
   *
   * Examples:
   *   $db->run("SELECT * FROM users WHERE id = :id", ['id' => 5]);
   *   $db->run("INSERT INTO log (msg) VALUES (?)", ['hello']);
   */
  private function run(string $sql, array $data = []): mysqli_stmt|bool {
    // Convert named placeholders (:key) → positional (?)
    $values = [];
    if (!empty($data) && array_keys($data) !== range(0, count($data) - 1)) {
      // Associative array → named placeholders
      foreach ($data as $key => $value) {
        $sql     = str_replace(":$key", '?', $sql);
        $values[] = $value;
      }
    } else {
      $values = array_values($data);
    }
    // echo  $sql;
    // hr();

    $stmt = $this->connection->prepare($sql);
    if (!$stmt) {
      $this->fail("Prepare failed for: $sql — " . $this->connection->error);
    }

    if (!empty($values)) {
      $types = '';
      foreach ($values as $v) {
        if (is_int($v))    $types .= 'i';
        elseif (is_float($v)) $types .= 'd';
        else               $types .= 's';
      }
      $stmt->bind_param($types, ...$values);
    }

    if (!$stmt->execute()) {
      $this->fail("Execute failed for: $sql — " . $stmt->error);
    }

    return $stmt;
  }


  // ── Public Query Methods ──────────────────────────────────────────────────

  /**
   * SELECT → returns all rows as associative array
   */
  public function query(string $sql, array $data = []): array {
    $stmt   = $this->run($sql, $data);
    $result = $stmt->get_result();
    $stmt->close();

    if (!$result) return [];

    $rows = [];
    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    return $rows;
  }


  /**
   * SELECT → returns first row (or a specific field of first row)
   */
  public function first(string $sql, array $data = [], string $field = null): mixed {
    $rows = $this->query($sql, $data);
    if (empty($rows)) return null;
    if ($field !== null) return $rows[0][$field] ?? null;
    return $rows[0];
  }


  /**
   * SELECT → returns a single scalar value
   */
  public function value(string $sql, array $data = []): mixed {
    $row = $this->first($sql, $data);
    if ($row === null) return null;
    return array_values($row)[0];
  }


  /**
   * INSERT → returns last inserted ID
   */
  public function insert(string $sql, array $data = []): int {
    $stmt   = $this->run($sql, $data);
    $lastId = $stmt->insert_id;
    $stmt->close();
    return $lastId;
  }


  /**
   * UPDATE / DELETE → returns affected rows
   */
  public function modify(string $sql, array $data = []): int {
    $stmt         = $this->run($sql, $data);
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    return $affectedRows;
  }


  /**
   * Check if a row exists
   */
  public function exists(string $sql, array $data = []): bool {
    return $this->first($sql, $data) !== null;
  }


  // ── Transaction Support ───────────────────────────────────────────────────

  public function beginTransaction(): void {
    $this->connection->begin_transaction();
  }

  public function commit(): void {
    $this->connection->commit();
  }

  public function rollback(): void {
    $this->connection->rollback();
  }

  /**
   * Run a callable inside a transaction.
   * Automatically commits on success, rolls back on exception.
   *
   * Usage:
   *   $db->transaction(function($db) {
   *     $db->insert(...);
   *     $db->modify(...);
   *   });
   */
  public function transaction(callable $callback): mixed {
    $this->beginTransaction();
    try {
      $result = $callback($this);
      $this->commit();
      return $result;
    } catch (Throwable $e) {
      $this->rollback();
      throw $e;
    }
  }


  // ── Helpers ───────────────────────────────────────────────────────────────

  public function connection(): mysqli {
    return $this->connection;
  }

  public function close(): void {
    $this->connection->close();
  }

  private function fail(string $message): never {
    global $config;
    $isLocal = ($config['app']['env'] ?? 'local') === 'local';

    if ($isLocal) {
      throw new RuntimeException("[DB Error] $message");
    } else {
      // در production جزئیات رو لاگ کن، به کاربر نشون نده
      error_log("[DB Error] $message");
      throw new RuntimeException("A database error occurred. Please try again later.");
    }
  }
}
