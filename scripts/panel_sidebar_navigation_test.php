<?php
// Isolated sidebar/role fixture. No application boot, network or persistent DB.
namespace Core\database {
    final class DB {
        public static array $state = [];
        public static function table(string $table): object { return new class($table) {
            private array $joins = [];
            private bool $manager = false;
            private bool $teacherOnly = false;
            public function __construct(private string $table) {}
            public function __call(string $name, array $args): mixed {
                if ($name === 'join') $this->joins[] = $args[0];
                if ($name === 'whereRaw') $this->manager = true;
                if ($name === 'where' && ($args[0] ?? '') === 'academy_branch_course_term_enrollments.type' && ($args[1] ?? '') === 'teacher') $this->teacherOnly = true;
                if ($name !== 'first') return $this;
                if (in_array('academy_branch_course_term_enrollments', $this->joins, true)) return DB::$state[$this->teacherOnly ? 'teaching-enrollment' : 'enrollment'] ?? null;
                if (in_array('access_system_roles', $this->joins, true)) return DB::$state[$this->manager ? 'academy-manager' : 'management-role'] ?? null;
                if (in_array('academy_branch_member_contracts', $this->joins, true)) return DB::$state['management-contract'] ?? null;
                return DB::$state[$this->table] ?? null;
            }
        }; }
    }
}
namespace Modules\System\Services {
    final class AccessControl { public function allows(int $id, string $permission): bool { return false; } }
}
namespace {
    $map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
    spl_autoload_register(static function (string $class) use ($map): void {
        if (isset($map[$class])) require_once $map[$class];
    });
    require_once __DIR__ . '/../Modules/Analytics/Services/MobilePanelNavigation.php';
    function base_path(string $path = ''): string { return dirname(__DIR__) . '/' . $path; }
    function locale(): string { return 'fa'; }
    function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
    function env(string $key, mixed $default = null): mixed { return $key === 'APP_ENV' ? ($GLOBALS['environment'] ?? 'production') : $default; }
    function auth(): object { return new class {
        public function user(): array { return $GLOBALS['actor']; }
        public function id(): int { return (int) $GLOBALS['actor']['user_id']; }
    }; }
    function expectMenu(bool $ok, string $message): void { if (!$ok) throw new \LogicException($message); }
    require base_path('Modules/Analytics/Routes/routes.php');
    require base_path('Modules/Academy/Routes/web.php');
    $catalog = (new \Modules\Analytics\Services\MobilePanelCatalog())->sections();
    $navigation = new \Modules\Analytics\Services\MobilePanelNavigation();
    $access = new \Modules\Analytics\Services\MobilePanelAccess();
    $personal = ['dashboard', 'account', 'gallery', 'lessons', 'member-schedules', 'finance', 'settings'];
    $academy = ['dashboard', 'account', 'gallery', 'branches', 'lessons', 'classrooms', 'teachers', 'terms', 'students', 'schedules', 'finance', 'settings'];
    $cases = [
        ['ordinary', 'human', [], $personal],
        ['student', 'human', ['academy_branch_members' => ['member_id' => 8], 'enrollment' => ['enrollment_id' => 9]], ['dashboard', 'account', 'my-classrooms', 'my-courses', 'my-terms', 'gallery', 'lessons', 'member-schedules', 'finance', 'settings']],
        ['teacher', 'human', ['academy_branch_members' => ['member_id' => 8], 'enrollment' => ['enrollment_id' => 9], 'teaching-enrollment' => ['enrollment_id' => 9, 'type' => 'teacher']], ['dashboard', 'account', 'schedules', 'my-classrooms', 'my-courses', 'my-terms', 'gallery', 'lessons', 'member-schedules', 'finance', 'settings']],
        ['academy', 'academy', [], $academy],
        ['branch', 'branch', [], array_values(array_diff($academy, ['branches']))],
        ['manager', 'human', ['management-role' => ['role_id' => 3]], $academy],
        ['contract', 'human', ['management-contract' => ['contract_id' => 3]], $academy],
        ['owner-human', 'human', ['academies' => ['academy_id' => 5]], $academy],
    ];
    foreach ($cases as [$name, $type, $state, $expected]) {
        $GLOBALS['actor'] = ['user_id' => 17, 'type' => $type, 'username' => 'fixture'];
        \Core\database\DB::$state = $state;
        $caps = $access->capabilities(auth()->user());
        $allowed = array_filter($catalog, fn ($section) => empty($section['hidden']) && !empty($caps[$section['access']]));
        $items = $navigation->forCurrentUser($allowed, $caps);
        expectMenu(array_column($items, 'key') === $expected, $name . ': website menu differs: ' . json_encode(array_column($items, 'key')));
        expectMenu(!array_intersect(['my-gallery', 'my-lessons', 'my-settings', 'my-finance', 'my-waiting'], array_column($items, 'key')), $name . ': API-only entry leaked');
        $index = array_column($items, null, 'key');
        if (in_array($name, ['academy', 'branch', 'manager', 'contract', 'owner-human'], true)) {
            expectMenu(in_array('waiting-approvals', $index['dashboard']['links'], true), $name . ': approval workflow disappeared from dashboard');
            expectMenu($index['lessons']['links'] === ['course-levels', 'courses'], $name . ': lesson tab destinations disappeared');
            expectMenu($index['teachers']['links'] === ['users', 'member-schedules', 'employee-leaves'], $name . ': employee schedule tabs missing');
            if (isset($index['branches'])) expectMenu($index['branches']['links'] === ['availabilities', 'scheduling-rules', 'organization-holidays'], $name . ': organization schedule tabs missing');
        }
        expectMenu($index['settings']['section'] === 'my-settings', $name . ': non-admin settings must remain personal');
        expectMenu($index['gallery']['section'] === (in_array($name, ['ordinary', 'student', 'teacher'], true) ? 'my-gallery' : 'gallery'), $name . ': gallery points at the wrong owner scope');
    }
    $GLOBALS['actor'] = ['user_id' => 1, 'type' => 'human', 'username' => '<script>fixture</script>'];
    \Core\database\DB::$state = [];
    $caps = $access->capabilities(auth()->user());
    $allowed = array_filter($catalog, fn ($section) => empty($section['hidden']) && !empty($caps[$section['access']]));
    $items = $navigation->forCurrentUser($allowed, $caps);
    $index = array_column($items, null, 'key');
    expectMenu(isset($index['profileAchievementsSubmenu'], $index['profilesSubmenu'], $index['notation']), 'Founder-only sections missing');
    expectMenu(count($index['profileAchievementsSubmenu']['children']) === 8, 'Achievement submenu incomplete');
    expectMenu(count($index['gallerySubmenu']['children']) === 4, 'Gallery categories incomplete');
    expectMenu($index['gallerySubmenu']['children'][2]['initialParams'] === ['collection' => 'intro_video'], 'Gallery category lost');
    expectMenu(!isset($index['reports'], $index['chart-gallery']), 'Local-only website demos leaked in production');
    $GLOBALS['environment'] = 'local';
    $local = array_column($navigation->forCurrentUser($allowed, $caps), null, 'key');
    expectMenu(isset($local['posts']) && !isset($local['post-categories'], $local['tests']), 'Posts must have one menu entry; test center must be absent');
    expectMenu(isset($local['reports'], $local['chart-gallery']), 'Local website destinations silently removed');
    expectMenu($local['chart-gallery']['destination'] === 'website-example', 'Chart demonstration must not pretend to have a native data API');
    $GLOBALS['environment'] = 'production';
    $GLOBALS['actor'] = ['user_id' => 19, 'type' => 'admin', 'username' => 'admin'];
    $caps = $access->capabilities(auth()->user());
    $allowed = array_filter($catalog, fn ($section) => empty($section['hidden']) && !empty($caps[$section['access']]));
    $admin = array_column($navigation->forCurrentUser($allowed, $caps), null, 'key');
    expectMenu(!isset($admin['profileAchievementsSubmenu'], $admin['profilesSubmenu'], $admin['notation']), 'Non-founder received founder-only menu');
    expectMenu($admin['settings']['section'] === 'settings', 'Site administrator settings downgraded');
    echo "Website/sidebar navigation: ordinary, student, academy, branch, manager, contract, owner, founder, site admin and local-only menus passed.\n";
}
