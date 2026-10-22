const {spawnSync}=require('node:child_process');
const base=require('./term_student_assignment_test.cjs');
const regression=String.raw`
$wizardId=$result['ids'][5];
$student['phone']='09120000029';$student['nationalId']='1000000029';$student['birthDate']='1990-01-01';
$before=(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_enrollments')->fetchColumn();
denied(fn()=>$service->assignStudent(10,$wizardId,$student+['classroomId'=>2001]));
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_enrollments')->fetchColumn()===$before,'Invalid classroom rolls back student enrollment');
$service->assignStudent(10,$wizardId,$student+['classroomId'=>1001]);
$draft=json_decode($pdo->query("SELECT value FROM translations WHERE field='schedule_draft' AND table_id=".$wizardId." ORDER BY translation_id DESC LIMIT 1")->fetchColumn(),true);
check((int)$draft['classroomId']===1001,'One request saves student and classroom');
echo "Wizard assignment: atomic enrollment/classroom and foreign classroom rollback passed.\n";
`;
const result=spawnSync('php',[],{input:base.replace('echo "Term assignment:',regression+'\necho "Term assignment:'),encoding:'utf8'});
process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
