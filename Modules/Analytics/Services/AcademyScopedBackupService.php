<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;
use Modules\System\Services\SiteAdminAccess;

final class AcademyScopedBackupService
{
    private function assertSiteAdmin(int $actor): void
    {
        if ($actor !== 1) {
            throw new RuntimeException('دسترسی لازم برای این بخش را ندارید.', 403);
        }
    }

    public function create(int $actor): array
    {
        $this->assertSiteAdmin($actor);
        $user = DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first();
        if (!$user) {
            throw new RuntimeException('حساب کاربری یافت نشد.');
        }
        $data = [];
        $selected = [];
        $members = DB::table('academy_branch_members')->where('user_id', $actor)->get();
        $memberIds = $this->ids($members, 'member_id');
        // Export ownership, not authorship: created_by can refer to other users' records.
        $excluded = ['user_security_tokens', 'user_security_rate_limits', 'tracking_user_sessions'];
        foreach ($this->tables() as $table) {
            if (in_array($table, $excluded, true) || $table === 'user_sessions' || preg_match('/^auth_|^security_|token|password|otp/', $table)) {
                continue;
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/D', $table)) {
                continue;
            }
            $columns = $this->columns($table);
            $names = array_column($columns, 'Field');
            $owner = in_array('user_id', $names, true) ? 'user_id' : (in_array('owner_id', $names, true) ? 'owner_id' : null);
            $memberOwned = !$owner && str_starts_with($table, 'academy_branch_') && in_array('member_id', $names, true);
            if ((!$owner && !$memberOwned) || in_array($table, ['translations', 'f_translations'], true)) {
                continue;
            }
            $query = db()->prepare($memberOwned ? "SELECT * FROM `$table` WHERE " . $this->in('member_id', $memberIds) : "SELECT * FROM `$table` WHERE `$owner`=?");
            $query->execute($memberOwned ? [] : [$actor]);
            $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
            $primary = array_column(array_filter($columns, fn ($c) => $c['Key'] === 'PRI'), 'Field');
            if (count($primary) === 1) {
                $selected[$table] = array_column($rows, $primary[0]);
            }
            foreach ($rows as &$row) {
                foreach (array_keys($row) as $field) {
                    if (preg_match('/password|token|secret|hash|session|credential|remember|otp|api_key/i', $field)) {
                        unset($row[$field]);
                    }
                }
                if (isset($row['key']) && preg_match('/password|token|secret|credential|otp|api_key/i', (string) $row['key'])) {
                    $row['value'] = null;
                }
            }
            unset($row);
            $data[$table] = $rows;
        }
        foreach (array_intersect(['translations', 'f_translations'], $this->tables()) as $store) {
            $data[$store] = [];
            foreach ($selected as $table => $ids) {
                if (!$ids) continue;
                $query = db()->prepare("SELECT * FROM `$store` WHERE table_name=? AND " . $this->in('table_id', $ids));
                $query->execute([$table]);
                array_push($data[$store], ...$query->fetchAll(\PDO::FETCH_ASSOC));
            }
        }
        $dir = storage_path('backups/users/' . $actor);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('ایجاد پوشه خروجی ناموفق بود.');
        }
        $filename = 'user-export-' . $actor . '-' . bin2hex(random_bytes(8)) . '.json';
        $path = $dir . '/' . $filename;
        $json = json_encode(['userId' => $actor, 'generatedAt' => date(DATE_ATOM), 'records' => $data], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        if (file_put_contents($path, $json, LOCK_EX) === false) {
            throw new RuntimeException('ایجاد خروجی اطلاعات ناموفق بود.');
        }
        $relative = 'storage/backups/users/' . $actor;
        try {
            $id = DB::table('media_files')->insertGetId(['user_id' => $actor, 'disk' => 'private', 'directory' => $relative, 'filename' => $filename, 'extension' => 'json', 'mime_type' => 'application/json', 'type' => 'archive', 'path' => $relative . '/' . $filename, 'original_filename' => $filename, 'fileable_type' => 'user_export', 'fileable_id' => $actor, 'size' => strlen($json), 'checksum' => hash('sha256', $json), 'visibility' => 'private', 'created_by' => $actor, 'updated_by' => $actor]);
        } catch (\Throwable $error) {
            unlink($path);
            throw $error;
        }
        return ['id' => $id, 'filename' => $filename, 'size' => strlen($json)];
    }

    public function find(int $actor, int $id): array
    {
        $this->assertSiteAdmin($actor);
        $export = DB::table('media_files')->where('media_file_id', $id)->where('user_id', $actor)->where('fileable_type', 'user_export')->where('fileable_id', $actor)->whereNull('deleted_at')->first();
        if ($export) {
            return ['path' => base_path($export['path']), 'filename' => $export['filename'], 'mime' => 'application/json'];
        }
        throw new RuntimeException('خروجی اطلاعات یافت نشد.', 404);
    }

    private function tables(): array
    {
        if (db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $tables = db()->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(\PDO::FETCH_COLUMN);
            return db() instanceof \Core\database\PrefixedPDO ? array_map([\Core\database\TableNames::class, 'logical'], $tables) : $tables;
        }
        $q = db()->query("SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE() AND table_type='BASE TABLE' ORDER BY table_name");
        $tables = $q->fetchAll(\PDO::FETCH_COLUMN);
        return db() instanceof \Core\database\PrefixedPDO ? array_map([\Core\database\TableNames::class, 'logical'], $tables) : $tables;
    }

    private function columns(string $table): array
    {
        if (db()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $physical = db() instanceof \Core\database\PrefixedPDO ? \Core\database\TableNames::physical($table) : $table;
            return array_map(fn ($c) => ['Field' => $c['name'], 'Key' => $c['pk'] ? 'PRI' : ''], db()->query("PRAGMA table_info(`$physical`)")->fetchAll(\PDO::FETCH_ASSOC));
        }
        return db()->query("SHOW COLUMNS FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
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
