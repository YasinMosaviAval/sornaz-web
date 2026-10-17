<?php

namespace Core\database;

use PDO;

class DatabaseChangeNotifier
{
    private const IGNORED_TABLES = ['user_messages', 'translations', 'f_financial_system_discount_organizations'];

    public static function record(PDO $pdo, string $table, string $operation, array $data = [], ?int $entityId = null): void
    {
        if (in_array($table, self::IGNORED_TABLES, true)) {
            return;
        }
        if (function_exists('session') && session()->get('suppress_database_notifications', false)) {
            return;
        }

        $pointActor = self::actorId($data);
        try {
            \Modules\Analytics\Services\UserPointService::recordDatabaseAction($pdo, $table, $operation, $data, $entityId, $pointActor);
        } catch (\Throwable) {
        }
        if ($operation === 'update') {
            return;
        }

        $actor = self::actorId($data);
        $stmt = $pdo->prepare("INSERT INTO user_messages (sender_id,receiver_user_id,type,status,related_entity_type,related_entity_id,is_read,created_by,updated_by) VALUES (?,?,'notification','published',?,?,0,?,?)");
        $notificationId = null;
        $stmt->execute([$actor, $actor, $table, $entityId, $actor, $actor]);
        $notificationId = (int) $pdo->lastInsertId();
        $translation = $pdo->prepare('INSERT INTO translations (table_name,table_id,field,locale,value,version,created_by,updated_by) VALUES (?,?,?,?,?,1,?,?)');
        foreach (['fa', 'en'] as $locale) {
            $description = ActivityText::describe($table, $operation, $data, $locale);
            $translatedTitle = $description['title'];
            $translatedMessage = $description['message'];
            $translation->execute(['user_messages', $notificationId, 'title', $locale, $translatedTitle, $actor, $actor]);
            $translation->execute(['user_messages', $notificationId, 'message', $locale, $translatedMessage, $actor, $actor]);
            foreach (['event_operation' => $operation, 'priority' => NotificationMetadata::priority($table, $operation)] as $field => $value) {
                $translation->execute(['user_messages', $notificationId, $field, $locale, $value, $actor, $actor]);
            }
        }
    }

    private static function actorId(array $data): ?int
    {
        foreach (['updated_by', 'created_by', 'deleted_by', 'user_id'] as $field) {
            if (!empty($data[$field])) {
                return (int) $data[$field];
            }
        }
        try {
            $id = auth()->id();
            return $id ? (int) $id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
