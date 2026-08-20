<?php
namespace Modules\System\Services;
use Core\database\DB;
class UserNotificationService {
    public function send(int $userId, string $title, string $message, string $entityType, int $entityId, ?int $createdBy = null, ?string $englishTitle = null, ?string $englishMessage = null): void {
        if ($userId <= 0) return;
        $actor = $createdBy ?: $userId;
        $id = (int)DB::table('user_notifications')->insertGetId(['user_id'=>$userId,'type'=>'registration','entity_type'=>$entityType,'entity_id'=>$entityId,'is_read'=>0,'created_by'=>$actor,'updated_by'=>$actor]);
        foreach (['fa'=>[$title,$message],'en'=>[$englishTitle ?: $this->englishTitle($title),$englishMessage ?: $this->englishMessage($message)]] as $locale => [$translatedTitle,$translatedMessage]) {
            foreach (['title'=>$translatedTitle,'message'=>$translatedMessage] as $field=>$value) DB::table('translations')->insert(['table_name'=>'user_notifications','table_id'=>$id,'field'=>$field,'locale'=>$locale,'value'=>$value,'version'=>1,'created_by'=>$actor,'updated_by'=>$actor]);
        }
    }
    private function englishTitle(string $title): string { return match ($title) {'ثبت‌نام موفق'=>'Registration successful','ثبت‌نام کاربر جدید'=>'New user registration','ایجاد اکانت مالی کاربر جدید'=>'New user financial account created','ایجاد کد دعوت کاربر جدید'=>'New user invitation code created',default=>$title}; }
    private function englishMessage(string $message): string { return $message; }
}
