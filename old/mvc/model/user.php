<?php

/**
 * mvc/model/user.php
 *
 * جدول: users
 * Soft Delete: بله — deleted_at
 */
class UserModel extends BaseModel {

  protected string $table = 'users';
  protected string $pk    = 'id';


  // ══════════════════════════════════════════════════
  // READ
  // ══════════════════════════════════════════════════

  /**
   * یه کاربر با id — فقط اگه حذف نشده باشه
   */
  public function findById(int $id): ?array {
    return $this->db->first(
      "SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1",
      ['id' => $id]
    );
  }

  /**
   * یه کاربر با email
   */
  public function findByEmail(string $email): ?array {
    return $this->db->first(
      "SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1",
      ['email' => $email]
    );
  }

  /**
   * یه کاربر با username
   */
  public function findByUsername(string $username): ?array {
    return $this->db->first(
      "SELECT * FROM users WHERE username = :username AND deleted_at IS NULL LIMIT 1",
      ['username' => $username]
    );
  }

  /**
   * همه کاربران — بدون حذف‌شده‌ها
   */
  public function getAll(string $orderBy = 'created_at DESC'): array {
    return $this->db->query(
      "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY $orderBy"
    );
  }

  /**
   * کاربران با وضعیت خاص
   */
  public function getByStatus(string $status): array {
    return $this->db->query(
      "SELECT * FROM users
       WHERE activity_status = :status AND deleted_at IS NULL
       ORDER BY created_at DESC",
      ['status' => $status]
    );
  }

  /**
   * جستجو در name/email/phone
   */
  public function search(string $keyword, int $limit = 20): array {
    $keyword = "%$keyword%";
    return $this->db->query(
      "SELECT * FROM users
       WHERE deleted_at IS NULL
         AND (email LIKE :k OR username LIKE :k OR phone LIKE :k)
       ORDER BY created_at DESC
       LIMIT :limit",
      ['k' => $keyword, 'limit' => $limit]
    );
  }

  /**
   * صفحه‌بندی — با فیلتر اختیاری
   */
  public function paginate(
    int    $page    = 1,
    int    $perPage = 20,
    array  $conditions = [],
    string $orderBy = 'created_at DESC'
  ): array {
    $where  = ['deleted_at IS NULL'];
    $params = [];

    if (!empty($conditions['status'])) {
      $where[]           = 'activity_status = :status';
      $params['status']  = $conditions['status'];
    }

    if (!empty($conditions['gender'])) {
      $where[]           = 'gender = :gender';
      $params['gender']  = $conditions['gender'];
    }

    if (!empty($conditions['search'])) {
      $where[]      = '(email LIKE :search OR username LIKE :search OR phone LIKE :search)';
      $params['search'] = '%' . $conditions['search'] . '%';
    }

    $whereSql = implode(' AND ', $where);

    $total = (int) $this->db->value(
      "SELECT COUNT(*) FROM users WHERE $whereSql",
      $params
    );

    $offset          = ($page - 1) * $perPage;
    $params['limit'] = $perPage;
    $params['offset'] = $offset;

    $rows = $this->db->query(
      "SELECT * FROM users
       WHERE $whereSql
       ORDER BY $orderBy
       LIMIT :limit OFFSET :offset",
      $params
    );

    return [
      'data'    => $rows,
      'total'   => $total,
      'page'    => $page,
      'perPage' => $perPage,
      'pages'   => (int) ceil($total / $perPage),
    ];
  }


  // ══════════════════════════════════════════════════
  // CREATE
  // ══════════════════════════════════════════════════

  /**
   * ثبت کاربر جدید
   * password باید قبل از پاس دادن هش شده باشه (encryptPassword)
   */
  public function create(array $data): int {
    return $this->db->insert(
      "INSERT INTO users
        (email, username, password, phone, national_code, gender, birthday,
         activity_status, register_time, created_by)
       VALUES
        (:email, :username, :password, :phone, :national_code, :gender, :birthday,
         :activity_status, :register_time, :created_by)",
      [
        'email'           => $data['email']           ?? null,
        'username'        => $data['username']        ?? null,
        'password'        => $data['password'],
        'phone'           => $data['phone']           ?? null,
        'national_code'   => $data['national_code']   ?? null,
        'gender'          => $data['gender']          ?? null,
        'birthday'        => $data['birthday']        ?? null,
        'activity_status' => $data['activity_status'] ?? 'active',
        'register_time'   => getCurrentDateTime(),
        'created_by'      => $data['created_by']      ?? null,
      ]
    );
  }


