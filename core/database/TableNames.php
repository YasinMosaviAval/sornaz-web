<?php

namespace Core\database;

/** Physical table names only; stored entity names and API resources stay logical. */
final class TableNames
{
    private static ?array $mapping = null;

    public static function mapping(): array
    {
        return self::$mapping ??= require dirname(__DIR__, 2) . '/config/table-names.php';
    }

    public static function physical(string $name): string
    {
        return self::mapping()[$name] ?? $name;
    }

    public static function logical(string $name): string
    {
        $logical = array_search($name, self::mapping(), true);
        return $logical === false ? $name : $logical;
    }

    /** Called at the PDO boundary, covering raw SQL and dynamic Builder queries. */
    public static function sql(string $sql): string
    {
        // Strings and comments are single tokens and never rewritten as identifiers.
        preg_match_all('/\s+|--[^\r\n]*|\#[^\r\n]*|\/\*[\s\S]*?\*\/|\'(?:\\\\.|\'\'|[^\'\\\\])*\'|"(?:\\\\.|""|[^"\\\\])*"|`(?:``|[^`])*`|[A-Za-z_][A-Za-z0-9_$]*|./s', $sql, $matches);
        $tokens = $matches[0];
        $significant = [];
        foreach ($tokens as $index => $token) {
            if (trim($token) !== '' && !preg_match('/^(--|\#|\/\*)/', $token)) {
                $significant[] = $index;
            }
        }
        [$tables, $aliases] = self::mapTables($tokens, $significant, $sql);
        self::mapQualifiers($tokens, $significant, $tables, $aliases);
        return implode('', $tokens);
    }

    private static function mapTables(array &$tokens, array $significant, string $sql): array
    {
        $aliases = [];
        $tables = [];
        $fromDepths = [];
        $depth = 0;
        $expectTable = false;
        $indexStatement = preg_match('/^\s*CREATE\s+(?:UNIQUE\s+)?(?:INDEX|TRIGGER)\b/i', $sql) === 1;
        foreach ($significant as $position => $index) {
            $token = $tokens[$index];
            $word = strtoupper($token);
            if ($token === ';') { $fromDepths = []; $expectTable = false; $depth = 0; continue; }
            if ($token === '(') { $depth++; $expectTable = false; continue; }
            if ($token === ')') { unset($fromDepths[$depth]); $depth--; $expectTable = false; continue; }
            if (in_array($word, ['WHERE', 'SET', 'VALUES', 'GROUP', 'ORDER', 'HAVING', 'LIMIT', 'UNION', 'RETURNING'], true)) {
                unset($fromDepths[$depth]);
                $expectTable = false;
            }
            if ($word === 'FROM') { $fromDepths[$depth] = true; }
            if (in_array($word, ['FROM', 'JOIN', 'INTO', 'UPDATE', 'TABLE', 'REFERENCES'], true) || ($word === 'ON' && $indexStatement) || ($token === ',' && isset($fromDepths[$depth]))) {
                $expectTable = true;
                continue;
            }
            if (!$expectTable) { continue; }
            if ($token === '.') { continue; }
            if (in_array($word, ['IF', 'NOT', 'EXISTS', 'ONLY', 'LOW_PRIORITY', 'IGNORE'], true)) { continue; }
            if (!preg_match('/^(?:[A-Za-z_][A-Za-z0-9_$]*|`(?:``|[^`])+`)$/', $token)) { $expectTable = false; continue; }
            // Schema-qualified identifiers: the final part is the table name.
            $next = $significant[$position + 1] ?? null;
            if ($next !== null && $tokens[$next] === '.') { continue; }
            $tables[trim($token, '`')] = true;
            $tokens[$index] = self::identifier($token);
            $expectTable = false;
            $alias = self::alias($tokens, $significant, $position + 1);
            if ($alias !== null) { $aliases[$alias] = true; }
        }
        return [$tables, $aliases];
    }

    private static function identifier(string $token): string
    {
        $physical = self::physical(trim($token, '`'));
        return str_starts_with($token, '`') ? '`' . $physical . '`' : $physical;
    }

    private static function alias(array $tokens, array $significant, int $position): ?string
    {
        if (isset($significant[$position]) && strtoupper($tokens[$significant[$position]]) === 'AS') { $position++; }
        $alias = $tokens[$significant[$position] ?? -1] ?? '';
        $keywords = ['WHERE','JOIN','INNER','LEFT','RIGHT','FULL','CROSS','ON','SET','VALUES','GROUP','ORDER','HAVING','LIMIT','UNION','USING','FOR','LOCK','RETURNING'];
        return preg_match('/^(?:[A-Za-z_][A-Za-z0-9_$]*|`[^`]+`)$/', $alias) && !in_array(strtoupper($alias), $keywords, true) ? trim($alias, '`') : null;
    }

    private static function mapQualifiers(array &$tokens, array $significant, array $tables, array $aliases): void
    {
        // Fully qualified columns without an alias (users.user_id, for example).
        foreach ($significant as $position => $index) {
            $name = trim($tokens[$index], '`');
            $next = $significant[$position + 1] ?? null;
            $previous = $significant[$position - 1] ?? null;
            if (isset($tables[$name]) && !isset($aliases[$name]) && $next !== null && $tokens[$next] === '.' && ($previous === null || $tokens[$previous] !== '.')) {
                $tokens[$index] = self::identifier($tokens[$index]);
            }
        }
    }
}
