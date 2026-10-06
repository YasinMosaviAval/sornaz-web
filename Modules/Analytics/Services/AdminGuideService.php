<?php

namespace Modules\Analytics\Services;

use Core\database\DB;

class AdminGuideService
{
    private array $guides = [
        'user-registration' => ['title' => 'ثبت‌نام کاربر', 'file' => 'USER_REGISTRATION_FLOW.md'],
        'academy-registration' => ['title' => 'ثبت آموزشگاه', 'file' => 'ACADEMY_REGISTRATION_FLOW.md'],
        'main-branch-registration' => ['title' => 'ثبت شعبه اصلی آموزشگاه', 'file' => 'MAIN_BRANCH_REGISTRATION_FLOW.md'],
    ];

    public function documents(): array
    {
        $documents = [];
        foreach (['docs', 'Modules'] as $directory) {
            $root = base_path($directory);
            if (!is_dir($root)) {
                continue;
            }
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
            foreach ($files as $file) {
                if (!$file->isFile() || strtolower($file->getExtension()) !== 'md') {
                    continue;
                }
                $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
                if ($directory === 'Modules' && str_contains($relative, '/Lib/')) {
                    continue;
                }
                $content = (string) file_get_contents($file->getPathname());
                preg_match('/^#\s+(.+)$/m', $content, $heading);
                $documents[] = [
                    'path' => $directory . '/' . $relative,
                    'title' => trim($heading[1] ?? pathinfo($file->getFilename(), PATHINFO_FILENAME)),
                    'content' => $content,
                    'html' => $this->renderMarkdown($content),
                ];
            }
        }
        usort($documents, static fn (array $a, array $b): int => strcmp($a['path'], $b['path']));
        return $documents;
    }

