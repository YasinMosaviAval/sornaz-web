<?php

namespace Core\database;

use PDO;

class DatabaseChangeNotifier {
    private const IGNORED_TABLES = ['user_notifications'];

    public static function record(PDO $pdo, string $table, string $operation, array $data = [], ?int $entityId = null): void {
        if (in_array($table, self::IGNORED_TABLES, true)) return;

        $labels = ['insert' => 'ایجاد', 'update' => 'ویرایش', 'delete' => 'حذف'];
        $label = $labels[$operation] ?? $operation;
        $actor = self::actorId($data);
        $fields = array_values(array_filter(array_keys($data), fn($field) => !in_array($field, ['password'], true)));
        $fieldText = $fields ? ' فیلدهای «' . implode('، ', array_slice($fields, 0, 6)) . '» تغییر کردند.' : '';

        $stmt = $pdo->prepare('INSERT INTO user_notifications (user_id,type,title,message,entity_type,entity_id,is_read,created_by) VALUES (?,?,?,?,?,?,0,?)');
        $stmt->execute([
            $actor,
            'database_' . $operation,
            $label . ' اطلاعات در ' . $table,
            'یک عملیات ' . $label . ' به‌صورت سیستمی در جدول ' . $table . ' ثبت شد.' . $fieldText,
            $table,
            $entityId,
            $actor,
        ]);
    }

    private static function actorId(array $data): ?int {
        foreach (['updated_by', 'created_by', 'deleted_by', 'user_id'] as $field) {
            if (!empty($data[$field])) return (int)$data[$field];
        }
        try {
            $id = auth()->id();
            return $id ? (int)$id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
