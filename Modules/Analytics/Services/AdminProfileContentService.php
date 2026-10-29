<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

final class AdminProfileContentService
{
    private const CONFIG = [
        'awards' => ['table' => 'user_awards', 'key' => 'user_award_id', 'dates' => ['date'], 'texts' => ['title', 'issuer', 'description']],
        'badges' => ['table' => 'user_badges', 'key' => 'user_badge_id', 'dates' => ['granted_at', 'expires_at'], 'texts' => []],
        'experiences' => ['table' => 'user_experiences', 'key' => 'user_experience_id', 'dates' => ['start_date', 'end_date'], 'texts' => ['title', 'organization', 'description']],
        'certificates' => ['table' => 'user_certificates', 'key' => 'user_certificate_id', 'dates' => ['issue_date', 'expire_date'], 'texts' => ['title', 'issuer', 'description']],
        'educations' => ['table' => 'user_educations', 'key' => 'user_education_id', 'dates' => ['start_date', 'end_date'], 'texts' => ['degree', 'institution', 'field', 'description']],
        'events' => ['table' => 'user_events', 'key' => 'user_event_id', 'dates' => ['event_date'], 'texts' => ['title', 'description']],
        'polls' => ['table' => 'user_polls', 'key' => 'user_poll_id', 'dates' => ['expires_at'], 'texts' => ['question', 'description']],
        'publications' => ['table' => 'user_publications', 'key' => 'user_publication_id', 'dates' => ['published_date'], 'texts' => ['content']],
    ];

    public function index(int $actor, string $entity): array
    {
        $c = $this->config($entity);
        [$organizations,$branchAccount,$autoApprove,$canModerate] = $this->organizations($actor);
        $ids = array_column($organizations, 'id');
        $ownerColumn = $entity === 'polls' ? 'owner_id' : 'user_id';
        $rows = $ids ? DB::table($c['table'])->whereIn($ownerColumn, $ids)->whereNull('deleted_at')->orderBy($c['key'], 'DESC')->get() : [];
        $names = [];
        foreach ($organizations as $u) {
            $names[$u['id']] = $u['name'];
        }
        $organizationMap = [];
        foreach ($organizations as $o) {
            $organizationMap[(int) $o['id']] = $o;
        }
        $items = [];
        foreach ($rows as $row) {
            $ownerId = (int) $row[$ownerColumn];
            $organization = $organizationMap[$ownerId] ?? null;
            if (!$organization || (!$organization['writable'] && empty($row['approved_at']))) {
                continue;
            }
            $id = (int) $row[$c['key']];
            $row['id'] = $id;
            $row['user_name'] = $names[$ownerId] ?? ('سازمان ' . $ownerId);
            $row['organization'] = $organization;
            $row['canManage'] = (bool) $organization['writable'] && ($entity !== 'badges' || $this->badgeWriter($actor));
            $row['canChangeStatus'] = $canModerate && $row['canManage'];
            $row['approval_status'] = $row['approved_at'] ? 'approved' : 'pending';
            foreach ($c['texts'] as $f) {
                $row[$f] = $entity === 'publications' && $f === 'content' ? \Core\translation\EntityText::get('user_publications',$id,'content') : $this->text($c['table'], $id, $f);
            }
            if ($entity === 'polls') {
                $row['options'] = $this->pollOptions($id, $actor);
            }
            $items[] = $row;
        }
        $levels = $entity === 'badges' ? $this->badgeLevels($items) : [];
        $addresses = [];
        if (in_array($entity, ['experiences', 'events'], true) && $ids) {
            foreach (DB::table('user_addresses')->whereIn('user_id', $ids)->whereNull('deleted_at')->get() as $a) {
                $id = (int) $a['address_id'];
                $addresses[] = ['id' => $id, 'userId' => (int) $a['user_id'], 'title' => $this->text('user_addresses', $id, 'address') ?: ('نشانی ' . $id)];
            }
        }
        return ['entity' => $entity, 'canCreate' => $entity !== 'badges' || $this->badgeWriter($actor), 'organizations' => $organizations, 'hideOrganizationField' => $branchAccount, 'canModerate' => $canModerate, 'autoApprove' => $autoApprove, 'items' => $items, 'levels' => $levels, 'addresses' => $addresses, 'version' => $this->version($c['table'], $ids, $ownerColumn)];
    }

