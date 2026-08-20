<?php
namespace Modules\System\Services;
use Core\database\DB;
class UserNotificationService {
    public function send(int $userId, string $title, string $message, string $entityType, int $entityId, ?int $createdBy = null): void {
        if ($userId <= 0) return;
        DB::table('user_notifications')->insert(['user_id'=>$userId,'type'=>'registration','title'=>$title,'message'=>$message,'entity_type'=>$entityType,'entity_id'=>$entityId,'is_read'=>0,'created_by'=>$createdBy ?: $userId]);
    }
}
