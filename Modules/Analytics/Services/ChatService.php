<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

final class ChatService
{
    private function messageRows(): \Core\database\Builder
    {
        return DB::table('conversation_messages')->select('conversation_messages.*', \Core\translation\EntityText::expression('conversation_messages','conversation_message_id','body').' AS body');
    }

    private function insertMessage(array $data): int
    {
        $text=$data['body']??'';unset($data['body']);$pdo=db();$own=!$pdo->inTransaction();
        if($own){$pdo->beginTransaction();}
        try{
            $id=(int)DB::table('conversation_messages')->insertGetId($data);
            \Core\translation\EntityText::save('conversation_messages',$id,'body',$text,(int)$data['sender_id'],$pdo);
            if($own){$pdo->commit();}return $id;
        }catch(\Throwable $error){if($own&&$pdo->inTransaction()){$pdo->rollBack();}throw $error;}
    }

    public function index(int $actor): array
    {
        $this->user($actor);
        $members = DB::table('conversation_members')->where('user_id', $actor)->whereNull('left_at')->whereNull('deleted_at')->get();
        $ids = array_map(fn ($r) => (int) $r['conversation_id'], $members);
        $items = [];
        foreach ($ids ? DB::table('conversations')->whereIn('conversation_id', $ids)->whereNull('deleted_at')->orderBy('updated_at', 'DESC')->get() : [] as $c) {
            $cid = (int) $c['conversation_id'];
            $cm = current(array_filter($members, fn ($r) => (int) $r['conversation_id'] === $cid));
            $peerRows = DB::table('conversation_members')->where('conversation_id', $cid)->whereNull('left_at')->whereNull('deleted_at')->get();
            $peerIds = array_map(fn ($r) => (int) $r['user_id'], $peerRows);
            $names = $this->names($peerIds);
            $otherIds = array_values(array_filter($peerIds, fn ($uid) => $uid !== $actor));
            $title = $c['title'] ?: implode('، ', array_values(array_filter($names, fn ($v, $k) => $k !== $actor, ARRAY_FILTER_USE_BOTH)));
            $last = $c['last_message_id'] ? $this->messageRows()->where('conversation_message_id', (int) $c['last_message_id'])->whereNull('deleted_at')->first() : null;
            $unread = $this->messageRows()->where('conversation_id', $cid)->where('sender_id', '!=', $actor)->whereNull('deleted_at')->where('conversation_message_id', '>', (int) ($cm['last_read_message_id'] ?? 0))->count();
            $image = $c['type'] === 'group' ? (!empty($c['avatar_path']) ? '/' . ltrim((string) $c['avatar_path'], '/') : null) : ($otherIds ? $this->avatar((int) $otherIds[0]) : null);
            $items[] = ['id' => $cid, 'type' => $c['type'], 'title' => $title ?: 'گفتگو', 'image' => $image, 'members' => count($peerIds), 'lastMessage' => $last ? ($last['body'] ?: $last['attachment_name'] ?: 'فایل پیوست') : 'هنوز پیامی نیست', 'lastAt' => $last['created_at'] ?? $c['created_at'], 'unread' => (int) $unread];
        }return ['conversations' => $items, 'users' => $this->availableUsers($actor)];
    }

    public function searchUsers(int $actor, string $term, int $conversationId = 0): array
    {
        $this->user($actor);
        $term = trim($term);
        if (mb_strlen($term) > 80) {
            throw new RuntimeException('عبارت جستجو بیش از حد طولانی است.');
        }
        if ($conversationId > 0) {
            $this->member($actor, $conversationId);
        }
        $params = [$actor];
        $where = "u.user_id<>? AND u.register_method IN ('email','phone') AND u.deleted_at IS NULL";
        if ($conversationId > 0) {
            $where .= ' AND NOT EXISTS (SELECT 1 FROM conversation_members cm WHERE cm.conversation_id=? AND cm.user_id=u.user_id AND cm.left_at IS NULL AND cm.deleted_at IS NULL)';
            $params[] = $conversationId;
        }
        if ($term !== '') {
            $where .= " AND (u.username LIKE ? OR EXISTS (SELECT 1 FROM translations t WHERE t.table_name='users' AND t.table_id=u.user_id AND t.field='full_name' AND t.deleted_at IS NULL AND t.value LIKE ?))";
            $like = '%' . $term . '%';
            array_push($params, $like, $like);
        }
        $statement = db()->prepare("SELECT u.user_id,u.username,u.avatar_file_id FROM users u WHERE $where ORDER BY u.username LIMIT 30");
        $statement->execute($params);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $names = $this->names(array_map(fn ($row) => (int) $row['user_id'], $rows));
        return array_map(fn ($row) => ['id' => (int) $row['user_id'], 'name' => $names[(int) $row['user_id']] ?? $row['username'], 'username' => $row['username'], 'avatar' => $this->avatar((int) $row['user_id'])], $rows);
    }

