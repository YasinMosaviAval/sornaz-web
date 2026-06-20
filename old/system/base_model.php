<?php

abstract class BaseModel {

  protected Db     $db;
  protected string $table  = '';   // نام جدول — در هر Model تعریف میشه
  protected string $pk     = 'id'; // کلید اصلی

  public function __construct() {
    $this->db = Db::getInstance();
  }


  // ── Basic CRUD ────────────────────────────────────────────────────────────

  public function find(int $id): ?array {
    return $this->db->first(
      "SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = :id LIMIT 1",
      ['id' => $id]
    );
  }

  public function all(string $orderBy = null): array {
    $order = $orderBy ? "ORDER BY $orderBy" : '';
    return $this->db->query("SELECT * FROM `{$this->table}` $order");
  }

  public function where(array $conditions, string $orderBy = null): array {
    [$sql, $params] = $this->buildWhere($conditions);
    $order = $orderBy ? "ORDER BY $orderBy" : '';
    return $this->db->query(
      "SELECT * FROM `{$this->table}` WHERE $sql $order",
      $params
    );
  }

  public function firstWhere(array $conditions): ?array {
    [$sql, $params] = $this->buildWhere($conditions);
    return $this->db->first(
      "SELECT * FROM `{$this->table}` WHERE $sql LIMIT 1",
      $params
    );
  }

  public function create(array $data): int {
    $cols   = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
    $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
    return $this->db->insert(
      "INSERT INTO `{$this->table}` ($cols) VALUES ($placeholders)",
      $data
    );
  }

  public function update(int $id, array $data): int {
    $sets = implode(', ', array_map(fn($k) => "`$k` = :$k", array_keys($data)));
    $data[$this->pk] = $id;
    return $this->db->modify(
      "UPDATE `{$this->table}` SET $sets WHERE `{$this->pk}` = :{$this->pk}",
      $data
    );
  }

  public function delete(int $id): int {
    return $this->db->modify(
      "DELETE FROM `{$this->table}` WHERE `{$this->pk}` = :id",
      ['id' => $id]
    );
  }

  public function exists(int $id): bool {
    return $this->db->exists(
      "SELECT `{$this->pk}` FROM `{$this->table}` WHERE `{$this->pk}` = :id LIMIT 1",
      ['id' => $id]
    );
  }

  public function count(array $conditions = []): int {
    if (empty($conditions)) {
      return (int) $this->db->value("SELECT COUNT(*) FROM `{$this->table}`");
    }
    [$sql, $params] = $this->buildWhere($conditions);
    return (int) $this->db->value(
      "SELECT COUNT(*) FROM `{$this->table}` WHERE $sql",
      $params
    );
  }


  // ── Pagination ────────────────────────────────────────────────────────────

  /**
   * Return paginated results
   * Returns: ['data' => [...], 'total' => N, 'page' => N, 'perPage' => N, 'pages' => N]
   */
  public function paginate(int $page = 1, int $perPage = 20, array $conditions = [], string $orderBy = ''): array {
    $page    = max(1, $page);
    $offset  = ($page - 1) * $perPage;
    $order   = $orderBy ? "ORDER BY $orderBy" : '';

    if (empty($conditions)) {
      $total = $this->count();
      $rows  = $this->db->query(
        "SELECT * FROM `{$this->table}` $order LIMIT :limit OFFSET :offset",
        ['limit' => $perPage, 'offset' => $offset]
      );
    } else {
      [$whereSql, $params] = $this->buildWhere($conditions);
      $total = $this->count($conditions);
      $params['limit']  = $perPage;
      $params['offset'] = $offset;
      $rows = $this->db->query(
        "SELECT * FROM `{$this->table}` WHERE $whereSql $order LIMIT :limit OFFSET :offset",
        $params
      );
    }

    return [
      'data'    => $rows,
      'total'   => $total,
      'page'    => $page,
      'perPage' => $perPage,
      'pages'   => (int) ceil($total / $perPage),
    ];
  }


  // ── Internal ──────────────────────────────────────────────────────────────

  private function buildWhere(array $conditions): array {
    $parts  = [];
    $params = [];
    foreach ($conditions as $col => $val) {
      $parts[]      = "`$col` = :$col";
      $params[$col] = $val;
    }
    return [implode(' AND ', $parts), $params];
  }
}