  // ══════════════════════════════════════════════════
  // UPDATE
  // ══════════════════════════════════════════════════

  /**
   * آپدیت اطلاعات پایه کاربر
   */
  public function update(int $id, array $data): int {
    return $this->db->modify(
      "UPDATE users SET
        email          = :email,
        username       = :username,
        phone          = :phone,
        national_code  = :national_code,
        gender         = :gender,
        birthday       = :birthday
       WHERE id = :id AND deleted_at IS NULL",
      [
        'email'         => $data['email']         ?? null,
        'username'      => $data['username']       ?? null,
        'phone'         => $data['phone']          ?? null,
        'national_code' => $data['national_code']  ?? null,
        'gender'        => $data['gender']         ?? null,
        'birthday'      => $data['birthday']       ?? null,
        'id'            => $id,
      ]
    );
  }

  /**
   * تغییر رمز عبور
   */
  public function updatePassword(int $id, string $hashedPassword): int {
    return $this->db->modify(
      "UPDATE users SET password = :password WHERE id = :id AND deleted_at IS NULL",
      ['password' => $hashedPassword, 'id' => $id]
    );
  }

  /**
   * تغییر وضعیت: active | inactive | banned
   */
  public function updateStatus(int $id, string $status): int {
    return $this->db->modify(
      "UPDATE users SET activity_status = :status WHERE id = :id AND deleted_at IS NULL",
      ['status' => $status, 'id' => $id]
    );
  }

  /**
   * ثبت زمان آخرین بازدید
   */
  public function updateLastVisit(int $id): int {
    return $this->db->modify(
      "UPDATE users SET last_visit_time = :now WHERE id = :id",
      ['now' => getCurrentDateTime(), 'id' => $id]
    );
  }

  /**
   * تأیید کاربر توسط ادمین
   */
  public function approve(int $id, int $approvedBy): int {
    return $this->db->modify(
      "UPDATE users SET
        approved_by = :approved_by,
        approved_at = :approved_at
       WHERE id = :id AND deleted_at IS NULL",
      [
        'approved_by' => $approvedBy,
        'approved_at' => getCurrentDateTime(),
        'id'          => $id,
      ]
    );
  }


  // ══════════════════════════════════════════════════
  // DELETE — Soft Delete
  // ══════════════════════════════════════════════════

  /**
   * Soft Delete — فقط deleted_at رو پر می‌کنه
   */
  public function delete(int $id): int {
    return $this->db->modify(
      "UPDATE users SET deleted_at = :now WHERE id = :id AND deleted_at IS NULL",
      ['now' => getCurrentDateTime(), 'id' => $id]
    );
  }

  /**
   * بازگردانی کاربر حذف‌شده
   */
  public function restore(int $id): int {
    return $this->db->modify(
      "UPDATE users SET deleted_at = NULL WHERE id = :id",
      ['id' => $id]
    );
  }

  /**
   * حذف واقعی — فقط برای superadmin
   */
  public function forceDelete(int $id): int {
    return $this->db->modify(
      "DELETE FROM users WHERE id = :id",
      ['id' => $id]
    );
  }


  // ══════════════════════════════════════════════════
  // VALIDATION HELPERS
  // ══════════════════════════════════════════════════

  public function emailExists(string $email, int $excludeId = 0): bool {
    return $this->db->exists(
      "SELECT id FROM users
       WHERE email = :email AND deleted_at IS NULL AND id != :exclude",
      ['email' => $email, 'exclude' => $excludeId]
    );
  }

  public function usernameExists(string $username, int $excludeId = 0): bool {
    return $this->db->exists(
      "SELECT id FROM users
       WHERE username = :username AND deleted_at IS NULL AND id != :exclude",
      ['username' => $username, 'exclude' => $excludeId]
    );
  }

  public function phoneExists(string $phone, int $excludeId = 0): bool {
    return $this->db->exists(
      "SELECT id FROM users
       WHERE phone = :phone AND deleted_at IS NULL AND id != :exclude",
      ['phone' => $phone, 'exclude' => $excludeId]
    );
  }

}
