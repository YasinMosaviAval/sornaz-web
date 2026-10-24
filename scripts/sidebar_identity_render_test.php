<?php
namespace Core\database {
    final class DB {
        public static function table(string $name): object {
            if ($name === 'translations') return new class {
                public function __call(string $method, array $args): self { return $this; }
                public function first(): array { return ['value' => 'نام آزمایشی']; }
            };
            if ($name === 'academies') return new class {
                public function __call(string $method, array $args): self { return $this; }
                public function first(): ?array { return ($GLOBALS['sidebarTestType'] ?? '') === 'academy' ? ['academy_id' => 1] : null; }
            };
            return new class {
                public function __call(string $method, array $args): self { return $this; }
                public function first(): ?array { return null; }
            };
        }
    }
}
namespace Modules\System\Services {
    final class SiteAdminAccess { public static function allows(?array $user): bool { return (int) ($user['user_id'] ?? 0) === 1; } }
    final class AccessControl { public function allows(int $userId, string $permission): bool { return false; } }
}
namespace Modules\Academy\Services {
    final class AcademyClassroomService { public const CREATE_TYPE_PERMISSION = 'classroom.create_type'; }
}
namespace {
    function auth(): object { return new class {
        public function user(): array { return ['user_id' => $this->id(), 'type' => $GLOBALS['sidebarTestType'] ?? 'human', 'username' => 'tester']; }
        public function id(): int { return ($GLOBALS['sidebarTestType'] ?? '') === 'admin' ? 1 : 2; }
    }; }
    function locale(): string { return 'fa'; }
    function env(string $key, mixed $default = null): mixed { return $default; }
    function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
    foreach (['human', 'academy', 'admin'] as $type) {
        $GLOBALS['sidebarTestType'] = $type;
        ob_start();
        require dirname(__DIR__) . '/Modules/Analytics/Resources/Views/sections/sidebar.php';
        $html = ob_get_clean();
        foreach (['نام آزمایشی', 'tester', 'id="sidebarAvatar"'] as $expected) {
            if (!str_contains($html, $expected)) throw new \RuntimeException("Missing sidebar identity: $expected");
        }
        if (str_contains($html, 'white_logo_transparent.png')) throw new \RuntimeException('Old site logo remains');
        if ($type !== 'admin' && str_contains($html, 'lessonsSubmenu')) throw new \RuntimeException("$type still has lessons submenu");
        if ($type !== 'admin' && str_contains($html, "showSection('notation')")) throw new \RuntimeException('Non-admin notation menu is visible');
        if ($type === 'admin' && (substr_count($html, "showSection('users')") !== 1 || strpos($html, "showSection('users')") < strpos($html, "showSection('classrooms')") || strpos($html, "showSection('users')") < strpos($html, "showSection('lessons')") || str_contains($html, "showSection('teachers')"))) {
            throw new \RuntimeException('Admin users must open users after lessons/classes without a duplicate menu');
        }
        if ($type === 'admin') {
            foreach (['notation', 'awards', 'tracking', 'national-holidays'] as $section) {
                if (strpos($html, "showSection('$section')") === false || strpos($html, "showSection('$section')") > strpos($html, "showSection('dashboard')")) throw new \RuntimeException('Admin priority menu order is incorrect');
            }
            if (!preg_match('/showSection\(\x27users\x27\).*?کاربران/su', $html)) throw new \RuntimeException('Admin staff label must be Users');
        }
        if ($type === 'academy' && strpos($html, "showSection('classrooms')") > strpos($html, "showSection('teachers')")) throw new \RuntimeException('Academy classrooms must appear before employees');
        if (str_contains($html, "showSection('courses')")) throw new \RuntimeException('Courses still have a standalone sidebar entry');
        if ($type === 'admin' && !str_contains($html, '<button type="button" onclick="showSection(\'lessons\')"')) throw new \RuntimeException('Lesson sidebar entry must open lessons, independent of tab order');
        if ($type === 'admin' && preg_match('/<button[^>]+onclick="showSection\(\x27lessons\x27\)"[^>]*>(.*?)<\/button>/s', $html, $lesson) && str_contains($lesson[1], 'submenu-chevron')) throw new \RuntimeException('Lesson menu must not have an arrow');
        if ($type === 'academy' && (str_contains($html, 'profilesSubmenu') || str_contains($html, "showSection('users')") || !str_contains($html, "showSection('teachers')"))) {
            throw new \RuntimeException('Academy roles must open through employee tabs');
        }
    }
    echo "Sidebar: identity, hidden notation, and academy employee navigation rendered.\n";
}
