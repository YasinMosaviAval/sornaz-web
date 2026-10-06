<?php

namespace Modules\Analytics\Services;

use PDO;
use RuntimeException;
use Modules\System\Services\SiteAdminAccess;

final class UserPointService
{
    private static bool $schemaReady = false;

    public function index(int $actor): array
    {
        $pdo = db();
        if (!self::ensureSchema($pdo)) {
            throw new RuntimeException('بخش امتیازدهی هنوز آماده نیست؛ مدیر سایت باید به‌روزرسانی پایگاه داده را تکمیل کند.', 503);
        }
        $this->processTracking($pdo, $actor);
        $rules = $pdo->query("SELECT r.*,".\Core\translation\EntityText::expression('user_point_rules','r.user_point_rule_id','description')." AS description,IF(r.academy_id IS NULL,'سراسری',CONCAT('آموزشگاه ',r.academy_id)) academy_name,IF(r.branch_id IS NULL,NULL,CONCAT('شعبه ',r.branch_id)) branch_name FROM user_point_rules r WHERE r.deleted_at IS NULL ORDER BY r.user_point_rule_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $q = $pdo->prepare("SELECT type,COALESCE(SUM(points),0) total FROM user_points WHERE user_id=? AND deleted_at IS NULL AND approved_at IS NOT NULL GROUP BY type");
        $q->execute([$actor]);
        $balance = ['general' => 0, 'professional' => 0];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $balance[$r['type']] = (int) $r['total'];
        }
        $recent = $pdo->prepare("SELECT user_point_id,type,points,action,reference_type,reference_id,created_at FROM user_points WHERE user_id=? AND deleted_at IS NULL ORDER BY user_point_id DESC LIMIT 15");
        $recent->execute([$actor]);
        return ['rules' => array_map([$this, 'mapRule'], $rules), 'balance' => $balance, 'recent' => $recent->fetchAll(PDO::FETCH_ASSOC), 'organizations' => $this->organizations($pdo, $actor), 'canManage' => SiteAdminAccess::allows(auth()->user()), 'version' => $this->version($pdo)];
    }

    public function store(int $actor, array $data): int
    {
        return \Core\translation\EntityText::atomic(db(),fn()=>$this->persistStore($actor,$data));
    }

    private function persistStore(int $actor, array $data): int
    {
        $pdo = db();
        if (!self::ensureSchema($pdo)) {
            throw new RuntimeException('بخش امتیازدهی هنوز آماده نیست؛ مدیر سایت باید به‌روزرسانی پایگاه داده را تکمیل کند.', 503);
        }
        $clean = $this->validate($data);
        $description=$clean['description'];unset($clean['description']);
        $clean['created_at'] = date('Y-m-d H:i:s');
        $clean['created_by'] = $actor;
        $clean['updated_at'] = $clean['created_at'];
        $clean['updated_by'] = $actor;
        $cols = array_keys($clean);
        $q = $pdo->prepare('INSERT INTO user_point_rules (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')');
        $q->execute(array_values($clean));
        $id=(int)$pdo->lastInsertId();
        \Core\translation\EntityText::save('user_point_rules',$id,'description',$description,$actor,$pdo);
        return $id;
    }

    public function update(int $actor, int $id, array $data): void
    {
        \Core\translation\EntityText::atomic(db(),fn()=>$this->persistUpdate($actor,$id,$data));
    }

    private function persistUpdate(int $actor, int $id, array $data): void
    {
        $pdo = db();
        if (!self::ensureSchema($pdo)) {
            throw new RuntimeException('بخش امتیازدهی هنوز آماده نیست؛ مدیر سایت باید به‌روزرسانی پایگاه داده را تکمیل کند.', 503);
        }
        $clean = $this->validate($data);
        $description=$clean['description'];unset($clean['description']);
        $clean['updated_at'] = date('Y-m-d H:i:s');
        $clean['updated_by'] = $actor;
        $set = implode(',', array_map(fn ($x) => "`$x`=?", array_keys($clean)));
        $q = $pdo->prepare("UPDATE user_point_rules SET $set WHERE user_point_rule_id=? AND deleted_at IS NULL");
        $q->execute([...array_values($clean), $id]);
        \Core\translation\EntityText::save('user_point_rules',$id,'description',$description,$actor,$pdo);
        if (!\Core\database\DB::table('user_point_rules')->where('user_point_rule_id',$id)->whereNull('deleted_at')->first()) {
            throw new RuntimeException('قانون امتیاز پیدا نشد.');
        }
    }

