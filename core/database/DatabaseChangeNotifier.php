<?php

namespace Core\database;

use PDO;

class DatabaseChangeNotifier {
    private const IGNORED_TABLES = ['user_notifications', 'translations'];

    public static function record(PDO $pdo, string $table, string $operation, array $data = [], ?int $entityId = null): void {
        if (in_array($table, self::IGNORED_TABLES, true)) return;
        if (function_exists('session') && session()->get('suppress_database_notifications', false)) return;

        $labels = ['insert' => 'ایجاد', 'update' => 'ویرایش', 'delete' => 'حذف'];
        $label = $labels[$operation] ?? $operation;
        $actor = self::actorId($data);
        $fields = array_values(array_filter(array_keys($data), fn($field) => !in_array($field, ['password'], true)));
        $fieldText = $fields ? ' فیلدهای «' . implode('، ', array_slice($fields, 0, 6)) . '» تغییر کردند.' : '';

        $stmt = $pdo->prepare('INSERT INTO user_notifications (user_id,type,entity_type,entity_id,is_read,created_by,updated_by) VALUES (?,?,?,?,0,?,?)');
        $notificationId = null;
        $stmt->execute([$actor, 'database_' . $operation, $table, $entityId, $actor, $actor]);
        $notificationId = (int)$pdo->lastInsertId();
        $title = $label . ' اطلاعات در ' . $table;
        $message = 'یک عملیات ' . $label . ' به‌صورت سیستمی در جدول ' . $table . ' ثبت شد.' . $fieldText;
        $translation = $pdo->prepare('INSERT INTO translations (table_name,table_id,field,locale,value,version,created_by,updated_by) VALUES (?,?,?,?,?,1,?,?)');
        foreach (['fa'=>[$title,$message], 'en'=>['Data ' . $operation . ' in ' . $table, 'A system ' . $operation . ' operation was recorded in the ' . $table . ' table.']] as $locale=>[$translatedTitle,$translatedMessage]) {
            $translation->execute(['user_notifications',$notificationId,'title',$locale,$translatedTitle,$actor,$actor]);
            $translation->execute(['user_notifications',$notificationId,'message',$locale,$translatedMessage,$actor,$actor]);
        }
        return;
        /*$stmt->execute([
            $actor,
            'database_' . $operation,
            $label . ' اطلاعات در ' . $table,
            'یک عملیات ' . $label . ' به‌صورت سیستمی در جدول ' . $table . ' ثبت شد.' . $fieldText,
            $table,
            $entityId,
            $actor,
        ]);*/
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