    public function create(int $actor, array $d): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($d['userIds'] ?? [])))));
        $ids = array_values(array_diff($ids, [$actor]));
        if (!$ids) {
            throw new RuntimeException('حداقل یک عضو دیگر انتخاب کنید.');
        }
        foreach ($ids as $id) {
            $this->user($id);
        }
        $all = array_merge([$actor], $ids);
        sort($all);
        $type = count($all) === 2 ? 'direct' : 'group';
        if ($type === 'direct') {
            foreach (DB::table('conversation_members')->where('user_id', $actor)->whereNull('left_at')->whereNull('deleted_at')->get() as $m) {
                $c = DB::table('conversations')->where('conversation_id', (int) $m['conversation_id'])->where('type', 'direct')->whereNull('deleted_at')->first();
                if (!$c) {
                    continue;
                }
                $peers = DB::table('conversation_members')->where('conversation_id', (int) $m['conversation_id'])->whereNull('left_at')->whereNull('deleted_at')->get();
                $found = array_map(fn ($x) => (int) $x['user_id'], $peers);
                sort($found);
                if ($found === $all) {
                    return ['id' => (int) $m['conversation_id']];
                }
            }
        }$now = date('Y-m-d H:i:s');
        $title = trim((string) ($d['title'] ?? ''));
        if ($type === 'group' && $title === '') {
            throw new RuntimeException('نام گروه الزامی است.');
        }
        return transaction(function () use ($actor, $all, $type, $title, $now) {
            $id = (int) DB::table('conversations')->insertGetId(['type' => $type, 'title' => $type === 'group' ? $title : null, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            foreach ($all as $uid) {
                DB::table('conversation_members')->insert(['conversation_id' => $id, 'user_id' => $uid, 'role' => $uid === $actor ? 'admin' : 'member', 'is_muted' => 0, 'joined_at' => $now, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            }
            return ['id' => $id];
        });
    }

    public function messages(int $actor, int $id, int $after = 0, bool $markRead = true, array $refresh = []): array
    {
        $member = $this->member($actor, $id);
        [$rows, $hasMore] = $this->messagePage($id, $after);
        $refresh = array_slice(array_values(array_unique(array_filter(array_map('intval', $refresh), fn ($v) => $v > 0))), 0, 200);
        $updated = $this->messagesToRefresh($id, $refresh);
        $allRows = array_merge($rows, $updated);
        $names = $this->names(array_map(fn ($row) => (int) $row['sender_id'], $allRows));
        $last = $rows ? (int) end($rows)['conversation_message_id'] : $after;
        if ($rows && $markRead) {
            $this->advanceReadCursor($actor, (int) $member['conversation_member_id'], $last);
        }
        $messageIds = array_values(array_unique(array_map(fn ($r) => (int) $r['conversation_message_id'], $allRows)));
        $reactions = $this->messageReactions($actor, $messageIds);
        $serialize = fn ($row) => $this->serializeMessage($actor, $row, $names, $reactions);

        return [
            'messages' => array_map($serialize, $rows),
            'updated' => array_map($serialize, $updated),
            'deletedIds' => array_values(array_diff($refresh, array_map(fn ($r) => (int) $r['conversation_message_id'], $updated))),
            'hasMore' => $hasMore,
            'lastId' => $last,
        ];
    }

    private function messagePage(int $id, int $after): array
    {
        $limit = $after ? 100 : 200;
        $query = $this->messageRows()->where('conversation_id', $id)->whereNull('deleted_at');
        if ($after) {
            $query->where('conversation_message_id', '>', $after);
        }
        $rows = $query->orderBy('conversation_message_id')->limit($limit + 1)->get();
        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows);
        }
        return [$rows, $hasMore];
    }

    private function messagesToRefresh(int $id, array $refresh): array
    {
        if (!$refresh) {
            return [];
        }
        return $this->messageRows()
            ->where('conversation_id', $id)
            ->whereIn('conversation_message_id', $refresh)
            ->whereNull('deleted_at')
            ->get();
    }

    private function advanceReadCursor(int $actor, int $membershipId, int $last): void
    {
        db()->prepare('UPDATE conversation_members SET last_read_message_id=?, updated_at=?, updated_by=? WHERE conversation_member_id=? AND (last_read_message_id IS NULL OR last_read_message_id < ?)')
            ->execute([$last, date('Y-m-d H:i:s'), $actor, $membershipId, $last]);
    }

    private function messageReactions(int $actor, array $messageIds): array
    {
        $reactionStats = [];
        if ($messageIds) {
            $q = db()->prepare('SELECT conversation_message_id, COUNT(*) AS likes, MAX(CASE WHEN user_id=? THEN 1 ELSE 0 END) AS liked FROM conversation_message_reactions WHERE reaction=? AND conversation_message_id IN (' . implode(',', array_fill(0, count($messageIds), '?')) . ') GROUP BY conversation_message_id');
            $q->execute(array_merge([$actor, 'like'], $messageIds));
            foreach ($q->fetchAll(\PDO::FETCH_ASSOC) as $reaction) {
                $reactionStats[(int) $reaction['conversation_message_id']] = $reaction;
            }
        }
        return $reactionStats;
    }

    private function serializeMessage(int $actor, array $row, array $names, array $reactions): array
    {
        $id = (int) $row['conversation_message_id'];
        $reference = $this->reference($actor, (string) ($row['body'] ?? ''));
        return [
            'system' => ($row['message_kind'] ?? 'text') === 'system',
            'reply' => $this->replyPreview($actor, $row),
            'reference' => $reference,
            'id' => $id,
            'senderId' => (int) $row['sender_id'],
            'sender' => $names[(int) $row['sender_id']] ?? 'کاربر',
            'body' => $reference ? $this->referenceBody((string) $row['body']) : ($row['body'] ?? ''),
            'file' => $row['attachment_path'] ? [
                'name' => $row['attachment_name'],
                'size' => (int) $row['attachment_size'],
                'mime' => $this->messageMime($row),
                'url' => '/analytics/chat/messages/' . $id . '/file',
            ] : null,
            'createdAt' => $this->displayDate((string) $row['created_at'], $actor),
            'edited' => !empty($row['edited_at']),
            'mine' => (int) $row['sender_id'] === $actor,
            'liked' => (bool) ($reactions[$id]['liked'] ?? false),
            'likes' => (int) ($reactions[$id]['likes'] ?? 0),
        ];
    }

    public function send(int $actor, int $id, string $body, array $file = [], int $replyTo = 0): array
    {
        $this->member($actor, $id);
        $body = trim($body);
        if ($replyTo) {
            $parent = $this->message($actor, $replyTo);
            if ((int) $parent['conversation_id'] !== $id || ($parent['message_kind'] ?? '') === 'system') {
                throw new RuntimeException('پیام مورد پاسخ معتبر نیست.', 422);
            }
        }$attachment = $this->storeFile($actor, $file);
        if ($body === '' && !$attachment) {
            throw new RuntimeException('متن پیام یا فایل پیوست الزامی است.');
        }
        if (mb_strlen($body) > 10000) {
            throw new RuntimeException('متن پیام بیش از حد طولانی است.');
        }
        $now = date('Y-m-d H:i:s');
        $mid = (int) $this->insertMessage(['conversation_id' => $id, 'sender_id' => $actor, 'body' => $body ?: null, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor] + ($attachment ?: []) + ($replyTo ? ['reply_to_id' => $replyTo] : []));
        DB::table('conversations')->where('conversation_id', $id)->update(['last_message_id' => $mid, 'updated_at' => $now, 'updated_by' => $actor]);
        DB::table('conversation_members')->where('conversation_id', $id)->where('user_id', $actor)->update(['last_read_message_id' => $mid, 'updated_at' => $now, 'updated_by' => $actor]);
        return ['id' => $mid];
    }

    public function details(int $actor, int $id): array
    {
        $me = $this->member($actor, $id);
        $c = $this->conversation($id);
        $rows = DB::table('conversation_members')->where('conversation_id', $id)->whereNull('left_at')->whereNull('deleted_at')->orderBy('conversation_member_id')->get();
        $names = $this->names(array_map(fn ($r) => (int) $r['user_id'], $rows));
        $adminLabel = locale() === 'en' ? 'Admin' : 'مدیر';
        return ['id' => $id, 'type' => $c['type'], 'title' => $c['title'] ?? '', 'image' => !empty($c['avatar_path']) ? '/' . ltrim((string) $c['avatar_path'], '/') : null, 'canManage' => $c['type'] === 'group' && $me['role'] === 'admin', 'canLeave' => $c['type'] === 'group' && $me['role'] !== 'admin', 'canDelete' => $c['type'] !== 'group' || $me['role'] === 'admin', 'members' => array_map(fn ($r) => ['id' => (int) $r['user_id'], 'name' => $names[(int) $r['user_id']] ?? ('کاربر ' . $r['user_id']), 'avatar' => $this->avatar((int) $r['user_id']), 'role' => $r['role'], 'roleLabel' => $c['type'] === 'group' ? ($r['role'] === 'admin' ? $adminLabel : (locale() === 'en' ? 'Member' : 'عضو')) : null, 'isMe' => (int) $r['user_id'] === $actor], $rows), 'availableUsers' => $this->availableUsersForConversation($actor, $id)];
    }

    public function rename(int $actor, int $id, string $title): void
    {
        $this->requireGroupAdmin($actor, $id);
        $title = trim($title);
        if ($title === '') {
            throw new RuntimeException('نام گروه الزامی است.');
        }
        if (mb_strlen($title) > 255) {
            throw new RuntimeException('نام گروه بیش از حد طولانی است.');
        }
        $old = $this->conversation($id);
        if ((string) $old['title'] === $title) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $name = $this->names([$actor])[$actor] ?? ('کاربر ' . $actor);
        transaction(function () use ($actor, $id, $title, $old, $now, $name) {
            DB::table('conversations')->where('conversation_id', $id)->update(['title' => $title, 'updated_at' => $now, 'updated_by' => $actor]);
            $this->systemMessage($actor, $id, $name . ' نام گروه را از «' . $old['title'] . '» به «' . $title . '» تغییر داد.', $now);
        });
    }

    public function updateGroupAvatar(int $actor, int $id, array $file): array
    {
        $this->requireGroupAdmin($actor, $id);
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) < 1) {
            throw new RuntimeException('انتخاب تصویر الزامی است.');
        }
        if ((int) $file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('حداکثر حجم تصویر گروه ۵ مگابایت است.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!isset($extensions[$mime])) {
            throw new RuntimeException('فرمت تصویر گروه مجاز نیست.');
        }
        $dir = 'assets/media/chat-groups/' . $id;
        $abs = base_path($dir);
        if (!is_dir($abs) && !mkdir($abs, 0775, true) && !is_dir($abs)) {
            throw new RuntimeException('ایجاد پوشه تصویر ناموفق بود.');
        }
        $path = $dir . '/avatar-' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], base_path($path))) {
            throw new RuntimeException('ذخیره تصویر ناموفق بود.');
        }
        $conversation = $this->conversation($id);
        $old = (string) ($conversation['avatar_path'] ?? '');
        DB::table('conversations')->where('conversation_id', $id)->update(['avatar_path' => $path, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor]);
        if ($old !== '' && $old !== $path && is_file(base_path($old))) {
            @unlink(base_path($old));
        }
        return ['image' => '/' . ltrim($path, '/')];
    }

    public function addMembers(int $actor, int $id, array $userIds): void
    {
        $this->requireGroupAdmin($actor, $id);
        $now = date('Y-m-d H:i:s');
        foreach (array_unique(array_filter(array_map('intval', $userIds))) as $uid) {
            $this->user($uid);
            $old = DB::table('conversation_members')->where('conversation_id', $id)->where('user_id', $uid)->first();
            $alreadyActive = $old && empty($old['left_at']) && empty($old['deleted_at']);
            if ($alreadyActive) {
                continue;
            }
            if ($old) {
                DB::table('conversation_members')->where('conversation_member_id', (int) $old['conversation_member_id'])->update(['role' => 'member', 'left_at' => null, 'deleted_at' => null, 'deleted_by' => null, 'joined_at' => $now, 'updated_at' => $now, 'updated_by' => $actor]);
            } else {
                DB::table('conversation_members')->insert(['conversation_id' => $id, 'user_id' => $uid, 'role' => 'member', 'is_muted' => 0, 'joined_at' => $now, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            }
            if (!$alreadyActive) {
                $names = $this->names([$uid, $actor]);
                $this->systemMessage($actor, $id, ($names[$uid] ?? ('کاربر ' . $uid)) . ' توسط ' . ($names[$actor] ?? ('کاربر ' . $actor)) . ' به گروه اضافه شد.', $now);
            }
        }DB::table('conversations')->where('conversation_id', $id)->update(['updated_at' => $now, 'updated_by' => $actor]);
    }

    public function removeMember(int $actor, int $id, int $userId): void
    {
        $this->requireGroupAdmin($actor, $id);
        $target = $this->member($userId, $id);
        if ($target['role'] === 'admin') {
            throw new RuntimeException('سازنده و مدیر گروه قابل حذف نیست.');
        }
        $names = $this->names([$userId, $actor]);
        $now = date('Y-m-d H:i:s');
        DB::table('conversation_members')->where('conversation_member_id', (int) $target['conversation_member_id'])->update(['left_at' => $now, 'deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
        $this->systemMessage($actor, $id, ($names[$userId] ?? ('کاربر ' . $userId)) . ' توسط ' . ($names[$actor] ?? ('کاربر ' . $actor)) . ' از گروه حذف شد.', $now);
    }

    public function leaveGroup(int $actor, int $id): void
    {
        $member = $this->member($actor, $id);
        $conversation = $this->conversation($id);
        if ($conversation['type'] !== 'group') {
            throw new RuntimeException('ترک گفتگوی خصوصی امکان‌پذیر نیست.');
        }
        if ($member['role'] === 'admin') {
            throw new RuntimeException('مدیر گروه نمی‌تواند گروه را ترک کند؛ مدیر می‌تواند گروه را برای همه حذف کند.');
        }
        $name = $this->names([$actor])[$actor] ?? ('کاربر ' . $actor);
        $now = date('Y-m-d H:i:s');
        transaction(function () use ($actor, $id, $member, $name, $now) {
            $this->systemMessage($actor, $id, $name . ' گروه را ترک کرد.', $now);
            DB::table('conversation_members')->where('conversation_member_id', (int) $member['conversation_member_id'])->whereNull('deleted_at')->update(['left_at' => $now, 'deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
        });
    }

    public function deleteConversation(int $actor, int $id): void
    {
        $me = $this->member($actor, $id);
        $c = $this->conversation($id);
        if ($c['type'] === 'group' && $me['role'] !== 'admin') {
            throw new RuntimeException('فقط مدیر گروه اجازه حذف گفتگو را دارد.');
        }
        $now = date('Y-m-d H:i:s');
        transaction(function () use ($actor, $id, $now) {
            DB::table('conversations')->where('conversation_id', $id)->whereNull('deleted_at')->update(['deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            DB::table('conversation_members')->where('conversation_id', $id)->whereNull('deleted_at')->update(['left_at' => $now, 'deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            $this->messageRows()->where('conversation_id', $id)->whereNull('deleted_at')->update(['deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
        });
    }

    public function toggleLike(int $actor, int $messageId): array
    {
        $m = $this->message($actor, $messageId);
        $row = DB::table('conversation_message_reactions')->where('conversation_message_id', $messageId)->where('user_id', $actor)->where('reaction', 'like')->first();
        if ($row) {
            DB::table('conversation_message_reactions')->where('conversation_message_reaction_id', (int) $row['conversation_message_reaction_id'])->delete();
        } else {
            DB::table('conversation_message_reactions')->insert(['conversation_message_id' => $messageId, 'user_id' => $actor, 'reaction' => 'like', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $actor]);
        }
        return ['liked' => !$row, 'likes' => DB::table('conversation_message_reactions')->where('conversation_message_id', $messageId)->where('reaction', 'like')->count()];
    }

    public function editMessage(int $actor, int $messageId, string $body): void
    {
        \Core\translation\EntityText::atomic(db(),fn()=>$this->persistMessageEdit($actor,$messageId,$body));
    }

    private function persistMessageEdit(int $actor, int $messageId, string $body): void
    {
        $m = $this->message($actor, $messageId);
        if ((int) $m['sender_id'] !== $actor) {
            throw new RuntimeException('فقط فرستنده می‌تواند پیام را ویرایش کند.');
        }
        if (str_starts_with($this->messageMime($m), 'audio/')) {
            throw new RuntimeException('پیام صوتی قابل ویرایش نیست.', 422);
        }
        $body = trim($body);
        if ($body === '' && !$m['attachment_path']) {
            throw new RuntimeException('متن پیام نمی‌تواند خالی باشد.');
        }
        if (mb_strlen($body) > 10000) {
            throw new RuntimeException('متن پیام بیش از حد طولانی است.');
        }
        if (preg_match('~/community/(?:stories|posts)/[0-9]+~', (string) $m['body'], $ref) && !str_contains($body, $ref[0])) {
            $body .= "\n" . $ref[0];
        }
        $now = date('Y-m-d H:i:s');
        $this->messageRows()->where('conversation_message_id', $messageId)->update(['edited_at' => $now, 'updated_at' => $now, 'updated_by' => $actor]);
        \Core\translation\EntityText::save('conversation_messages',$messageId,'body',$body,$actor);
    }

    public function deleteMessage(int $actor, int $messageId): void
    {
        $m = $this->message($actor, $messageId);
        if ((int) $m['sender_id'] !== $actor) {
            throw new RuntimeException('فقط فرستنده می‌تواند پیام را حذف کند.');
        }
        $now = date('Y-m-d H:i:s');
        $this->messageRows()->where('conversation_message_id', $messageId)->update(['deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
    }

    public function forwardMessage(int $actor, int $messageId, array $conversationIds): void
    {
        $m = $this->message($actor, $messageId);
        foreach (array_unique(array_filter(array_map('intval', $conversationIds))) as $cid) {
            $this->member($actor, $cid);
            $now = date('Y-m-d H:i:s');
            $id = (int) $this->insertMessage(['conversation_id' => $cid, 'sender_id' => $actor, 'body' => $m['body'], 'attachment_path' => $m['attachment_path'], 'attachment_name' => $m['attachment_name'], 'attachment_mime' => $m['attachment_mime'], 'attachment_size' => $m['attachment_size'], 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            DB::table('conversations')->where('conversation_id', $cid)->update(['last_message_id' => $id, 'updated_at' => $now, 'updated_by' => $actor]);
        }
    }

    public function file(int $actor, int $messageId): array
    {
        $row = $this->messageRows()->where('conversation_message_id', $messageId)->whereNull('deleted_at')->first();
        if (!$row || !$row['attachment_path']) {
            throw new RuntimeException('فایل یافت نشد.');
        }
        $this->member($actor, (int) $row['conversation_id']);
        return ['path' => base_path($row['attachment_path']), 'name' => $row['attachment_name'], 'mime' => $row['attachment_mime'] ?: 'application/octet-stream'];
    }

    private function storeFile(int $actor, array $file): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed.', 422);
        }
        $info = ChatAttachmentPolicy::inspect((string) ($file['tmp_name'] ?? ''), (string) ($file['name'] ?? ''));
        $dir = 'storage/chat/' . $actor . '/' . date('Y/m');
        $absolute = base_path($dir);
        if (!is_dir($absolute) && !mkdir($absolute, 0775, true) && !is_dir($absolute)) {
            throw new RuntimeException('Cannot create upload directory.');
        }
        $path = $dir . '/' . bin2hex(random_bytes(16)) . '.' . $info['extension'];
        if (!move_uploaded_file((string) $file['tmp_name'], base_path($path))) {
            throw new RuntimeException('Cannot save upload.');
        }
        return ['attachment_path' => $path, 'attachment_name' => $info['name'], 'attachment_mime' => $info['mime'], 'attachment_size' => $info['size']];
    }

    private function systemMessage(int $actor, int $id, string $body, string $now): void
    {
        $mid = (int) $this->insertMessage(['conversation_id' => $id, 'sender_id' => $actor, 'body' => $body, 'message_kind' => 'system', 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
        DB::table('conversations')->where('conversation_id', $id)->update(['last_message_id' => $mid, 'updated_at' => $now, 'updated_by' => $actor]);
    }

    private function messageMime(array $row): string
    {
        $name = (string) ($row['attachment_name'] ?? '');
        if (preg_match('/^voice-.*\.(m4a|webm|ogg)$/i', $name, $m)) {
            return ['m4a' => 'audio/mp4', 'webm' => 'audio/webm', 'ogg' => 'audio/ogg'][strtolower($m[1])];
        }
        return (string) ($row['attachment_mime'] ?? 'application/octet-stream');
    }

    private function replyPreview(int $actor, array $row): ?array
    {
        if (empty($row['reply_to_id'])) {
            return null;
        }
        $parent = $this->messageRows()->where('conversation_message_id', (int) $row['reply_to_id'])->where('conversation_id', (int) $row['conversation_id'])->whereNull('deleted_at')->first();
        return $parent ? ['id' => (int) $parent['conversation_message_id'], 'body' => $parent['body'] ?: ($parent['attachment_name'] ?? ''), 'senderId' => (int) $parent['sender_id']] : ['body' => locale() === 'en' ? 'Message deleted' : 'پیام حذف شده است'];
    }

    private function referenceBody(string $body): string
    {
        $body = preg_replace('~/community/(?:stories|posts)/[0-9]+~u', '', $body);
        return trim(preg_replace('~^پاسخ به استوری:\s*~u', '', $body));
    }

    private function reference(int $actor, string $body): ?array
    {
        if (!preg_match('~/community/(stories|posts)/([0-9]+)~', $body, $m)) {
            return null;
        }
        $id = (int) $m[2];
        $kind = $m[1] === 'stories' ? 'story' : 'post';
        $p = DB::table('social_posts')->where('id', $id)->where('kind', $kind)->whereNull('deleted_at')->first();
        if (!$p) {
            return ['id' => $id, 'kind' => $kind, 'available' => false];
        }
        $author = DB::table('users')->where('user_id', (int) $p['owner_id'])->whereNull('deleted_at')->first();
        if (!$author || ((int) $p['owner_id'] !== $actor && ($author['visibility'] ?? '') === 'private')) {
            return ['id' => $id, 'kind' => $kind, 'available' => false];
        }
        $owner = (int) $p['owner_id'] === $actor;
        $expired = !empty($p['expires_at']) && strtotime($p['expires_at'] . ' UTC') <= time();
        $result = ['id' => $id, 'kind' => $kind, 'owner' => $owner, 'available' => $owner || !$expired, 'expiresAt' => empty($p['expires_at']) ? null : str_replace(' ', 'T', $p['expires_at']) . 'Z'];
        if (!$result['available']) {
            return $result;
        }
        $media = !empty($p['media_id']) ? DB::table('social_media')->where('id', (int) $p['media_id'])->first() : null;
        return $result + ['body' => \Core\translation\EntityText::get('social_posts',(int)$p['id'],'body'), 'media' => $media ? '/api/sornaz/v1/social/media/' . (int) $p['media_id'] : null, 'mime' => $media['mime'] ?? ''];
    }

    private function member(int $actor, int $id): array
    {
        $m = DB::table('conversation_members')->where('conversation_id', $id)->where('user_id', $actor)->whereNull('left_at')->whereNull('deleted_at')->first();
        if (!$m) {
            throw new RuntimeException('به این گفتگو دسترسی ندارید.');
        }
        return $m;
    }

    private function conversation(int $id): array
    {
        $c = DB::table('conversations')->where('conversation_id', $id)->whereNull('deleted_at')->first();
        if (!$c) {
            throw new RuntimeException('گفتگو یافت نشد.');
        }
        return $c;
    }

    private function message(int $actor, int $id): array
    {
        $m = $this->messageRows()->where('conversation_message_id', $id)->whereNull('deleted_at')->first();
        if (!$m) {
            throw new RuntimeException('پیام یافت نشد.');
        }
        $this->member($actor, (int) $m['conversation_id']);
        return $m;
    }

    private function requireGroupAdmin(int $actor, int $id): array
    {
        $m = $this->member($actor, $id);
        $c = $this->conversation($id);
        if ($c['type'] !== 'group' || $m['role'] !== 'admin') {
            throw new RuntimeException('فقط مدیر گروه اجازه این عملیات را دارد.');
        }
        return $m;
    }

    private function user(int $id): array
    {
        $u = DB::table('users')->where('user_id', $id)->whereNull('deleted_at')->first();
        if (!$u) {
            throw new RuntimeException('کاربر معتبر نیست.');
        }
        return $u;
    }

    private function displayDate(string $value, int $actor): string
    {
        static $zones = [];
        $source = (string) env('APP_STORAGE_TIMEZONE', date_default_timezone_get());
        if (!isset($zones[$actor])) {
            $user = $this->user($actor);
            $zones[$actor] = (string) ($user['timezone'] ?? env('APP_TIMEZONE', 'Asia/Tehran'));
        }try {
            return (new \DateTimeImmutable($value, new \DateTimeZone($source)))->setTimezone(new \DateTimeZone($zones[$actor]))->format('Y-m-d\TH:i:sP');
        } catch (\Throwable) {
            return $value;
        }
    }

    private function availableUsers(int $actor): array
    {
        $rows = DB::table('users')->where('user_id', '!=', $actor)->whereIn('register_method', ['email', 'phone'])->whereNull('deleted_at')->orderBy('username')->limit(30)->get();
        $names = $this->names(array_map(fn ($r) => (int) $r['user_id'], $rows));
        return array_map(fn ($r) => ['id' => (int) $r['user_id'], 'name' => $names[(int) $r['user_id']] ?? $r['username'], 'username' => $r['username'] ?? '', 'avatar' => $this->avatar((int) $r['user_id'])], $rows);
    }

    private function availableUsersForConversation(int $actor, int $id): array
    {
        $current = array_map(fn ($r) => (int) $r['user_id'], DB::table('conversation_members')->where('conversation_id', $id)->whereNull('left_at')->whereNull('deleted_at')->get());
        return array_values(array_filter($this->availableUsers($actor), fn ($u) => !in_array((int) $u['id'], $current, true)));
    }

    private function names(array $ids): array
    {
        $out = [];
        foreach (array_unique(array_filter($ids)) as $id) {
            $u = DB::table('users')->where('user_id', $id)->first();
            $t = DB::table('translations')->where('table_name', 'users')->where('table_id', $id)->where('field', 'full_name')->where('locale', locale())->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            $out[$id] = $t['value'] ?? $u['username'] ?? ('کاربر ' . $id);
        }return $out;
    }

    private function avatar(int $userId): ?string
    {
        $user = DB::table('users')->where('user_id', $userId)->first();
        if (!$user) {
            return null;
        }
        $file = !empty($user['avatar_file_id']) ? DB::table('media_files')->where('media_file_id', (int) $user['avatar_file_id'])->whereNull('deleted_at')->first() : null;
        if (!$file) {
            $file = DB::table('media_files')->where('user_id', $userId)->whereIn('collection', ['avatar', 'teacher_avatar', 'logo'])->whereNull('deleted_at')->orderBy('sort_order')->first();
        }
        return $file && !empty($file['path']) ? '/' . ltrim((string) $file['path'], '/') : null;
    }
}
