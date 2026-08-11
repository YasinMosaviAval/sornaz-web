<?php

namespace Core\localization;

use PDO;
use Throwable;

class FrameworkTranslator
{
    private array $loaded = [];
    private array $translations = [];

    public function __construct(private PDO $db)
    {
    }

    public function get(string $key, ?string $fallback = null, array $replace = []): string
    {
        $locale = app()->getLocale();
        $this->load($locale);
        $value = $this->translations[$locale][$key] ?? $fallback ?? $key;

        foreach ($replace as $name => $replacement) {
            $value = str_replace(':' . $name, (string) $replacement, $value);
        }

        return $value;
    }

    public function all(string $prefix = ''): array
    {
        $locale = app()->getLocale();
        $this->load($locale);

        if ($prefix === '') {
            return $this->translations[$locale];
        }

        return array_filter(
            $this->translations[$locale],
            static fn (string $key): bool => str_starts_with($key, $prefix),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function load(string $locale): void
    {
        if (isset($this->loaded[$locale])) {
            return;
        }

        $this->loaded[$locale] = true;
        $this->translations[$locale] = [];

        try {
            $statement = $this->db->prepare(
                "SELECT s.variable_name, t.value
                 FROM f_settings s
                 INNER JOIN f_translations t
                    ON t.table_name = 'f_settings' AND t.table_id = s.setting_id
                 WHERE t.locale = :locale
                   AND s.deleted_at IS NULL AND t.deleted_at IS NULL
                   AND (s.status IS NULL OR s.status = 'active')
                 ORDER BY s.sort_order, s.setting_id"
            );
            $statement->execute(['locale' => $locale]);
            foreach ($statement->fetchAll() as $row) {
                $this->translations[$locale][$row['variable_name']] = (string) $row['value'];
            }
        } catch (Throwable) {
            // Framework translations must never make a page unavailable.
        }
    }
}
