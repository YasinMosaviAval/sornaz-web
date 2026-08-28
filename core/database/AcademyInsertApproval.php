<?php

namespace Core\database;

use PDO;
use Throwable;

final class AcademyInsertApproval
{
    public const PERMISSION = 'academy_records.auto_approve';

    private static array $approvalColumns = [];
    private static array $allowed = [];

    public static function apply(PDO $pdo, string $table, array $data): array
    {
        if ($table !== 'academies' && !str_starts_with($table, 'academy_')) return $data;

        $actor = self::authenticatedActor();
        if (!$actor || !self::hasApprovalColumns($pdo, $table)) return $data;

        $approved = self::actorCanApprove($pdo, $actor);
        $data['approved_by'] = $approved ? $actor : null;
        $data['approved_at'] = $approved ? date('Y-m-d H:i:s') : null;
        return $data;
    }

    private static function authenticatedActor(): ?int
    {
        try {
            $actor = auth()->id();
            return $actor ? (int) $actor : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function hasApprovalColumns(PDO $pdo, string $table): bool
    {
        if (array_key_exists($table, self::$approvalColumns)) return self::$approvalColumns[$table];
        if (!preg_match('/^[a-z0-9_]+$/', $table)) return self::$approvalColumns[$table] = false;
        $statement = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME IN (\'approved_by\',\'approved_at\')');
        $statement->execute([$table]);
        return self::$approvalColumns[$table] = (int) $statement->fetchColumn() === 2;
    }

    private static function actorCanApprove(PDO $pdo, int $actor): bool
    {
        if ($actor === 1) return true;
        if (array_key_exists($actor, self::$allowed)) return self::$allowed[$actor];
        $statement = $pdo->prepare("SELECT 1 FROM access_system_permissions p WHERE p.name=? AND p.deleted_at IS NULL AND (EXISTS(SELECT 1 FROM user_permissions up WHERE up.permission_id=p.permission_id AND up.user_id=? AND up.deleted_at IS NULL AND (up.expires_at IS NULL OR up.expires_at>NOW())) OR EXISTS(SELECT 1 FROM user_roles ur JOIN access_system_role_permissions rp ON rp.role_id=ur.role_id AND rp.permission_id=p.permission_id AND rp.deleted_at IS NULL JOIN access_system_roles r ON r.role_id=ur.role_id AND r.deleted_at IS NULL WHERE ur.user_id=? AND ur.deleted_at IS NULL AND (ur.expires_at IS NULL OR ur.expires_at>NOW()))) LIMIT 1");
        $statement->execute([self::PERMISSION, $actor, $actor]);
        return self::$allowed[$actor] = (bool) $statement->fetchColumn();
    }
}