    public function save(int $actor, string $entity, ?int $id, array $d): int
    {
        $this->requireBadgeWriter($actor, $entity);
        return \Core\translation\EntityText::atomic(db(),fn()=>$this->persist($actor,$entity,$id,$d));
    }

    private function persist(int $actor, string $entity, ?int $id, array $d): int
    {
        $c = $this->config($entity);
        $userId = (int) ($d['user_id'] ?? $d['owner_id'] ?? 0);
        [,,$autoApprove] = $this->organizations($actor);
        $this->allowedOrganization($actor, $userId, true);
        $now = date('Y-m-d H:i:s');
        $required = ['awards' => 'title', 'experiences' => 'title', 'certificates' => 'title', 'educations' => 'degree', 'events' => 'title', 'polls' => 'question'];
        if (isset($required[$entity]) && trim((string) ($d[$required[$entity]] ?? '')) === '') {
            throw new RuntimeException('تکمیل فیلد اصلی الزامی است.');
        }
        $values = [$entity === 'polls' ? 'owner_id' : 'user_id' => $userId, 'updated_at' => $now, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null, 'approved_at' => $autoApprove ? $now : null, 'approved_by' => $autoApprove ? $actor : null];
        foreach ($c['dates'] as $f) {
            $values[$f] = $this->date($d[$f] ?? null, str_ends_with($f, '_at'));
        }
        if ($entity === 'badges') {
            $values += $this->badgeValues($actor, $d);
            $values['granted_at'] ??= $now;
        }
        if ($entity === 'certificates' && mb_strlen(trim((string)($d['file_path']??'')))>2048) {
            throw new RuntimeException('File path exceeds 2048 characters.',422);
        }
        if ($entity === 'certificates') {
            $values += ['certificate_url' => $this->url($d['certificate_url'] ?? ''), 'file_path' => trim((string) ($d['file_path'] ?? '')) ?: null];
        }
        if (in_array($entity, ['experiences', 'events'], true)) {
            $addressId = (int) ($d['address_id'] ?? 0);
            if ($addressId && !DB::table('user_addresses')->where('address_id', $addressId)->where('user_id', $userId)->whereNull('deleted_at')->first()) {
                throw new RuntimeException('نشانی انتخاب‌شده متعلق به این سازمان نیست.');
            }
            $values['address_id'] = $addressId ?: null;
        }
        if ($entity === 'events') {
            $values['event_type'] = $this->one($d['event_type'] ?? 'other', ['concert', 'festival', 'competition', 'workshop', 'other'], 'نوع رویداد');
        }
        if ($entity === 'polls') {
            $values += ['target_type' => trim((string) ($d['target_type'] ?? '')) ?: null, 'target_id' => (int) ($d['target_id'] ?? 0) ?: null, 'type' => $this->one($d['type'] ?? 'single', ['single', 'multiple'], 'نوع نظرسنجی'), 'is_anonymous' => !empty($d['is_anonymous']) ? 1 : 0, 'status' => $this->one($d['status'] ?? 'deactive', ['active', 'deactive', 'closed'], 'وضعیت'), 'votes_count' => 0];
        }
        if ($entity === 'publications') {
            $title = trim((string) ($d['title'] ?? ''));
            if ($title === '') {
                throw new RuntimeException('عنوان الزامی است.');
            }
            $values += ['title' => $title, 'publisher' => trim((string) ($d['publisher'] ?? '')) ?: null, 'url' => $this->url($d['url'] ?? ''), 'is_peer_reviewed' => !empty($d['is_peer_reviewed']) ? 1 : 0];
        }
        if ($id) {
            $this->owned($actor, $entity, $id);
            DB::table($c['table'])->where($c['key'], $id)->update($values);
        } else {
            $values += ['created_at' => $now, 'created_by' => $actor];
            $id = (int) DB::table($c['table'])->insertGetId($values);
        }
        foreach ($c['texts'] as $f) {
            if($entity==='publications'&&$f==='content'){\Core\translation\EntityText::save('user_publications',$id,'content',trim((string)($d[$f]??'')),$actor);}else{$this->setText($c['table'], $id, $f, trim((string) ($d[$f] ?? '')), $actor);}
        }
        if ($entity === 'polls') {
            $this->saveOptions($id, $actor, (array) ($d['options'] ?? []));
        }
        return $id;
    }

