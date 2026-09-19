<?php
namespace Modules\Social\Services;

use Modules\Social\Repositories\SocialRepository;
use Modules\Analytics\Services\ChatService;
use RuntimeException;

class SocialInteractionService
{
    public const EMOJI = ['❤️','😂','😍','😮','😢','👏'];
    public function __construct(private SocialRepository $r, private SocialService $social, private ChatService $chat) {}

    private function actor(int $actor): void
    {
        if ($actor <= 0) throw new RuntimeException('برای ادامه وارد حساب شوید.',401);
    }
    private function body(string $body): string
    {
        $body = trim($body);
        if ($body === '' || mb_strlen($body) > 2000) throw new RuntimeException('متن باید بین ۱ تا ۲۰۰۰ کاراکتر باشد.',422);
        return $body;
    }
    public function comments(int $actor, int $post, int $before = 0): array
    {
        $p = $this->social->post($actor,$post);
        if ($p['kind'] !== 'post') throw new RuntimeException('برای استوری از پاسخ خصوصی استفاده کنید.',422);
        $params = [$post]; $where = '';
        if ($before > 0) { $where = ' AND id<?'; $params[] = $before; }
        $rows = $this->r->query('SELECT * FROM social_comments WHERE post_id=? AND deleted_at IS NULL'.$where.' ORDER BY id DESC LIMIT 30',$params);
        return array_map(function ($c) use ($actor,$p) {
            $c['author'] = $this->social->profile($actor,(int)$c['user_id']);
            $c['canDelete'] = $actor > 0 && ($actor === (int)$c['user_id'] || $actor === (int)$p['owner_id']);
            return $c;
        },$rows);
    }
    public function comment(int $actor,int $post,string $body): array
    {
        $this->actor($actor); $body = $this->body($body);
        $p = $this->social->post($actor,$post);
        if ($p['kind'] !== 'post') throw new RuntimeException('برای استوری از پاسخ خصوصی استفاده کنید.',422);
        return $this->r->transaction(function () use ($actor,$post,$body,$p) {
            $id = $this->r->insert('social_comments',['post_id'=>$post,'user_id'=>$actor,'body'=>$body]);
            $this->social->notify((int)$p['owner_id'],$actor,'comment',$post,'برای پست شما نظر نوشت.');
            return ['id'=>$id,'post_id'=>$post,'body'=>$body,'created_at'=>gmdate('Y-m-d H:i:s'),
                'author'=>$this->social->profile($actor,$actor),'canDelete'=>true];
        });
    }
    public function deleteComment(int $actor,int $post,int $id): array
    {
        $this->actor($actor); $p = $this->social->post($actor,$post);
        $c = $this->r->one('SELECT * FROM social_comments WHERE id=? AND post_id=? AND deleted_at IS NULL',[$id,$post]);
        if (!$c) throw new RuntimeException('نظر پیدا نشد.',404);
        if ((int)$c['user_id'] !== $actor && (int)$p['owner_id'] !== $actor) throw new RuntimeException('اجازه حذف این نظر را ندارید.',403);
        $this->r->query('UPDATE social_comments SET deleted_at=UTC_TIMESTAMP() WHERE id=? AND post_id=?',[$id,$post]);
        return [];
    }
    public function share(int $actor,int $post,array $recipients): array
    {
        $this->actor($actor); $p = $this->social->post($actor,$post);
        if (count($recipients)>10) throw new RuntimeException('حداکثر ده عضو را انتخاب کنید.',422);
        foreach ($recipients as $id) {
            if ((!is_int($id) && !is_string($id)) || !preg_match('/^[1-9][0-9]*$/D',(string)$id)) throw new RuntimeException('شناسه عضو معتبر نیست.',422);
        }
        $ids = array_values(array_unique(array_map('intval',$recipients)));
        if (!$ids || count($ids)>10 || in_array(0,$ids,true) || in_array($actor,$ids,true)) throw new RuntimeException('یک تا ده عضو دیگر را انتخاب کنید.',422);
        foreach ($ids as $id) $this->social->user($id);
        $sent = [];
        foreach ($ids as $id) {
            $conversation = $this->chat->create($actor,['userIds'=>[$id]]);
            $cid = (int)$conversation['id'];
            $path = $p['kind'] === 'story' ? 'stories' : 'posts';
            $this->chat->send($actor,$cid,'/community/'.$path.'/'.$post);
            $this->social->notify($id,$actor,'message',$cid,'محتوایی را با شما به اشتراک گذاشت.');
            $sent[] = $id;
        }
        return ['sent'=>$sent];
    }
    public function replyStory(int $actor,int $post,string $body,string $emoji = ''): array
    {
        $this->actor($actor); $p = $this->social->post($actor,$post);
        if ($p['kind'] !== 'story') throw new RuntimeException('استوری پیدا نشد.',404);
        if ($emoji !== '' && !in_array($emoji,self::EMOJI,true)) throw new RuntimeException('واکنش معتبر نیست.',422);
        $body = $this->body(trim($emoji.' '.$body));
        $owner = (int)$p['owner_id'];
        if ($owner === $actor) throw new RuntimeException('نمی‌توانید به استوری خود پاسخ خصوصی بدهید.',422);
        $conversation = $this->chat->create($actor,['userIds'=>[$owner]]);
        $id = (int)$conversation['id'];
        $this->chat->send($actor,$id,$body."\n/community/stories/".$post);
        $this->social->notify($owner,$actor,'message',$id,'به استوری شما پاسخ داد.');
        return ['conversation_id'=>$id];
    }
}