    public function delete(int $actor, int $id): void
    {
        $q = db()->prepare('UPDATE user_point_rules SET deleted_at=?,deleted_by=? WHERE user_point_rule_id=? AND deleted_at IS NULL');
        $q->execute([date('Y-m-d H:i:s'), $actor, $id]);
    }

    public static function recordDatabaseAction(PDO $pdo, string $table, string $operation, array $data, ?int $entityId, ?int $actor): void
    {
        if (!$actor || in_array($table, ['user_points', 'user_point_rules', 'user_messages', 'translations', 'comments', 'public_ratings', 'tracking_ingestion_batches', 'tracking_user_events', 'tracking_user_page_views', 'tracking_user_sessions', 'tracking_user_activity_intervals', 'tracking_user_content_engagements'], true)) {
            return;
        }
        if (!self::ensureSchema($pdo)) {
            return;
        }
        $recipient = self::recipient($pdo, $data, $actor);
        self::award($pdo, $recipient, 'database', $table . '.' . $operation, $table, $entityId, (string) ($entityId ?? ''));
    }

    public static function recordTrackingEvents(PDO $pdo, ?int $userId, array $events, int $pageViewId): void
    {
        if (!$userId || !self::ensureSchema($pdo)) {
            return;
        }
        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }
            $name = preg_replace('/[^a-zA-Z0-9_.:-]+/', '_', trim((string) ($event['name'] ?? '')));
            if (!$name) {
                continue;
            }
            $uuid = (string) ($event['uuid'] ?? '');
            self::award($pdo, $userId, 'tracking', $name, 'tracking_event', $pageViewId, $uuid);
        }
    }

    public static function recordPublicAction(PDO $pdo, ?int $userId, string $action, string $referenceType, int $referenceId, ?string $eventKey = null): void
    {
        if (!$userId || !self::ensureSchema($pdo)) {
            return;
        }
        self::award($pdo, $userId, 'database', $action, $referenceType, $referenceId, $eventKey ?: $referenceType . ':' . $referenceId);
    }

    private static function award(PDO $pdo, int $userId, string $source, string $action, string $referenceType, ?int $referenceId, string $eventKey): void
    {
        $scope = self::userScope($pdo, $userId);
        $sql = "SELECT * FROM user_point_rules WHERE source=? AND action=? AND status='active' AND deleted_at IS NULL AND (academy_id IS NULL OR academy_id=?) AND (branch_id IS NULL OR branch_id=?)";
        $q = $pdo->prepare($sql);
        $q->execute([$source, $action, $scope['academy_id'], $scope['branch_id']]);
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $rid = (int) $r['user_point_rule_id'];
            $today = date('Y-m-d');
            $cap = (int) $r['daily_cap'];
            if ($cap > 0) {
                $c = $pdo->prepare('SELECT COUNT(*) FROM user_points WHERE user_id=? AND rule_id=? AND created_at>=? AND deleted_at IS NULL');
                $c->execute([$userId, $rid, $today . ' 00:00:00']);
                if ((int) $c->fetchColumn() >= $cap) {
                    continue;
                }
            }$cool = (int) $r['cooldown_minutes'];
            if ($cool > 0) {
                $c = $pdo->prepare('SELECT created_at FROM user_points WHERE user_id=? AND rule_id=? AND deleted_at IS NULL ORDER BY user_point_id DESC LIMIT 1');
                $c->execute([$userId, $rid]);
                $last = $c->fetchColumn();
                if ($last && strtotime($last) > time() - $cool * 60) {
                    continue;
                }
            }$bucket = $r['repeat_mode'] === 'once' ? 'once' : ($r['repeat_mode'] === 'daily' ? $today : ($eventKey ?: uniqid('', true)));
            $key = hash('sha256', implode('|', [$rid, $userId, $bucket, $referenceType, $referenceId]));
            $ins = $pdo->prepare("INSERT IGNORE INTO user_points (user_id,type,points,action,reference_type,reference_id,rule_id,award_key,metadata,created_at,created_by,updated_at,updated_by,approved_at,approved_by) VALUES (?,?,?,?,?,?,?,?,?,NOW(),?,NOW(),?,NOW(),?)");
            $ins->execute([$userId, $r['point_type'], (int) $r['points'], $r['action'], $referenceType, $referenceId, $rid, $key, json_encode(['source' => $source, 'event_key' => $eventKey], JSON_UNESCAPED_UNICODE), $userId, $userId, $userId]);
        }
    }

    private static function ensureSchema(PDO $pdo): bool
    {
        if (self::$schemaReady) {
            return true;
        }
        // Never run DDL here: MySQL would implicitly commit the caller's work.
        $rulesTable = $pdo instanceof \Core\database\PrefixedPDO ? \Core\database\TableNames::physical('user_point_rules') : 'user_point_rules';
        $pointsTable = $pdo instanceof \Core\database\PrefixedPDO ? \Core\database\TableNames::physical('user_points') : 'user_points';
        $tables = (int) $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('$rulesTable','$pointsTable')")->fetchColumn();
        $columns = (int) $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$pointsTable' AND COLUMN_NAME IN ('rule_id','award_key','metadata')")->fetchColumn();
        $index = (int) $pdo->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$pointsTable' AND INDEX_NAME='uq_user_points_award_key' AND NON_UNIQUE=0")->fetchColumn();
        if ($tables !== 2 || $columns !== 3 || !$index) {
            return false;
        }
        if (!$pdo->inTransaction()) {
            self::seed($pdo);
        }
        self::$schemaReady = true;
        return true;
    }

    private static function seed(PDO $pdo): void
    {
        $rules = [
            ['تکمیل/ویرایش پروفایل', 'general', 'profile', 25, 'database', 'users.update', 'daily', 1, 0],
            ['ثبت‌نام در دوره', 'general', 'academic', 40, 'database', 'academy_branch_course_term_enrollments.insert', 'event', 0, 0],
            ['به‌روزرسانی ثبت‌نام دوره', 'general', 'academic', 10, 'database', 'academy_branch_course_term_enrollments.update', 'daily', 2, 0],
            ['حضور در کلاس', 'general', 'attendance', 10, 'database', 'academy_branch_course_term_session_attendances.insert', 'event', 0, 0],
            ['ثبت وضعیت حضور', 'general', 'attendance', 8, 'database', 'academy_branch_course_term_session_attendances.update', 'event', 0, 0],
            ['پرداخت شهریه', 'general', 'financial', 50, 'database', 'financial_system_payments.insert', 'event', 0, 0],
            ['به‌روزرسانی پرداخت', 'professional', 'financial', 12, 'database', 'financial_system_payments.update', 'daily', 10, 0],
            ['ثبت تراکنش موفق', 'general', 'financial', 30, 'database', 'financial_system_transactions.insert', 'event', 0, 0],
            ['ثبت فاکتور دوره', 'professional', 'financial', 20, 'database', 'academy_branch_course_term_invoices.insert', 'event', 0, 0],
            ['ثبت قسط فاکتور', 'general', 'financial', 15, 'database', 'academy_branch_course_term_invoice_installments.insert', 'event', 0, 0],
            ['ارسال بازخورد یا دیدگاه', 'general', 'social', 10, 'database', 'comments.insert', 'daily', 3, 0],
            ['به‌روزرسانی دیدگاه', 'general', 'social', 3, 'database', 'comments.update', 'daily', 2, 0],
            ['امتیاز به مقاله', 'general', 'social', 3, 'database', 'public.article.rate', 'event', 10, 0],
            ['امتیاز به نظر مقاله', 'general', 'social', 2, 'database', 'public.comment.rate', 'event', 15, 0],
            ['پاسخ به نظر مقاله', 'general', 'social', 5, 'database', 'public.comment.reply', 'event', 10, 0],
            ['ارسال نظر برای مقاله', 'general', 'social', 8, 'database', 'public.comment.submit', 'event', 5, 0],
            ['دریافت نظر برای مقاله', 'general', 'social', 4, 'database', 'public.article.comment.received', 'event', 20, 0],
            ['دریافت پاسخ برای نظر مقاله', 'general', 'social', 3, 'database', 'public.comment.reply.received', 'event', 20, 0],
            ['امتیاز دادن به پروفایل کاربر', 'general', 'social', 3, 'database', 'public.profile.rate', 'event', 10, 0],
            ['دریافت امتیاز برای پروفایل', 'professional', 'social', 5, 'database', 'public.profile.rating.received', 'event', 20, 0],
            ['ثبت محتوای آموزشی', 'professional', 'academic', 30, 'database', 'posts.insert', 'daily', 3, 0],
            ['بهبود محتوای آموزشی', 'professional', 'academic', 8, 'database', 'posts.update', 'daily', 5, 0],
            ['بارگذاری رسانه', 'professional', 'achievement', 10, 'database', 'media_files.insert', 'daily', 5, 0],
            ['تکمیل اطلاعات رسانه', 'professional', 'achievement', 4, 'database', 'media_files.update', 'daily', 5, 0],
            ['ایجاد دوره آموزشی', 'professional', 'academic', 60, 'database', 'academy_branch_courses.insert', 'event', 0, 0],
            ['به‌روزرسانی دوره آموزشی', 'professional', 'academic', 15, 'database', 'academy_branch_courses.update', 'daily', 5, 0],
            ['ایجاد ترم آموزشی', 'professional', 'academic', 50, 'database', 'academy_branch_course_terms.insert', 'event', 0, 0],
            ['به‌روزرسانی ترم آموزشی', 'professional', 'academic', 12, 'database', 'academy_branch_course_terms.update', 'daily', 5, 0],
            ['ایجاد جلسه آموزشی', 'professional', 'academic', 20, 'database', 'academy_branch_course_term_sessions.insert', 'daily', 10, 0],
            ['به‌روزرسانی جلسه آموزشی', 'professional', 'academic', 6, 'database', 'academy_branch_course_term_sessions.update', 'daily', 10, 0],
            ['ثبت برنامه زمانی', 'professional', 'management', 15, 'database', 'user_availabilities.insert', 'daily', 5, 0],
            ['به‌روزرسانی برنامه زمانی', 'professional', 'management', 5, 'database', 'user_availabilities.update', 'daily', 5, 0],
            ['ثبت قانون زمان‌بندی', 'professional', 'management', 20, 'database', 'academy_branch_scheduling_rules.insert', 'daily', 5, 0],
            ['ثبت رزرو', 'general', 'engagement', 15, 'database', 'academy_branch_bookings.insert', 'daily', 5, 0],
            ['تأیید یا مدیریت رزرو', 'professional', 'management', 8, 'database', 'academy_branch_bookings.update', 'daily', 10, 0],
            ['افزودن درس به پروفایل', 'professional', 'profile', 15, 'database', 'user_lessons.insert', 'event', 0, 0],
            ['به‌روزرسانی درس کاربر', 'professional', 'profile', 5, 'database', 'user_lessons.update', 'daily', 3, 0],
            ['افزودن ساز به پروفایل', 'professional', 'profile', 15, 'database', 'user_instruments.insert', 'event', 0, 0],
            ['به‌روزرسانی ساز کاربر', 'professional', 'profile', 5, 'database', 'user_instruments.update', 'daily', 3, 0],
            ['افزودن هنرجو یا عضو', 'professional', 'management', 10, 'database', 'academy_branch_members.insert', 'daily', 10, 0],
            ['تکمیل اطلاعات عضو', 'professional', 'management', 6, 'database', 'academy_branch_members.update', 'daily', 10, 0],
            ['ثبت قرارداد عضو', 'professional', 'management', 20, 'database', 'academy_branch_member_contracts.insert', 'event', 0, 0],
            ['به‌روزرسانی قرارداد عضو', 'professional', 'management', 8, 'database', 'academy_branch_member_contracts.update', 'daily', 5, 0],
            ['تخصیص نقش به عضو', 'professional', 'management', 12, 'database', 'academy_branch_member_roles.insert', 'daily', 10, 0],
            ['تخصیص دسترسی به عضو', 'professional', 'management', 12, 'database', 'academy_branch_member_permissions.insert', 'daily', 10, 0],
            ['ایجاد کلاس', 'professional', 'academic', 25, 'database', 'academy_branch_classrooms.insert', 'daily', 5, 0],
            ['به‌روزرسانی کلاس', 'professional', 'academic', 6, 'database', 'academy_branch_classrooms.update', 'daily', 5, 0],
            ['افزودن امکانات کلاس', 'professional', 'academic', 8, 'database', 'academy_branch_classroom_assets.insert', 'daily', 10, 0],
            ['ثبت سند آموزشگاه', 'professional', 'management', 15, 'database', 'academy_documents.insert', 'daily', 5, 0],
            ['عضویت در لیست انتظار', 'general', 'academic', 10, 'database', 'academy_branch_course_term_waiting_list.insert', 'event', 0, 0],
            ['ارسال موفق فرم', 'general', 'engagement', 2, 'tracking', 'form_submit', 'daily', 5, 10],
            ['استفاده فعال از بخش‌های پنل', 'general', 'engagement', 1, 'tracking', 'section_enter', 'daily', 10, 5],
            ['مطالعه محتوای سایت', 'general', 'engagement', 2, 'tracking', 'content_read', 'daily', 5, 10],
            ['استفاده از جستجو', 'general', 'engagement', 1, 'tracking', 'search', 'daily', 5, 5],
            ['دریافت فایل یا گزارش', 'general', 'engagement', 3, 'tracking', 'download', 'daily', 5, 5],
            ['تکمیل مشاهده صفحه', 'general', 'engagement', 1, 'tracking', 'page_leave', 'daily', 5, 10]
        ];
        $exists = $pdo->prepare('SELECT COUNT(*) FROM user_point_rules WHERE source=? AND action=? AND academy_id IS NULL AND branch_id IS NULL AND deleted_at IS NULL');
        $q = $pdo->prepare("INSERT INTO user_point_rules(title,point_type,category,points,source,action,repeat_mode,daily_cap,cooldown_minutes,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,'active',NOW(),NOW())");
        foreach ($rules as $r) {
            $exists->execute([$r[4], $r[5]]);
            if (!(int) $exists->fetchColumn()) {
                $q->execute($r);
            }
        }
    }

    private function validate(array $d): array
    {
        $title = trim((string) ($d['title'] ?? ''));
        $action = trim((string) ($d['action'] ?? ''));
        $points = (int) ($d['points'] ?? 0);
        if ($title === '' || $action === '' || $points < 1 || $points > 100000) {
            throw new RuntimeException('عنوان، عملیات و امتیاز معتبر الزامی است.');
        }
        $enum = fn ($v, $a, $x) => in_array($v, $a, true) ? $v : $x;
        return ['title' => $title, 'summary' => trim((string) ($d['summary'] ?? '')) ?: null, 'description' => trim((string) ($d['description'] ?? '')) ?: null, 'point_type' => $enum($d['type'] ?? '', ['general', 'professional'], 'general'), 'category' => preg_replace('/[^a-z0-9_-]/i', '', (string) ($d['category'] ?? 'engagement')), 'points' => $points, 'source' => $enum($d['source'] ?? '', ['database', 'tracking', 'manual'], 'database'), 'action' => preg_replace('/[^a-zA-Z0-9_.:-]/', '', $action), 'reference_type' => trim((string) ($d['referenceType'] ?? '')) ?: null, 'repeat_mode' => $enum($d['repeatMode'] ?? '', ['event', 'daily', 'once'], 'event'), 'daily_cap' => max(0, min(1000, (int) ($d['dailyCap'] ?? 0))), 'cooldown_minutes' => max(0, min(525600, (int) ($d['cooldownMinutes'] ?? 0))), 'academy_id' => (int) ($d['academyId'] ?? 0) ?: null, 'branch_id' => (int) ($d['branchId'] ?? 0) ?: null, 'status' => $enum($d['status'] ?? '', ['active', 'inactive'], 'active')];
    }

    private function mapRule(array $r): array
    {
        return ['id' => (int) $r['user_point_rule_id'], 'title' => $r['title'], 'summary' => $r['summary'], 'description' => $r['description'], 'type' => $r['point_type'], 'category' => $r['category'], 'points' => (int) $r['points'], 'source' => $r['source'], 'action' => $r['action'], 'referenceType' => $r['reference_type'], 'repeatMode' => $r['repeat_mode'], 'dailyCap' => (int) $r['daily_cap'], 'cooldownMinutes' => (int) $r['cooldown_minutes'], 'academyId' => (int) ($r['academy_id'] ?? 0), 'branchId' => (int) ($r['branch_id'] ?? 0), 'branchName' => $r['branch_name'] ?: ($r['academy_name'] ?: 'سراسری'), 'status' => $r['status']];
    }

    private function organizations(PDO $pdo, int $actor): array
    {
        $out = [['id' => 0, 'name' => 'سراسری', 'academyId' => 0]];
        $q = $pdo->prepare('SELECT DISTINCT b.branch_id id,b.academy_id FROM academy_branches b LEFT JOIN academy_branch_members m ON m.branch_id=b.branch_id AND m.deleted_at IS NULL WHERE b.deleted_at IS NULL AND (b.user_id=? OR m.user_id=? OR b.created_by=?)');
        $q->execute([$actor, $actor, $actor]);
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[] = ['id' => (int) $r['id'], 'academyId' => (int) $r['academy_id'], 'name' => 'شعبه ' . (int) $r['id']];
        }
        return $out;
    }

    private function processTracking(PDO $pdo, int $actor): void
    {
        $q = $pdo->prepare("SELECT event_uuid,event_name,page_view_id FROM tracking_user_events WHERE user_id=? AND created_at>=DATE_SUB(NOW(),INTERVAL 2 DAY) ORDER BY tracking_user_event_id DESC LIMIT 500");
        $q->execute([$actor]);
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $e) {
            self::award($pdo, $actor, 'tracking', $e['event_name'], 'tracking_event', (int) $e['page_view_id'], $e['event_uuid']);
        }
    }

    private function version(PDO $pdo): string
    {
        return (string) $pdo->query("SELECT GREATEST(COALESCE(MAX(updated_at),'1970-01-01'),COALESCE((SELECT MAX(updated_at) FROM user_points),'1970-01-01')) FROM user_point_rules")->fetchColumn();
    }

    private static function userScope(PDO $pdo, int $user): array
    {
        $q = $pdo->prepare('SELECT academy_id,branch_id FROM academy_branch_members WHERE user_id=? AND deleted_at IS NULL ORDER BY member_id LIMIT 1');
        $q->execute([$user]);
        $r = $q->fetch(PDO::FETCH_ASSOC) ?: [];
        return ['academy_id' => $r['academy_id'] ?? null, 'branch_id' => $r['branch_id'] ?? null];
    }

    private static function recipient(PDO $pdo, array $data, int $actor): int
    {
        if (!empty($data['user_id'])) {
            return (int) $data['user_id'];
        }
        if (!empty($data['member_id'])) {
            $q = $pdo->prepare('SELECT user_id FROM academy_branch_members WHERE member_id=? LIMIT 1');
            $q->execute([(int) $data['member_id']]);
            if ($id = $q->fetchColumn()) {
                return (int) $id;
            }
        }return $actor;
    }
}
