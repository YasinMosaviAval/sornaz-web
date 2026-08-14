<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminNotificationService {
    public function all(): array {
        $rows = DB::table('user_notifications')->whereNull('deleted_at')->orderBy('user_notification_id', 'DESC')->limit(1000)->get();
        return array_map(fn(array $row) => $this->map($row), $rows);
    }

    public function create(int $actor, array $data): int {
        $title = trim((string)($data['title'] ?? ''));
        $message = trim((string)($data['body'] ?? ''));
        if ($title === '' || $message === '') throw new RuntimeException('عنوان و متن اعلان الزامی است.');
        return (int)DB::table('user_notifications')->insertGetId([
            'user_id' => $actor,
            'type' => !empty($data['asDraft']) ? 'manual_draft' : 'manual',
            'title' => $title,
            'message' => $message,
            'entity_type' => 'admin_notification',
            'is_read' => 0,
            'created_by' => $actor,
        ]);
    }

    public function setStatus(int $id, string $status, int $actor): void {
        $row = DB::table('user_notifications')->where('user_notification_id', $id)->whereNull('deleted_at')->first();
        if (!$row) throw new RuntimeException('اعلان یافت نشد.');
        if ($status === 'expired') {
            DB::table('user_notifications')->where('user_notification_id', $id)->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$actor]);
            return;
        }
        DB::table('user_notifications')->where('user_notification_id', $id)->update(['type'=>'manual','is_read'=>0,'updated_by'=>$actor]);
    }

    public function delete(int $id, int $actor): void {
        $this->setStatus($id, 'expired', $actor);
    }

    private function map(array $row): array {
        $operation = str_replace('database_', '', (string)$row['type']);
        $priority = $operation === 'delete' ? 'بالا' : ($operation === 'update' ? 'متوسط' : 'کم');
        return [
            'id'=>(int)$row['user_notification_id'], 'title'=>$row['title'], 'body'=>$row['message'],
            'branchId'=>0, 'branchName'=>'سیستم', 'audience'=>'مدیران', 'priority'=>$priority,
            'status'=>$row['type'] === 'manual_draft' ? 'پیش‌نویس' : 'منتشر شده',
            'date'=>date('Y/m/d', strtotime($row['created_at'])), 'dateISO'=>date('Y-m-d', strtotime($row['created_at'])),
            'source'=>'سیستم', 'type'=>$row['type'], 'entityType'=>$row['entity_type'], 'entityId'=>$row['entity_id'],
        ];
    }
}
