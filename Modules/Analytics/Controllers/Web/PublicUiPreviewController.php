<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\MobilePanelCatalog;

final class PublicUiPreviewController
{
    public function __construct(private MobilePanelCatalog $catalog)
    {
    }

    public function index()
    {
        $sections = [];
        foreach ($this->catalog->sections() as $section) {
            $actions = [];
            foreach ($section['actions'] as $key => $action) {
                $actions[] = [
                    'key' => $key,
                    'label' => $action['label'],
                    'fields' => $this->previewFields($action['fields'] ?? []),
                ];
            }
            $sections[] = [
                'key' => $section['key'],
                'label' => $section['label'],
                'access' => $section['access'],
                'fields' => $this->previewFields($section['fields'] ?? []),
                'actions' => $actions,
            ];
        }

        // This route is intentionally public. Only labels and form structure are
        // passed to the view; no account, tenant, route path or database row is read.
        header('X-Robots-Tag: noindex, nofollow');
        $role = (string) ($_GET['role'] ?? 'admin');
        if (!in_array($role, ['admin', 'academy', 'branch', 'user'], true)) $role = 'admin';
        return ResponseFactory::view('Analytics::public-ui-preview', ['previewSections' => $sections, 'previewRole' => $role])
            ->layout('main')->title('نمایش عمومی بخش‌های غیرعمومی | سُرناز');
    }

    private function previewFields(array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $options = $field['options'] ?? null;
            $result[] = [
                'key' => (string) ($field['key'] ?? ''),
                'label' => (string) ($field['label'] ?? ''),
                'type' => (string) ($field['type'] ?? 'text'),
                'required' => (bool) ($field['required'] ?? false),
                'options' => is_array($options) && !isset($options['source']) ? $this->previewOptions($options) : [],
            ];
        }
        return $result;
    }

    private function previewOptions(array $options): array
    {
        $labels = [];
        foreach ($options as $option) {
            if (is_string($option) || is_numeric($option)) {
                $labels[] = (string) $option;
            }
        }
        return array_slice($labels, 0, 12);
    }
}
