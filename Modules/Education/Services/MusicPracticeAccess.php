<?php
namespace Modules\Education\Services;

final class MusicPracticeAccess
{
    public function __construct(private MusicPracticeStore $store) {}
    public function permitted(int $actor, array $exam): bool
    {
        if(!empty($exam['deleted_at']))return false;
        if ($actor === 1 || ($actor > 0 && $actor === (int)$exam['owner_id'])) return true;
        if (!(int)$exam['published']) return false;
        if ($exam['audience'] === 'public') return true;
        if ($actor < 1) return false;
        if ($this->expires($actor,(int)$exam['owner_id'],(int)$exam['id']) !== null) return true;
        if ($exam['audience'] === 'users') return (bool)$this->store->one('SELECT user_id FROM f_music_practice_grants WHERE exam_id=? AND user_id=?', [$exam['id'], $actor]);
        if ($exam['audience'] === 'students') return $this->student($actor, (int)$exam['owner_id']);
        return $exam['audience'] === 'subscribers' && $this->expires($actor, (int)$exam['owner_id'], (int)$exam['id']) !== null;
    }
    public function expires(int $actor, int $teacher, int $exam): ?string
    {
        $nodes=$this->store->hasNodeScope('music_practice_orders')?($this->nodePath($exam,$teacher)?:[0]):[];
        $scope=$nodes?' AND (node_id IS NULL OR node_id IN ('.implode(',',array_fill(0,count($nodes),'?')).'))':'';
        $row = $this->store->one("SELECT MAX(COALESCE(expires_at,'2100-01-01 00:00:00')) expires_at FROM f_music_practice_orders WHERE buyer_id=? AND teacher_id=? AND (exam_id IS NULL OR exam_id=?) AND status='paid' AND starts_at<=? AND (expires_at IS NULL OR expires_at>?)$scope", [$actor,$teacher,$exam,$this->store->now(),$this->store->now(),...$nodes]);
        return $row['expires_at'] ?? null;
    }
    public function rootNode(int $exam,int $teacher): int
    {
        $path=$this->nodePath($exam,$teacher);return $path?(int)end($path):0;
    }
    public function nodePath(int $exam,int $teacher): array
    {
        $row=$this->store->one('SELECT n.id node_id,p.id parent_id,g.id grandparent_id FROM f_music_practice_exams e JOIN f_music_practice_nodes n ON n.id=e.node_id LEFT JOIN f_music_practice_nodes p ON p.id=n.parent_id LEFT JOIN f_music_practice_nodes g ON g.id=p.parent_id WHERE e.id=? AND e.owner_id=?',[$exam,$teacher]);
        return array_values(array_map('intval',array_filter($row?:[],static fn($id)=>$id!==null)));
    }
    public function permit(int $actor, array $exam): array
    {
        if ($actor < 1 || !$this->permitted($actor,$exam)) return [];
        $expiry=$exam['audience']==='public'?4102444800:time()+7*86400;
        if($actor!==1&&$actor!==(int)$exam['owner_id']&&$exam['audience']!=='public'){
            $membership=$exam['audience']==='users'&&(bool)$this->store->one('SELECT user_id FROM f_music_practice_grants WHERE exam_id=? AND user_id=?',[$exam['id'],$actor]);
            if($exam['audience']==='students')$membership=$this->student($actor,(int)$exam['owner_id']);
            if(!$membership)$expiry=strtotime($this->expires($actor,(int)$exam['owner_id'],(int)$exam['id']).' UTC');
        }
        $payload = $actor.':'.$exam['id'].':'.$exam['version'].':'.$expiry;
        return ['permitExpiresAt'=>$expiry,'downloadPermit'=>hash_hmac('sha256','music-practice:'.$payload,$this->key())];
    }
    public function validPermit(int $actor, array $input): bool
    {
        $expiry=$input['permitExpiresAt']??null;$token=$input['downloadPermit']??null;
        if(!is_int($expiry)||!is_string($token)||!preg_match('/^[a-f0-9]{64}$/D',$token))return false;
        $payload=$actor.':'.$input['examId'].':'.$input['version'].':'.$expiry;
        return hash_equals(hash_hmac('sha256','music-practice:'.$payload,$this->key()),$token);
    }
    private function key(): string
    {
        $dedicated=(string)env('MUSIC_PRACTICE_SIGNING_KEY','');
        $key=$dedicated!==''?$dedicated:(string)config('app.key','');
        if(strlen($key)<32)throw new \RuntimeException('کلید امن اجرای آفلاین روی سرور آماده نیست.',503);
        return $key;
    }
    public function signingReady(): bool
    {
        try{$this->key();return true;}catch(\RuntimeException $e){if($e->getCode()!==503)throw $e;return false;}
    }
    private function student(int $actor, int $teacher): bool
    {
        // Teacher ownership is established through the existing personal learning relationship.
        return (bool)$this->store->one("SELECT m.member_id FROM academy_branch_members m JOIN academy_branch_course_term_enrollments student ON student.member_id=m.member_id AND student.type='student' AND student.status='active' AND student.deleted_at IS NULL JOIN academy_branch_course_terms term ON term.term_id=student.term_id AND term.deleted_at IS NULL JOIN academy_branch_course_term_enrollments teaching ON teaching.term_id=student.term_id AND teaching.type='teacher' AND teaching.status='active' AND teaching.deleted_at IS NULL JOIN academy_branch_members teacher ON teacher.member_id=teaching.member_id AND teacher.deleted_at IS NULL WHERE m.user_id=? AND teacher.user_id=? AND m.deleted_at IS NULL LIMIT 1", [$actor,$teacher]);
    }
}
