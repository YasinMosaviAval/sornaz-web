<?php

namespace Modules\Analytics\Services;

use RuntimeException;

/** SQL is written to a private stream, never executed against the application database. */
final class AdminDatabaseExporter
{
    private string $schema = '';

    public function __construct(private AdminDatabaseConnection $connection, private AdminDatabaseService $catalog) {}

    public function export(array $objects, bool $data): AdminDatabaseDownloadResponse
    {
        $this->catalog->authorize();
        $stream = $this->temporaryStream();
        $session = [];
        try {
            $session = $this->sourceSession();
            $this->connection->snapshot(function () use ($objects, $data, $stream) { $this->writeDump($stream, $objects, $data); });
            rewind($stream);
            $scope = count($objects) === 1 ? substr(preg_replace('/[^A-Za-z0-9_-]/', '_', $objects[0]['name']), 0, 64) : 'all';
            $filename = 'sornaz-db-' . $scope . '-' . ($data ? 'schema-data-' : 'schema-') . gmdate('Ymd-His') . '.sql';
            $transactional = !array_filter($objects, fn ($object) => $object['type'] === 'table' && $this->connection->driver() === 'mysql' && strcasecmp((string) $object['engine'], 'InnoDB') !== 0);
            return new AdminDatabaseDownloadResponse($stream, $filename, $transactional ? 'transactional-snapshot' : 'best-effort');
        } catch (\Throwable $error) { fclose($stream); throw $error; }
        finally { if ($session) $this->restoreSourceSession($session); }
    }

    private function sourceSession(): array
    {
        if ($this->connection->driver() !== 'mysql') return [];
        $session = $this->connection->all('SELECT @@time_zone timezone,@@character_set_client client,@@character_set_results results,@@collation_connection collation,@@sql_mode sqlMode')[0];
        try {
            $this->connection->statement("SET time_zone='+00:00'")->closeCursor();
            $this->connection->statement('SET NAMES utf8mb4')->closeCursor();
            $this->connection->statement("SET sql_mode='NO_AUTO_VALUE_ON_ZERO'")->closeCursor();
        } catch (\Throwable $error) { $this->restoreSourceSession($session); throw $error; }
        return $session;
    }

    private function restoreSourceSession(array $session): void
    {
        $this->connection->statement('SET time_zone=?,character_set_client=?,character_set_results=?,collation_connection=?,sql_mode=?', array_values($session))->closeCursor();
    }

    private function temporaryStream(): mixed
    {
        $temporary = realpath(sys_get_temp_dir());
        if (!$temporary) throw new RuntimeException('فضای موقت امن برای خروجی روی این سرور تنظیم نشده است.', 503);
        foreach ([realpath(base_path()), realpath($_SERVER['DOCUMENT_ROOT'] ?? '')] as $public) {
            if ($temporary && $public && ($temporary === $public || str_starts_with($temporary . DIRECTORY_SEPARATOR, $public . DIRECTORY_SEPARATOR))) throw new RuntimeException('فضای موقت امن برای خروجی روی این سرور تنظیم نشده است.', 503);
        }
        $stream = tmpfile();
        if (!$stream) throw new RuntimeException('ایجاد فایل خروجی ناموفق بود.', 503);
        $metadata = stream_get_meta_data($stream);
        if (isset($metadata['uri']) && is_file($metadata['uri'])) @chmod($metadata['uri'], 0600);
        return $stream;
    }

    private function writeDump(mixed $stream, array $objects, bool $data): void
    {
        $mysql = $this->connection->driver() === 'mysql';
        $this->schema = $mysql ? (string) $this->connection->scalar('SELECT DATABASE()') : '';
        $this->write($stream, "-- Sornaz database export\n-- Generated UTC: " . gmdate('c') . "\n-- Scope: current database tables, views, indexes and triggers.\n");
        if ($mysql && array_filter($objects, static fn ($object) => $object['type'] === 'table' && strcasecmp((string) $object['engine'], 'InnoDB') !== 0)) $this->write($stream, "-- Non-transactional tables are exported on a best-effort basis; concurrent writes may be visible.\n");
        $this->write($stream, $mysql ? "SET @SORNAZ_OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;\nSET FOREIGN_KEY_CHECKS=0;\nSET @SORNAZ_OLD_SQL_MODE=@@SQL_MODE;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\nSET @SORNAZ_OLD_TIME_ZONE=@@TIME_ZONE;\nSET TIME_ZONE='+00:00';\nSET NAMES utf8mb4;\n" : "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n");
        $tables = array_values(array_filter($objects, static fn ($object) => $object['type'] === 'table'));
        $views = array_values(array_filter($objects, static fn ($object) => $object['type'] === 'view'));
        foreach ($tables as $table) $this->write($stream, $this->definition($table) . ";\n\n");
        if ($data) foreach ($tables as $table) $this->writeRows($stream, $table['name']);
        $this->writeViews($stream, $views);
        $this->writeExtras($stream, $tables);
        if ($data && !$mysql) $this->writeSequences($stream, $tables);
        $this->write($stream, $mysql ? "SET SQL_MODE=@SORNAZ_OLD_SQL_MODE;\nSET FOREIGN_KEY_CHECKS=@SORNAZ_OLD_FOREIGN_KEY_CHECKS;\nSET TIME_ZONE=@SORNAZ_OLD_TIME_ZONE;\n" : "COMMIT;\nPRAGMA foreign_keys=ON;\n");
        $this->write($stream, "-- Sornaz export complete.\n");
    }

