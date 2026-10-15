<?php

namespace Modules\Analytics\Services;

/** Shared view markup. `html` and attribute fragments must come from trusted view code. */
final class PanelTableRenderer
{
    private static ?array $markup = null;

    private static function template(string $name, array $values): string
    {
        if (self::$markup === null) {
            $source = file_get_contents(__DIR__ . '/../Resources/Views/sections/panel-table-template.php');
            preg_match('~<script type="application/json" id="adminPanelTableMarkup">(.*?)</script>~s', $source, $match);
            self::$markup = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
        }
        $replacements = [];
        foreach ($values as $key => $value) {
            $replacements['{{' . $key . '}}'] = (string) $value;
        }
        return strtr(self::$markup[$name], $replacements);
    }

    private static function cells(array $cells, bool $heading = false): string
    {
        $html = '';
        foreach ($cells as $cell) {
            $cell = is_array($cell) ? $cell : ['value' => $cell];
            $html .= self::template($heading || ($cell['tag'] ?? '') === 'th' ? 'heading' : 'cell', [
                'attributes' => $cell['attributes'] ?? '',
                'html' => $cell['html'] ?? e((string) ($cell['value'] ?? '')),
            ]);
        }
        return $html;
    }

    public static function render(array $config): string
    {
        $rows = '';
        foreach ($config['rows'] ?? [] as $row) {
            $rows .= self::template('row', [
                'attributes' => $row['attributes'] ?? '',
                'cells' => self::cells($row['cells'] ?? []),
            ]);
        }
        return self::template('table', [
            'attributes' => $config['attributes'] ?? 'class="w-full"',
            'headAttributes' => $config['headAttributes'] ?? 'class="border-b bg-gray-50"',
            'headerRowAttributes' => $config['headerRowAttributes'] ?? '',
            'bodyAttributes' => $config['bodyAttributes'] ?? 'class="divide-y text-sm"',
            'columns' => self::cells($config['columns'] ?? [], true),
            'rows' => $rows,
        ]);
    }
}