    public function delete(int $actor, string $entity, int $id): void
    {
        $row = $this->owned($actor, $entity, $id);
        $c = $this->config($entity);
        $now = date('Y-m-d H:i:s');
        DB::table($c['table'])->where($c['key'], $id)->update(['deleted_at' => $now, 'deleted_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
    }

    public function cycleStatus(int $actor, string $entity, int $id): void
    {
        $this->requireBadgeWriter($actor, $entity);
        [, ,,$canModerate] = $this->organizations($actor);
        if (!$canModerate) {
            throw new RuntimeException('اجازه تغییر وضعیت تأیید را ندارید.');
        }
        $row = $this->owned($actor, $entity, $id);
        $c = $this->config($entity);
        $approved = empty($row['approved_at']);
        DB::table($c['table'])->where($c['key'], $id)->update(['approved_at' => $approved ? date('Y-m-d H:i:s') : null, 'approved_by' => $approved ? $actor : null, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor]);
    }

    public function vote(int $actor, int $pollId, array $options): void
    {
        \Modules\System\Services\PaymentMutex::run(db(), 'profile-poll:' . $pollId, fn () => \Core\translation\EntityText::atomic(db(), fn () => $this->voteScoped($actor, $pollId, $options)));
    }

    private function voteScoped(int $actor, int $pollId, array $options): void
    {
        $poll = DB::table('user_polls')->where('user_poll_id', $pollId)->where('status', 'active')->whereNull('deleted_at')->first();
        if (!$poll || ($poll['expires_at'] && strtotime($poll['expires_at']) < time())) {
            throw new RuntimeException('این نظرسنجی فعال نیست.');
        }
        $this->allowedOrganization($actor, (int) $poll['owner_id']);
        $options = array_values(array_unique(array_filter(array_map('intval', $options))));
        if (!$options) {
            throw new RuntimeException('یک گزینه را انتخاب کنید.');
        }
        if ($poll['type'] === 'single' && count($options) > 1) {
            throw new RuntimeException('فقط یک گزینه قابل انتخاب است.');
        }
        $valid = array_map(fn ($x) => (int) $x['user_poll_option_id'], DB::table('user_poll_options')->where('poll_id', $pollId)->whereIn('user_poll_option_id', $options)->whereNull('deleted_at')->get());
        if (count($valid) !== count($options)) {
            throw new RuntimeException('گزینه نامعتبر است.');
        }
        $old = DB::table('user_poll_votes')->where('poll_id', $pollId)->where('user_id', $actor)->whereNull('deleted_at')->get();
        $now = date('Y-m-d H:i:s');
        foreach ($old as $v) {
            DB::table('user_poll_votes')->where('user_poll_vote_id', (int) $v['user_poll_vote_id'])->update(['deleted_at' => $now, 'deleted_by' => $actor]);
        }
        foreach ($options as $o) {
            DB::table('user_poll_votes')->insert(['poll_id' => $pollId, 'option_id' => $o, 'user_id' => $actor, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor, 'approved_at' => $now, 'approved_by' => $actor]);
        }
        $this->refreshVotes($pollId);
    }

    private function saveOptions(int $poll, int $actor, array $options): void
    {
        $options = array_values(array_filter(array_map(fn ($x) => trim((string) $x), $options)));
        if (count($options) < 2) {
            throw new RuntimeException('حداقل دو گزینه برای نظرسنجی لازم است.');
        }
        $now = date('Y-m-d H:i:s');
        foreach (DB::table('user_poll_options')->where('poll_id', $poll)->whereNull('deleted_at')->get() as $o) {
            DB::table('user_poll_options')->where('user_poll_option_id', (int) $o['user_poll_option_id'])->update(['deleted_at' => $now, 'deleted_by' => $actor]);
        }
        foreach (DB::table('user_poll_votes')->where('poll_id', $poll)->whereNull('deleted_at')->get() as $v) {
            DB::table('user_poll_votes')->where('user_poll_vote_id', (int) $v['user_poll_vote_id'])->update(['deleted_at' => $now, 'deleted_by' => $actor]);
        }
        foreach ($options as $i => $label) {
            $id = (int) DB::table('user_poll_options')->insertGetId(['poll_id' => $poll, 'votes_count' => 0, 'sort_order' => $i, 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            $this->setText('user_poll_options', $id, 'label', $label, $actor);
        }
    }

    private function pollOptions(int $id, int $actor): array
    {
        $out = [];
        foreach (DB::table('user_poll_options')->where('poll_id', $id)->whereNull('deleted_at')->orderBy('sort_order')->get() as $o) {
            $oid = (int) $o['user_poll_option_id'];
            $o['id'] = $oid;
            $o['label'] = $this->text('user_poll_options', $oid, 'label');
            $o['selected'] = (bool) DB::table('user_poll_votes')->where('poll_id', $id)->where('option_id', $oid)->where('user_id', $actor)->whereNull('deleted_at')->first();
            $out[] = $o;
        }return $out;
    }

    private function refreshVotes(int $id): void
    {
        $count = DB::table('user_poll_votes')->where('poll_id', $id)->whereNull('deleted_at')->count();
        DB::table('user_polls')->where('user_poll_id', $id)->update(['votes_count' => $count]);
        foreach (DB::table('user_poll_options')->where('poll_id', $id)->whereNull('deleted_at')->get() as $o) {
            $n = DB::table('user_poll_votes')->where('option_id', (int) $o['user_poll_option_id'])->whereNull('deleted_at')->count();
            DB::table('user_poll_options')->where('user_poll_option_id', (int) $o['user_poll_option_id'])->update(['votes_count' => $n]);
        }
    }

    private function organizations(int $actor): array
    {
        $me = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$me || $actor !== (int) auth()->id()) throw new RuntimeException('کاربر معتبر نیست.', 401);
        $admin = SiteAdminAccess::allows($me);
        $wide = $branches = [];
        $reception = false;
        $directBranch = DB::table('academy_branches')->where('user_id', $actor)->whereNull('deleted_at')->first();
        $directAcademy = DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if ($admin) {
            foreach (DB::table('academies')->whereNull('deleted_at')->get() as $a) $wide[(int) $a['academy_id']] = true;
        } elseif ($directBranch) {
            $branches[(int) $directBranch['academy_id']][(int) $directBranch['branch_id']] = true;
        } elseif ($directAcademy) {
            $wide[(int) $directAcademy['academy_id']] = true;
        } else {
            foreach (DB::table('academy_branch_members')->where('user_id', $actor)->where('status', 'active')->whereNull('deleted_at')->get() as $member) {
                $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
                foreach ($roles as $role) {
                    $name = (string) $role['name'];
                    $academy = (int) $member['academy_id'];
                    $branch = (int) ($member['branch_id'] ?? 0);
                    if (!preg_match('/^(?:academy|branch|academy_branch)_(?:owner|manager|receptionist)(?:_[1-9][0-9]*)?$/D', $name)) continue;
                    $reception = $reception || str_contains($name, 'receptionist');
                    if (!$branch && preg_match('/^academy_(?:owner|manager|receptionist)(?:_[1-9][0-9]*)?$/D', $name)) $wide[$academy] = true;
                    elseif ($branch) $branches[$academy][$branch] = true;
                }
            }
        }
        $out = [];
        foreach (array_unique([...array_keys($wide), ...array_keys($branches)]) as $academyId) {
            $academy = DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first();
            if (!$academy) continue;
            $name = $this->text('academies', $academyId, 'title') ?: 'آموزشگاه ' . $academyId;
            if (isset($wide[$academyId]) && !empty($academy['user_id'])) {
                $uid = (int) $academy['user_id'];
                $out[$uid] = ['id' => $uid, 'entityId' => $academyId, 'academyId' => $academyId, 'branchId' => null, 'kind' => 'academy', 'name' => $name, 'writable' => true];
            }
            foreach (DB::table('academy_branches')->where('academy_id', $academyId)->whereNull('deleted_at')->get() as $branch) {
                $bid = (int) $branch['branch_id'];
                if ((!isset($wide[$academyId]) && !isset($branches[$academyId][$bid])) || empty($branch['user_id'])) continue;
                $uid = (int) $branch['user_id'];
                $out[$uid] = ['id' => $uid, 'entityId' => $bid, 'academyId' => $academyId, 'branchId' => $bid, 'kind' => 'branch', 'name' => $this->text('academy_branches', $bid, 'name') ?: 'شعبه ' . $bid, 'academyName' => $name, 'writable' => $admin || isset($wide[$academyId]) || $uid === $actor || isset($branches[$academyId][$bid])];
            }
        }
        if ($admin) {
            foreach (DB::table('users')->whereNull('deleted_at')->get() as $user) $out += $this->selfOrganization((int) $user['user_id'], $user);
        }
        $out += $this->selfOrganization($actor, $me);
        return [array_values($out), count($out) === 1, !$reception, $admin || (!$reception && ($directAcademy || $directBranch || $wide || $branches))];
    }

    private function allowedOrganization(int $a, int $u, bool $write = false): void
    {
        [$organizations] = $this->organizations($a);
        $o = null;
        foreach ($organizations as $item) {
            if ((int) $item['id'] === $u) {
                $o = $item;
                break;
            }
        }if (!$o || ($write && !$o['writable'])) {
            throw new RuntimeException('دسترسی به سازمان انتخاب‌شده ندارید.', 403);
        }
    }

    private function selfOrganization(int $actor, array $user): array
    {
        return [$actor => ['id' => $actor, 'entityId' => $actor, 'academyId' => null, 'branchId' => null, 'kind' => 'user', 'name' => (string) ($user['username'] ?? 'من'), 'writable' => true]];
    }

    private function hideOrganizationField(array $organizations, ?array $directBranch, bool $academyWide, array $branchIds): bool
    {
        return count($organizations) === 1 || count(array_filter($organizations, fn ($organization) => $organization['writable'])) <= 1
            && ($directBranch || (!$academyWide && count($branchIds) === 1));
    }

    private function owned(int $a, string $e, int $id): array
    {
        $this->requireBadgeWriter($a, $e);
        $c = $this->config($e);
        $r = DB::table($c['table'])->where($c['key'], $id)->whereNull('deleted_at')->first();
        if (!$r) {
            throw new RuntimeException('رکورد یافت نشد.');
        }
        $this->allowedOrganization($a, (int) $r[$e === 'polls' ? 'owner_id' : 'user_id'], true);
        return $r;
    }

    private function version(string $table, array $ids, string $ownerColumn): string
    {
        if (!$ids) {
            return '0';
        }
        $rows = DB::table($table)->whereIn($ownerColumn, $ids)->get();
        return sha1(json_encode(array_map(fn ($r) => [$r['updated_at'] ?? null, $r['deleted_at'] ?? null], $rows)));
    }

    private function badgeWriter(int $actor): bool
    {
        return $actor === (int) auth()->id() && SiteAdminAccess::allows(auth()->user());
    }

    private function badgeValues(int $actor, array $data): array
    {
        $level = (int) ($data['verification_level_id'] ?? 0);
        if (!$level || !DB::table('verification_levels')->where('verification_level_id', $level)->whereNull('deleted_at')->first()) throw new RuntimeException('انتخاب نشان الزامی است.', 422);
        return ['verification_level_id' => $level, 'granted_by' => $actor, 'status' => $this->one($data['status'] ?? 'active', ['active', 'expired', 'revoked'], 'وضعیت')];
    }

    private function badgeLevels(array &$items): array
    {
        $levels = [];
        foreach (DB::table('verification_levels')->whereNull('deleted_at')->orderBy('priority')->get() as $level) {
            $id = (int) $level['verification_level_id'];
            $levels[] = ['id' => $id, 'code' => $level['code'], 'title' => $this->text('verification_levels', $id, 'title') ?: $level['code']];
        }
        $titles = array_column($levels, 'title', 'id');
        foreach ($items as &$item) $item['badge_title'] = $titles[(int) $item['verification_level_id']] ?? 'نشان';
        unset($item);
        return $levels;
    }

    private function requireBadgeWriter(int $actor, string $entity): void
    {
        if ($entity === 'badges' && !$this->badgeWriter($actor)) throw new RuntimeException('اعطا و تغییر نشان فقط برای مدیر سایت مجاز است.', 403);
    }

    private function config(string $e): array
    {
        if (!isset(self::CONFIG[$e])) {
            throw new RuntimeException('بخش نامعتبر است.');
        }
        return self::CONFIG[$e];
    }

    private function text(string $t, int $id, string $f): string
    {
        $r = DB::table('translations')->where('table_name', $t)->where('table_id', $id)->where('field', $f)->where('locale', 'fa')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
        return (string) ($r['value'] ?? '');
    }

    private function setText(string $t, int $id, string $f, string $v, int $a): void
    {
        $r = DB::table('translations')->where('table_name', $t)->where('table_id', $id)->where('field', $f)->where('locale', 'fa')->first();
        $x = ['value' => $v, 'version' => 1, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $a, 'deleted_at' => null, 'deleted_by' => null];
        if ($r) {
            DB::table('translations')->where('translation_id', (int) $r['translation_id'])->update($x);
        } else {
            DB::table('translations')->insert(['table_name' => $t, 'table_id' => $id, 'field' => $f, 'locale' => 'fa', 'created_at' => date('Y-m-d H:i:s'), 'created_by' => $a] + $x);
        }
    }

    private function one(string $v, array $a, string $n): string
    {
        if (!in_array($v, $a, true)) {
            throw new RuntimeException($n . ' نامعتبر است.');
        }
        return $v;
    }

    private function date($v, bool $time = false): ?string
    {
        $v = trim((string) $v);
        if ($v === '') {
            return null;
        }
        $v = str_replace('T', ' ', $v);
        $format = $time ? 'Y-m-d H:i' : 'Y-m-d';
        $input = substr($v, 0, $time ? 16 : 10);
        $d = \DateTime::createFromFormat('!' . $format, $input);
        if (!$d || $d->format($format) !== $input) {
            throw new RuntimeException('تاریخ معتبر نیست.');
        }
        return $d->format($time ? 'Y-m-d H:i:s' : 'Y-m-d');
    }

    private function url($v): ?string
    {
        $v = trim((string) $v);
        if ($v !== '' && (mb_strlen($v)>2048 || !filter_var($v, FILTER_VALIDATE_URL))) {
            throw new RuntimeException('نشانی اینترنتی معتبر نیست.');
        }
        return $v ?: null;
    }
}