    private function definition(array $object): string
    {
        if ($this->connection->driver() === 'sqlite') return (string) $this->connection->scalar('SELECT sql FROM sqlite_master WHERE name=? AND type=?', [$object['name'], $object['type']]);
        $row = $this->connection->all('SHOW CREATE ' . ($object['type'] === 'view' ? 'VIEW ' : 'TABLE ') . AdminDatabaseConnection::identifier($object['name']))[0];
        return $this->localDefinition($row[$object['type'] === 'view' ? 'Create View' : 'Create Table']);
    }

    private function localDefinition(string $sql): string
    {
        if ($this->schema === '') return $sql;
        $schema = preg_quote(AdminDatabaseConnection::identifier($this->schema), '~');
        // Quoted strings/comments are indivisible; never alter their contents.
        $pattern = '~\'(?:\\\\.|\'\'|[^\'\\\\])*\'|"(?:\\\\.|""|[^"\\\\])*"|--[^\r\n]*|\#[^\r\n]*|/\*[\s\S]*?\*/|' . $schema . '\s*\.\s*~s';
        return preg_replace_callback($pattern, static fn ($match) => str_starts_with($match[0], '`') ? '' : $match[0], $sql);
    }

    private function writeRows(mixed $stream, string $table): void
    {
        $columns = array_values(array_filter($this->catalog->columns($this->connection, $table), static fn ($column) => !$column['generated']));
        if (!$columns) return;
        $mysql = $this->connection->driver() === 'mysql';
        $fields = [];
        foreach ($columns as $index => $column) {
            $name = AdminDatabaseConnection::identifier($column['name']);
            $expression = $mysql && $this->spatial($column['type']) ? "ST_AsWKB($name)" : $name;
            if ($mysql && preg_match('/^bit(?:\(|$)/i', $column['type'])) $expression = "CAST($name AS UNSIGNED)";
            if ($mysql && preg_match('/^float\b/i', $column['type'])) $expression = "($name + 0e0)";
            $fields[] = $expression . ' AS __sornaz_value_' . $index;
            if (!$mysql) $fields[] = "typeof($name) AS __sornaz_type_$index";
            elseif ($this->spatial($column['type'])) $fields[] = "ST_SRID($name) AS __sornaz_srid_$index";
        }
        $names = implode(',', array_map([AdminDatabaseConnection::class, 'identifier'], array_column($columns, 'name')));
        $prefix = 'INSERT INTO ' . AdminDatabaseConnection::identifier($table) . " ($names) VALUES (";
        $this->connection->each('SELECT ' . implode(',', $fields) . ' FROM ' . AdminDatabaseConnection::identifier($table), function ($row) use ($stream, $columns, $prefix) {
            $values = [];
            foreach ($columns as $index => $column) $values[] = $this->literal($row['__sornaz_value_' . $index], $column['type'], $row['__sornaz_type_' . $index] ?? null, (int) ($row['__sornaz_srid_' . $index] ?? 0));
            $this->write($stream, $prefix . implode(',', $values) . ");\n");
        });
        $this->write($stream, "\n");
    }

