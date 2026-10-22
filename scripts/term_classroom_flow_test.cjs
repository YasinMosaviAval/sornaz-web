const {spawnSync} = require('node:child_process');
const base = require('./term_student_assignment_test.cjs');
const regression = String.raw`
$unscheduled=$result['ids'][5];
$student['phone']='09120000009';$student['nationalId']='1000000009';$student['birthDate']='1990-01-01';
$registered=$service->assignStudent(10,$unscheduled,$student);
check($pdo->query('SELECT status FROM academy_branch_course_terms WHERE term_id='.$unscheduled)->fetchColumn()==='ongoing','Enrollment activates administrative term status without a classroom');
check(!(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_sessions WHERE term_id='.$unscheduled)->fetchColumn(),'Skipping classroom creates no calendar sessions');
$service->assignDraftClassroom(10,$unscheduled,1001);
$draft=json_decode($pdo->query("SELECT value FROM translations WHERE field='schedule_draft' AND table_id=".$unscheduled." ORDER BY translation_id DESC LIMIT 1")->fetchColumn(),true);
check((int)$draft['classroomId']===1001,'Classroom can be assigned after enrollment changed the status to ongoing');
denied(fn()=>$service->assignDraftClassroom(20,$unscheduled,2001));
echo "Classroom flow: deferred room, ongoing assignment and tenant guard passed.\n";
`;
const source = base.replace('echo "Term assignment:', regression + '\necho "Term assignment:');
const result = spawnSync('php', [], {input: source, encoding: 'utf8'});
process.stdout.write(result.stdout || '');
process.stderr.write(result.stderr || '');
process.exitCode = result.status ?? 1;
