<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;
use Modules\System\Services\SiteAdminAccess;

final class AcademyScopedBackupService
{
    public function create(int $actor): array
    {
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        $academy = DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->first() ?: DB::table('academies')->where('created_by', $actor)->whereNull('deleted_at')->orderBy('academy_id')->first();
        if (!$academy && SiteAdminAccess::allows($user)) {
            $academy = DB::table('academies')->whereNull('deleted_at')->orderBy('academy_id')->first();
        }
        if (!$academy) {
            throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
        }
        $aid = (int) $academy['academy_id'];
        $branches = DB::table('academy_branches')->where('academy_id', $aid)->get();
        $branchIds = $this->ids($branches, 'branch_id');
        $members = $branchIds ? DB::table('academy_branch_members')->whereIn('branch_id', $branchIds)->get() : [];
        $memberIds = $this->ids($members, 'member_id');
        $courses = $branchIds ? DB::table('academy_branch_courses')->whereIn('branch_id', $branchIds)->get() : [];
        $courseIds = $this->ids($courses, 'course_id');
        $terms = $courseIds ? DB::table('academy_branch_course_terms')->whereIn('course_id', $courseIds)->get() : [];
        $termIds = $this->ids($terms, 'term_id');
        $sessions = $termIds ? DB::table('academy_branch_course_term_sessions')->whereIn('term_id', $termIds)->get() : [];
        $sessionIds = $this->ids($sessions, 'term_session_id');
        $bookingIds = $this->ids($sessions, 'booking_id');
        $invoices = $termIds ? DB::table('academy_branch_course_term_invoices')->whereIn('term_id', $termIds)->get() : [];
        $invoiceIds = $this->ids($invoices, 'term_invoice_id');
        $rooms = $branchIds ? DB::table('academy_branch_classrooms')->whereIn('branch_id', $branchIds)->get() : [];
        $roomIds = $this->ids($rooms, 'classroom_id');
        $filters = [
            'academies' => $this->in('academy_id', [$aid]), 'academy_branches' => $this->in('branch_id', $branchIds), 'academy_branch_members' => $this->in('member_id', $memberIds),
            'academy_branch_member_contracts' => $this->in('member_id', $memberIds), 'academy_branch_member_permissions' => $this->in('member_id', $memberIds), 'academy_branch_member_roles' => $this->in('member_id', $memberIds),
            'academy_branch_courses' => $this->in('course_id', $courseIds), 'academy_branch_course_terms' => $this->in('term_id', $termIds), 'academy_branch_course_term_sessions' => $this->in('term_session_id', $sessionIds),
            'academy_branch_course_term_enrollments' => $this->in('term_id', $termIds), 'academy_branch_course_term_invoices' => $this->in('term_invoice_id', $invoiceIds), 'academy_branch_course_term_invoice_installments' => $this->in('invoice_id', $invoiceIds),
            'academy_branch_course_term_session_attendances' => $this->in('session_id', $sessionIds), 'academy_branch_bookings' => $this->in('booking_id', $bookingIds), 'academy_branch_classrooms' => $this->in('classroom_id', $roomIds),
            'academy_branch_classroom_assets' => $this->in('classroom_id', $roomIds), 'academy_branch_scheduling_rules' => $this->in('branch_id', $branchIds), 'academy_documents' => $this->in('academy_id', [$aid]),
        ];
        // Explicit academy-owned tables only; never export authentication or user-wide data.
        $tables = array_values(array_intersect(array_keys($filters), $this->tables()));
        $selected = [];
        $dump = "-- Sornaz academy-scoped backup\n-- academy_id: $aid\n-- generated_at: " . date(DATE_ATOM) . "\nSET NAMES utf8mb4;\nSTART TRANSACTION;\n\n";
        foreach ($tables as $t) {
            $where = $filters[$t] ?? null;
            if (!$where) {
                continue;
            }
            $rows = db()->query("SELECT * FROM `$t` WHERE $where")->fetchAll();
            if (!$rows) {
                continue;
            }
            $selected[$t] = [];
            foreach ($rows as $r) {
                $pk = array_key_first($r);
                $selected[$t][] = $r[$pk];
                $cols = '`' . implode('`,`', array_map(fn ($x) => str_replace('`', '``', $x), array_keys($r))) . '`';
                $vals = implode(',', array_map(fn ($v) => $v === null ? 'NULL' : db()->quote((string) $v), array_values($r)));
                $dump .= "INSERT INTO `$t` ($cols) VALUES ($vals);\n";
            }$dump .= "\n";
        }
        $translationParts = [];
        foreach ($selected as $t => $ids) {
            if ($ids) {
                $translationParts[] = "(`table_name`=" . db()->quote($t) . ' AND ' . $this->in('table_id', $ids) . ')';
            }
        }
        if ($translationParts) {
            $rows = db()->query('SELECT * FROM translations WHERE ' . implode(' OR ', $translationParts))->fetchAll();
            foreach ($rows as $r) {
                $cols = '`' . implode('`,`', array_keys($r)) . '`';
                $vals = implode(',', array_map(fn ($v) => $v === null ? 'NULL' : db()->quote((string) $v), array_values($r)));
                $dump .= "INSERT INTO `translations` ($cols) VALUES ($vals);\n";
            }
        }
        $dump .= "\nCOMMIT;\n";
        $dir = storage_path('backups/academy/' . $aid);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('پوشه پشتیبان قابل ایجاد نیست.');
        }
        $filename = 'academy-scoped-v2-' . $aid . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.sql';
        $path = $dir . '/' . $filename;
        if (file_put_contents($path, $dump) === false) {
            throw new RuntimeException('ساخت فایل پشتیبان ناموفق بود.');
        }
        $size = filesize($path);
        $id = DB::table('media_files')->insertGetId(['user_id' => (int) $academy['user_id'], 'disk' => 'private', 'directory' => str_replace('\\', '/', dirname(str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path))), 'filename' => $filename, 'extension' => 'sql', 'mime_type' => 'application/sql', 'type' => 'archive', 'collection' => null, 'path' => str_replace('\\', '/', str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path)), 'original_filename' => $filename, 'fileable_type' => 'academy_backup', 'fileable_id' => $aid, 'size' => $size, 'checksum' => hash_file('sha256', $path), 'visibility' => 'private', 'created_by' => $actor, 'updated_by' => $actor]);
        return ['id' => $id, 'path' => $path, 'filename' => $filename, 'size' => $size];
    }

    public function find(int $actor, int $id): array
    {
        $academy = DB::table('academies')->where('user_id', $actor)->whereNull('deleted_at')->first() ?: DB::table('academies')->where('created_by', $actor)->whereNull('deleted_at')->orderBy('academy_id')->first();
        if (!$academy) {
            throw new RuntimeException('آموزشگاه یافت نشد.');
        }
        $m = DB::table('media_files')->where('media_file_id', $id)->where('fileable_type', 'academy_backup')->where('fileable_id', (int) $academy['academy_id'])->whereNull('deleted_at')->first();
        if (!$m || !str_starts_with((string) $m['filename'], 'academy-scoped-v2-')) {
            throw new RuntimeException('پشتیبان یافت نشد.');
        }
        return ['path' => base_path($m['path']), 'filename' => $m['original_filename'] ?: $m['filename']];
    }

    private function tables(): array
    {
        $q = db()->query("SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE() AND table_type='BASE TABLE' ORDER BY table_name");
        return $q->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function ids(array $r, string $k): array
    {
        return array_values(array_unique(array_filter(array_map(fn ($x) => (int) ($x[$k] ?? 0), $r))));
    }

    private function in(string $c, array $v): string
    {
        return '`' . $c . '` IN (' . implode(',', array_map('intval', $v ?: [0])) . ')';
    }
}
