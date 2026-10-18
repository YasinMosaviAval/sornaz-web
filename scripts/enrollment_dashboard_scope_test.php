<?php
namespace Modules\Academy\Services {
    class PublicAcademyEnrollmentService { public function ensureConversation(int $id): int { return 1000 + $id; } }
}
namespace {
    $map = require __DIR__.'/../vendor/composer/autoload_classmap.php';
    spl_autoload_register(static function ($class) use ($map) {
        $path = $map[$class] ?? __DIR__.'/../'.str_replace('\\', '/', $class).'.php';
        if (is_file($path)) require_once $path;
    });
    function db() { return $GLOBALS['pdo']; }
    function locale() { return 'fa'; }
    function check($ok, $label) { if (!$ok) throw new \LogicException($label); }
    $pdo = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE=>\PDO::FETCH_ASSOC]);
    foreach ([
        'users'=>'user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT',
        'academies'=>'academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT',
        'academy_branches'=>'branch_id INTEGER PRIMARY KEY,academy_id INTEGER,user_id INTEGER,deleted_at TEXT',
        'academy_branch_members'=>'member_id INTEGER PRIMARY KEY,academy_id INTEGER,branch_id INTEGER,user_id INTEGER,status TEXT,deleted_at TEXT',
        'academy_branch_member_roles'=>'member_id INTEGER,role_id INTEGER,deleted_at TEXT',
        'access_system_roles'=>'role_id INTEGER PRIMARY KEY,name TEXT,deleted_at TEXT',
        'academy_branch_courses'=>'course_id INTEGER PRIMARY KEY,academy_id INTEGER,branch_id INTEGER,deleted_at TEXT',
        'academy_branch_course_terms'=>'term_id INTEGER PRIMARY KEY,course_id INTEGER,deleted_at TEXT',
        'academy_branch_course_term_waiting_list'=>'term_waiting_list_id INTEGER PRIMARY KEY,term_id INTEGER,deleted_at TEXT',
    ] as $table=>$columns) $pdo->exec('CREATE TABLE '.$table.'('.$columns.')');
    foreach ([10,20,30,40,50,60,70,80] as $id) $pdo->exec("INSERT INTO users VALUES($id,'human',NULL)");
    $pdo->exec('INSERT INTO academies VALUES(100,10,20,NULL),(200,11,21,NULL)');
    $pdo->exec("INSERT INTO academy_branch_members VALUES(1,100,NULL,30,'active',NULL),(2,100,NULL,40,'active',NULL),(3,100,7,50,'active',NULL),(4,100,NULL,60,'pending',NULL),(5,100,NULL,80,'active',NULL)");
    $pdo->exec("INSERT INTO access_system_roles VALUES(1,'academy_manager',NULL),(2,'academy_receptionist',NULL),(3,'branch_manager',NULL)");
    $pdo->exec("INSERT INTO academy_branch_member_roles VALUES(1,1,NULL),(2,2,NULL),(3,3,NULL),(4,1,NULL),(5,1,'2026-01-01')");
    $pdo->exec('INSERT INTO academy_branch_courses VALUES(501,100,NULL,NULL),(502,200,NULL,NULL)');
    $pdo->exec('INSERT INTO academy_branch_course_terms VALUES(101,501,NULL),(102,502,NULL)');
    $pdo->exec('INSERT INTO academy_branch_course_term_waiting_list VALUES(1,101,NULL),(2,102,NULL)');
    $service = new \Modules\Analytics\Services\AdminDashboardService();
    $scope = new \ReflectionMethod($service, 'allowedAcademyIds');
    foreach ([10,20,30,40] as $actor) {
        check($scope->invoke($service,$actor)===[100], 'Owner/creator/staff academy scope lost');
        check($service->ensureWaitingChat($actor,1)['conversationId']===1001, 'Authorized coordination chat unavailable');
        try { $service->ensureWaitingChat($actor,2); throw new \LogicException('Foreign academy accepted'); } catch (\RuntimeException) {}
    }
    foreach ([50,60,70,80] as $actor) {
        check($scope->invoke($service,$actor)===[], 'Branch/pending/deleted/unrelated role gained academy-wide access');
        try { $service->ensureWaitingChat($actor,1); throw new \LogicException('Unauthorized chat accepted'); } catch (\RuntimeException) {}
    }
    echo "Enrollment dashboard: owner, creator, active academy manager/receptionist, foreign academy and branch/pending/deleted role isolation passed.\n";
}
