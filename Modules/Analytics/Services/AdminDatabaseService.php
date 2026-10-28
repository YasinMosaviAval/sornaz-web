<?php

namespace Modules\Analytics\Services;

use RuntimeException;

final class AdminDatabaseService
{
    public function authorize(): void
    {
        $user = auth()->user();
        if (!$user) throw new RuntimeException('برای ادامه وارد حساب شوید.', 401);
        if ((int) ($user['user_id'] ?? 0) !== 1) throw new RuntimeException('این بخش فقط برای مدیر اصلی سایت مجاز است.', 403);
    }

    private function connection(): AdminDatabaseConnection
    {
        $connection = new AdminDatabaseConnection(db());
        if (!in_array($connection->driver(), ['mysql', 'sqlite'], true)) throw new RuntimeException('این نوع دیتابیس پشتیبانی نمی‌شود.', 503);
        return $connection;
    }

    public function tables(array $filters = []): array
    {
        $this->authorize();
        $rows = $this->objects($this->connection());
        $search = is_scalar($filters['search'] ?? '') ? trim((string) ($filters['search'] ?? '')) : '';
        if ($search !== '') $rows = array_values(array_filter($rows, static fn ($row) => stripos($row['name'], $search) !== false));
        $total = count($rows); $page = $this->page($filters, $total);
        return ['tables' => array_slice($rows, ($page - 1) * 10, 10), 'total' => $total, 'page' => $page, 'perPage' => 10];
    }

    public function rows(array $filters): array
    {
        $this->authorize();
        $connection = $this->connection();
        $table = $this->selected($connection, $filters['table'] ?? null);
        return $connection->snapshot(function () use ($connection, $table, $filters) {
            $columns = $this->columns($connection, $table['name']);
            $name = AdminDatabaseConnection::identifier($table['name']);
            $total = (int) $connection->scalar('SELECT COUNT(*) FROM ' . $name);
            $page = $this->page($filters, $total); $offset = ($page - 1) * 10;
            $primary = array_column(array_filter($columns, static fn ($column) => $column['primary']), 'name');
            $order = $primary ?: array_slice(array_column($columns, 'name'), 0, 1);
            $fields = implode(',', array_map([AdminDatabaseConnection::class, 'identifier'], array_column($columns, 'name')));
            $orderSql = $order ? ' ORDER BY ' . implode(',', array_map([AdminDatabaseConnection::class, 'identifier'], $order)) : '';
            $rows = $connection->all("SELECT $fields FROM $name$orderSql LIMIT 10 OFFSET $offset");
            foreach ($rows as &$row) foreach ($columns as $column) $row[$column['name']] = $this->cell($row[$column['name']], $column);
            return ['table' => $table['name'], 'columns' => $columns, 'rows' => $rows, 'total' => $total, 'page' => $page, 'perPage' => 10, 'stableOrder' => (bool) $primary];
        });
    }

    public function download(array $filters): AdminDatabaseDownloadResponse
    {
        $this->authorize();
        $mode = $filters['mode'] ?? '';
        if (!in_array($mode, ['schema', 'data'], true)) throw new RuntimeException('نوع خروجی معتبر نیست.', 422);
        $connection = $this->connection();
        $selected = isset($filters['table']) && $filters['table'] !== '' ? [$this->selected($connection, $filters['table'])] : $this->objects($connection);
        return (new AdminDatabaseExporter($connection, $this))->export($selected, $mode === 'data');
    }

    public function objects(AdminDatabaseConnection $connection): array
    {
        $this->authorize();
        if ($connection->driver() === 'mysql') {
            $rows = $connection->all("SELECT TABLE_NAME name,TABLE_TYPE type,ENGINE engine,TABLE_ROWS estimatedRows,COALESCE(DATA_LENGTH,0)+COALESCE(INDEX_LENGTH,0) bytes FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE IN ('BASE TABLE','VIEW') ORDER BY TABLE_NAME");
        } else {
            $rows = $connection->all("SELECT name,upper(type) type,NULL engine,NULL estimatedRows,0 bytes FROM sqlite_master WHERE type IN ('table','view') AND name NOT LIKE 'sqlite_%' ORDER BY name");
        }
        foreach ($rows as &$row) {
            $row['type'] = $row['type'] === 'VIEW' ? 'view' : 'table';
            $row['estimatedRows'] = $row['estimatedRows'] === null ? null : (int) $row['estimatedRows'];
            $row['bytes'] = (int) $row['bytes'];
        }
        return $rows;
    }

    private function selected(AdminDatabaseConnection $connection, mixed $name): array
    {
        if (!is_string($name) || $name === '' || strlen($name) > 256) throw new RuntimeException('جدول معتبر را انتخاب کنید.', 422);
        foreach ($this->objects($connection) as $table) if ($table['name'] === $name) return $table;
        throw new RuntimeException('جدول پیدا نشد.', 404);
    }

    public function columns(AdminDatabaseConnection $connection, string $table): array
    {
        $this->authorize();
        $name = AdminDatabaseConnection::identifier($table);
        if ($connection->driver() === 'mysql') {
            return array_map(static fn ($column) => ['name' => $column['Field'], 'type' => $column['Type'], 'nullable' => $column['Null'] === 'YES', 'primary' => $column['Key'] === 'PRI', 'generated' => (bool) preg_match('/(?:VIRTUAL|STORED) GENERATED/i', $column['Extra'])], $connection->all('SHOW FULL COLUMNS FROM ' . $name));
        }
        return array_map(static fn ($column) => ['name' => $column['name'], 'type' => $column['type'], 'nullable' => !$column['notnull'], 'primary' => (bool) $column['pk'], 'generated' => in_array((int) ($column['hidden'] ?? 0), [2, 3], true)], $connection->all('PRAGMA table_xinfo(' . $name . ')'));
    }

    private function cell(mixed $value, array $column): mixed
    {
        if ($value === null) return null;
        if (is_int($value) && ($value > 9007199254740991 || $value < -9007199254740991)) return (string) $value;
        if (is_resource($value)) $value = stream_get_contents($value);
        if (is_string($value) && (preg_match('/blob|binary|^bit(?:\(|$)|^(?:geometry|point|linestring|polygon|multipoint|multilinestring|multipolygon|geometrycollection)$/i', $column['type']) || preg_match('//u', $value) !== 1)) return ['type' => 'binary', 'encoding' => 'base64', 'value' => base64_encode($value)];
        return $value;
    }

    private function page(array $filters, int $total): int
    {
        $page = filter_var($filters['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($page === false) throw new RuntimeException('شماره صفحه معتبر نیست.', 422);
        return min($page, max(1, (int) ceil($total / 10)));
    }
}