    private function literal(mixed $value, string $type, ?string $storageType, int $srid): string
    {
        if ($value === null) return 'NULL';
        if (is_resource($value)) $value = stream_get_contents($value);
        $hex = "X'" . bin2hex((string) $value) . "'";
        if ($this->connection->driver() === 'sqlite') {
            if ($storageType === 'blob') return $hex;
            if ($storageType === 'integer') return (string) $value;
            if ($storageType === 'real') return str_replace(',', '.', sprintf('%.17g', $value));
            return "CAST($hex AS TEXT)";
        }
        if ($this->spatial($type)) return "ST_GeomFromWKB($hex,$srid)";
        if (preg_match('/^bit(?:\(|$)/i', $type) && preg_match('/^\d+$/D', (string) $value)) return (string) $value;
        if (preg_match('/blob|binary|^bit(?:\(|$)/i', $type)) return $hex;
        if (is_float($value)) return str_replace(',', '.', sprintf('%.17g', $value));
        if (preg_match('/^(?:tinyint|smallint|mediumint|int|bigint|decimal|numeric|float|double|real|year)\b/i', $type) && preg_match('/^-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?$/D', (string) $value)) return (string) $value;
        return '_utf8mb4 ' . $hex;
    }

    private function spatial(string $type): bool { return (bool) preg_match('/^(?:geometry|point|linestring|polygon|multipoint|multilinestring|multipolygon|geometrycollection)$/i', $type); }

    private function writeViews(mixed $stream, array $views): void
    {
        if ($this->connection->driver() === 'mysql') {
            // Placeholders allow arbitrary view dependency order, matching mysqldump's approach.
            foreach ($views as $view) {
                $columns = $this->catalog->columns($this->connection, $view['name']);
                $fields = array_map(static fn ($column) => AdminDatabaseConnection::identifier($column['name']) . ' ' . $column['type'], $columns);
                $this->write($stream, 'CREATE TABLE ' . AdminDatabaseConnection::identifier($view['name']) . ' (' . implode(',', $fields) . ");\n");
            }
        }
        foreach ($views as $view) {
            if ($this->connection->driver() === 'mysql') $this->write($stream, 'DROP TABLE ' . AdminDatabaseConnection::identifier($view['name']) . ";\n");
            $this->write($stream, $this->definition($view) . ";\n\n");
        }
    }

    private function writeExtras(mixed $stream, array $tables): void
    {
        $selected = array_column($tables, 'name');
        if ($this->connection->driver() === 'sqlite') {
            foreach ($this->connection->all("SELECT sql,tbl_name FROM sqlite_master WHERE type IN ('index','trigger') AND sql IS NOT NULL ORDER BY type,name") as $object) if (in_array($object['tbl_name'], $selected, true)) $this->write($stream, $object['sql'] . ";\n");
            return;
        }
        foreach ($this->connection->all('SELECT TRIGGER_NAME name,EVENT_OBJECT_TABLE tableName FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA=DATABASE() ORDER BY TRIGGER_NAME') as $trigger) {
            if (!in_array($trigger['tableName'], $selected, true)) continue;
            $row = $this->connection->all('SHOW CREATE TRIGGER ' . AdminDatabaseConnection::identifier($trigger['name']))[0];
            $definition = $this->localDefinition($row['SQL Original Statement']);
            $delimiter = '$$SORNAZ_' . bin2hex(random_bytes(6)) . '$$';
            $mode = "_utf8mb4 X'" . bin2hex($row['sql_mode']) . "'";
            $this->write($stream, "SET @SORNAZ_TRIGGER_MODE=@@SQL_MODE;\nSET SQL_MODE=$mode;\nDELIMITER $delimiter\n$definition$delimiter\nDELIMITER ;\nSET SQL_MODE=@SORNAZ_TRIGGER_MODE;\n");
        }
    }

    private function writeSequences(mixed $stream, array $tables): void
    {
        if (!$this->connection->scalar("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='sqlite_sequence'")) return;
        foreach ($this->connection->all('SELECT name,seq FROM sqlite_sequence') as $row) {
            if (!in_array($row['name'], array_column($tables, 'name'), true)) continue;
            $name = "CAST(X'" . bin2hex($row['name']) . "' AS TEXT)";
            $this->write($stream, "DELETE FROM sqlite_sequence WHERE name=$name;\nINSERT INTO sqlite_sequence(name,seq) VALUES ($name," . (int) $row['seq'] . ");\n");
        }
    }

    private function write(mixed $stream, string $sql): void
    {
        $offset = 0; $length = strlen($sql);
        while ($offset < $length) {
            $written = fwrite($stream, substr($sql, $offset));
            if ($written === false || $written === 0) throw new RuntimeException('فضای کافی برای ایجاد خروجی دیتابیس وجود ندارد.', 503);
            $offset += $written;
        }
    }
}