    private function renderMarkdown(string $markdown): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown) ?: [];
        $html = [];
        $list = '';
        $code = false;
        $table = false;
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '```')) {
                $this->closeMarkdownBlocks($html, $list, $table);
                $html[] = $code ? '</code></pre>' : '<pre><code>';
                $code = !$code;
                continue;
            }
            if ($code) {
                $html[] = htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
                continue;
            }
            if ($trimmed === '') {
                $this->closeMarkdownBlocks($html, $list, $table);
                continue;
            }
            if (preg_match('/^(#{1,6})\s+(.+)$/u', $trimmed, $heading)) {
                $this->closeMarkdownBlocks($html, $list, $table);
                $level = strlen($heading[1]);
                $html[] = "<h{$level}>" . $this->renderMarkdownInline($heading[2]) . "</h{$level}>";
                continue;
            }
            if (preg_match('/^(?:---+|\*\*\*+)$/', $trimmed)) {
                $this->closeMarkdownBlocks($html, $list, $table);
                $html[] = '<hr>';
                continue;
            }
            if (str_starts_with($trimmed, '> ')) {
                $this->closeMarkdownBlocks($html, $list, $table);
                $html[] = '<blockquote>' . $this->renderMarkdownInline(substr($trimmed, 2)) . '</blockquote>';
                continue;
            }
            if (preg_match('/^(?:[-*+]\s+|\d+[.)]\s+)(.*)$/u', $trimmed, $item)) {
                $ordered = preg_match('/^\d+[.)]\s+/', $trimmed) === 1;
                $wanted = $ordered ? 'ol' : 'ul';
                if ($list !== $wanted) {
                    $this->closeMarkdownBlocks($html, $list, $table);
                    $html[] = '<' . $wanted . '>';
                    $list = $wanted;
                }
                $html[] = '<li>' . $this->renderMarkdownInline($item[1]) . '</li>';
                continue;
            }
            if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|')) {
                $cells = array_map('trim', explode('|', trim($trimmed, '|')));
                if (count(array_filter($cells, static fn (string $cell): bool => preg_match('/^:?-{3,}:?$/', $cell) === 1)) === count($cells)) {
                    continue;
                }
                if (!$table) {
                    $this->closeMarkdownBlocks($html, $list, $table);
                    $html[] = '<div class="guide-md-table-wrap"><table>';
                    $table = true;
                }
                $html[] = '<tr>' . implode('', array_map(fn (string $cell): string => '<td>' . $this->renderMarkdownInline($cell) . '</td>', $cells)) . '</tr>';
                continue;
            }
            $this->closeMarkdownBlocks($html, $list, $table);
            $html[] = '<p>' . $this->renderMarkdownInline($trimmed) . '</p>';
        }
        $this->closeMarkdownBlocks($html, $list, $table);
        if ($code) {
            $html[] = '</code></pre>';
        }
        return implode("\n", $html);
    }

    private function closeMarkdownBlocks(array &$html, string &$list, bool &$table): void
    {
        if ($list !== '') {
            $html[] = '</' . $list . '>';
            $list = '';
        }
        if ($table) {
            $html[] = '</table></div>';
            $table = false;
        }
    }

    private function renderMarkdownInline(string $text): string
    {
        $parts = preg_split('/(`[^`]+`|\[[^\]]+\]\([^)]+\))/', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $html = '';
        foreach ($parts as $part) {
            if (preg_match('/^`([^`]+)`$/s', $part, $code)) {
                $html .= '<code>' . htmlspecialchars($code[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code>';
            } elseif (preg_match('/^\[([^\]]+)\]\(([^)]+)\)$/s', $part, $link)) {
                $target = trim($link[2]);
                $safe = preg_match('~^https?://~i', $target) || (str_starts_with($target, '/') && !str_starts_with($target, '//'));
                $label = htmlspecialchars($link[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html .= $safe ? '<a href="' . htmlspecialchars($target, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . $label . '</a>' : $label;
            } else {
                $escaped = htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $escaped = preg_replace('/\*\*(.+?)\*\*/us', '<strong>$1</strong>', $escaped);
                $html .= preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/us', '<em>$1</em>', $escaped);
            }
        }
        return $html;
    }

    public function all(string $locale = 'fa'): array
    {
        $this->seedMissing();
        $out = [];
        foreach ($this->guides as $key => $meta) {
            $setting = DB::table('f_settings')->where('variable_name', 'admin.guide.' . $key)->whereNull('deleted_at')->first();
            if (!$setting) {
                continue;
            }
            $rows = DB::table('f_translations')->where('table_name', 'f_settings')->where('table_id', (int) $setting['setting_id'])->whereNull('deleted_at')->get();
            $text = [];
            foreach ($rows as $row) {
                $text[$row['locale']][$row['field']] = $row['value'];
            }
            $chosen = $text[$locale] ?? $text['fa'] ?? [];
            $out[] = ['key' => $key, 'title' => $chosen['title'] ?? $meta['title'], 'content' => $chosen['content'] ?? '', 'fa' => $text['fa'] ?? [], 'en' => $text['en'] ?? []];
        }
        return $out;
    }

    public function save(int $actor, string $key, array $data): void
    {
        if (!isset($this->guides[$key])) {
            throw new \RuntimeException('راهنمای موردنظر یافت نشد.');
        }
        $setting = DB::table('f_settings')->where('variable_name', 'admin.guide.' . $key)->first();
        if (!$setting) {
            $id = (int) DB::table('f_settings')->insertGetId(['variable_name' => 'admin.guide.' . $key, 'page' => 'admin', 'table_name' => 'admin_guides', 'status' => 'active', 'created_by' => $actor, 'updated_by' => $actor]);
        } else {
            $id = (int) $setting['setting_id'];
        }
        foreach (['fa', 'en'] as $locale) {
            foreach (['title', 'content'] as $field) {
                $value = trim((string) ($data[$locale][$field] ?? ''));
                if ($value === '') {
                    throw new \RuntimeException('عنوان و متن هر دو زبان الزامی است.');
                }
                $row = DB::table('f_translations')->where('table_name', 'f_settings')->where('table_id', $id)->where('locale', $locale)->where('field', $field)->first();
                $v = ['value' => $value, 'version' => 1, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
                if ($row) {
                    DB::table('f_translations')->where('translation_id', (int) $row['translation_id'])->update($v);
                } else {
                    DB::table('f_translations')->insert(['table_name' => 'f_settings', 'table_id' => $id, 'field' => $field, 'locale' => $locale, 'created_by' => $actor] + $v);
                }
            }
        }
    }

    private function seedMissing(): void
    {
        foreach ($this->guides as $key => $meta) {
            $keyName = 'admin.guide.' . $key;
            $setting = DB::table('f_settings')->where('variable_name', $keyName)->first();
            $path = base_path('docs/' . $meta['file']);
            $content = is_file($path) ? (string) file_get_contents($path) : '';
            if (!$setting) {
                $id = (int) DB::table('f_settings')->insertGetId(['variable_name' => $keyName, 'page' => 'admin', 'table_name' => 'admin_guides', 'status' => 'active', 'created_by' => 1, 'updated_by' => 1]);
            } else {
                $id = (int) $setting['setting_id'];
            }
            foreach (['fa' => [$meta['title'], $content], 'en' => [$meta['title'], $content]] as $locale => $values) {
                foreach (['title', 'content'] as $i => $field) {
                    $row = DB::table('f_translations')->where('table_name', 'f_settings')->where('table_id', $id)->where('locale', $locale)->where('field', $field)->first();
                    if (!$row) {
                        DB::table('f_translations')->insert(['table_name' => 'f_settings', 'table_id' => $id, 'field' => $field, 'locale' => $locale, 'value' => $values[$i], 'version' => 1, 'created_by' => 1, 'updated_by' => 1]);
                    }
                }
            }
        }
    }
}
