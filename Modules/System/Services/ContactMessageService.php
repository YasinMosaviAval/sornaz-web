<?php
namespace Modules\System\Services;

use Core\database\DB;

final class ContactMessageService
{
    public function submit(array $input, ?array $user = null, bool $requireContact = true): int
    {
        $text = static function(string $key) use ($input): string {
            if (isset($input[$key]) && !is_scalar($input[$key])) throw new \InvalidArgumentException('اطلاعات فرم معتبر نیست.');
            return trim((string)($input[$key] ?? ''));
        };
        $name = $user ? (string)$user['username'] : $text('name');
        $email = $user ? (string)($user['email'] ?? '') : $text('email');
        $subject = $text('subject');
        $message = $text('message');
        if ($message === '' || ($requireContact && !$user && ($name === '' || $email === ''))) throw new \InvalidArgumentException('نام، ایمیل و متن پیام را تکمیل کنید.');
        if ($email !== '' && !filter_var($email,FILTER_VALIDATE_EMAIL)) throw new \InvalidArgumentException('ایمیل معتبر وارد کنید.');
        if (mb_strlen($name)>200 || strlen($email)>254 || mb_strlen($subject)>200 || mb_strlen($message)>10000) throw new \InvalidArgumentException('متن یکی از فیلدها بیش از حد طولانی است.');
        return transaction(function() use ($user,$name,$email,$subject,$message) {
            $actor = $user ? (int)$user['user_id'] : null;
            $now = date('Y-m-d H:i:s');
            $id = (int)DB::table('user_messages')->insertGetId(['sender_id'=>$actor,'receiver_user_id'=>1,'type'=>'message','status'=>'published','is_read'=>0,'created_at'=>$now,'updated_at'=>$now,'created_by'=>$actor ?? 1,'updated_by'=>$actor ?? 1]);
            if (!$id) throw new \RuntimeException('Contact message was not saved');
            $body = "Name: $name\nEmail: $email\n\n$message";
            foreach (['fa','en'] as $locale) foreach (['title'=>$subject ?: ($locale==='en'?'Contact form':'فرم تماس'),'message'=>$body] as $field=>$value) {
                if (!DB::table('translations')->insert(['table_name'=>'user_messages','table_id'=>$id,'field'=>$field,'locale'=>$locale,'value'=>$value,'version'=>1,'created_at'=>$now,'updated_at'=>$now,'created_by'=>$actor ?? 1,'updated_by'=>$actor ?? 1])) throw new \RuntimeException('Contact content was not saved');
            }
            return $id;
        });
    }
}
