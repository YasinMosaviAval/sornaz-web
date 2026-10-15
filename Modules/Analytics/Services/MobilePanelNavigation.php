<?php
namespace Modules\Analytics\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;

/** The authenticated website sidebar is the menu authority, not the API catalog. */
final class MobilePanelNavigation
{
    public function forCurrentUser(array $sections, array $capabilities): array
    {
        $level = ob_get_level();
        ob_start();
        try {
            require base_path('Modules/Analytics/Resources/Views/sections/sidebar.php');
            $html = ob_get_clean();
        } catch (\Throwable $error) {
            while (ob_get_level() > $level) ob_end_clean();
            throw $error;
        }
        $capabilities['websiteManagement'] = (bool) $hasAcademyPanelAccess;
        return $this->fromSidebar($html, $sections, $capabilities);
    }

    public function fromSidebar(string $html, array $sections, array $capabilities): array
    {
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $xpath = new DOMXPath($dom);
        $list = $xpath->query('//*[@id="sidebar"]/nav/ul')->item(0);
        if (!$list instanceof DOMElement) throw new \LogicException('Website sidebar navigation is missing.');
        return $this->items($xpath, $list, $sections, $capabilities);
    }

    private function items(DOMXPath $xpath, DOMElement $list, array $sections, array $capabilities): array
    {
        $result = [];
        foreach ($xpath->query('./li', $list) as $li) {
            $control = $xpath->query('./a|./button', $li)->item(0);
            if (!$control instanceof DOMElement) continue;
            $label = trim(preg_replace('/\s+/u', ' ', $control->textContent));
            $nested = $xpath->query('./ul', $li)->item(0);
            if ($nested instanceof DOMElement) {
                $children = $this->items($xpath, $nested, $sections, $capabilities);
                if ($children !== []) $result[] = ['key' => $nested->getAttribute('id'), 'label' => $label,
                    'en' => $this->groupEnglish($nested->getAttribute('id')), 'children' => $children];
                continue;
            }
            if (!preg_match('/showSection\(\s*[\'"]([a-z0-9-]+)[\'"]\s*\)/', $control->getAttribute('onclick'), $match)) continue;
            $item = $this->destination($match[1], $label, $sections, $capabilities);
            if ($item !== null) $result[] = $item;
        }
        return $result;
    }

    private function destination(string $webKey, string $label, array $sections, array $capabilities): ?array
    {
        if ($webKey === 'notation') return !empty($capabilities['founder'])
            ? ['key' => 'notation', 'destination' => 'notation', 'label' => $label, 'en' => 'Notation'] : null;
        if ($webKey === 'chart-gallery') return ['key' => $webKey, 'destination' => 'website-example',
            'label' => $label, 'en' => 'Chart gallery'];
        $key = ['teachers' => 'members', 'gallery-cover' => 'gallery', 'gallery-logo' => 'gallery',
            'gallery-intro-video' => 'gallery', 'gallery-collection' => 'gallery'][$webKey] ?? $webKey;
        if (empty($capabilities['websiteManagement'] ?? $capabilities['management'])) {
            $key = ['gallery' => 'my-gallery', 'lessons' => 'my-lessons', 'member-schedules' => 'my-schedule',
                'finance' => 'my-finance'][$key] ?? $key;
        }
        if ($key === 'settings' && empty($capabilities['admin'])) $key = 'my-settings';
        if (!isset($sections[$key]) || !empty($sections[$key]['hidden'])) return null;
        $item = ['key' => $webKey, 'section' => $key, 'label' => $label, 'en' => $sections[$key]['en']];
        if ($webKey === 'dashboard') {
            $item['links'] = $this->dashboardLinks($sections, !empty($capabilities['websiteManagement'] ?? $capabilities['management']));
        }
        if ($webKey === 'lessons' && $key === 'lessons') {
            // Courses moved from the sidebar into the shared lesson page tabs.
            $item['links'] = array_values(array_filter(['course-levels', 'courses'],
                fn ($link) => isset($sections[$link]) && empty($sections[$link]['hidden'])));
        }
        if ($webKey === 'lessons' && $key === 'lessons') {
            // Courses moved from the sidebar into the shared lesson page tabs.
            $item['links'] = array_values(array_filter(['course-levels', 'courses'],
                fn ($link) => isset($sections[$link]) && empty($sections[$link]['hidden'])));
        }
        $english = ['account' => 'Account', 'settings' => 'Settings', 'teachers' => 'Staff',
            'gallery' => 'Gallery', 'lessons' => 'Lessons', 'member-schedules' => 'Schedule', 'finance' => 'Finance'];
        $item['en'] = $english[$webKey] ?? $item['en'];
        $category = ['gallery-cover' => 'cover', 'gallery-logo' => 'logo', 'gallery-intro-video' => 'intro_video',
            'gallery-collection' => 'gallery'][$webKey] ?? null;
        if ($category !== null) {
            $item['where'] = ['category' => $category];
            $item['initialParams'] = ['collection' => $category];
            $item['en'] = ['cover' => 'Cover', 'logo' => 'Logo', 'intro_video' => 'Introduction video', 'gallery' => 'Photos and videos'][$category];
        }
        return $item;
    }

    private function groupEnglish(string $id): string
    {
        return ['gallerySubmenu' => 'Gallery', 'profileAchievementsSubmenu' => 'Background and achievements',
            'branchesSubmenu' => 'Branches', 'profilesSubmenu' => 'Roles and permissions',
            'lessonsSubmenu' => 'Lessons', 'classroomsSubmenu' => 'Classes', 'scheduleSubmenu' => 'Schedule'][$id] ?? 'Sections';
    }

    private function dashboardLinks(array $sections, bool $management): array
    {
        $links = $management
            ? ['waiting-approvals', 'waiting-schedules', 'schedules', 'finance', 'reports', 'students', 'messages', 'availability-exceptions']
            : ['my-classrooms', 'my-courses', 'my-terms', 'my-waiting'];
        return array_values(array_filter($links, fn ($link) => isset($sections[$link])));
    }
}
