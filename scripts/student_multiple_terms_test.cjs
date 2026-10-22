const {spawnSync}=require('node:child_process');
const base=require('./student_term_schedule_test.cjs');
const extra=String.raw`
$pdo->exec('CREATE TABLE academy_branch_course_term_waiting_list(waiting_id '.$auto.',term_id INTEGER,member_id INTEGER,deleted_at TEXT)'.($mysql?' ENGINE=InnoDB':''));
$uid=(int)$savedStudent['user_id'];$mid=(int)$savedStudent['member_id'];
$picker=new Modules\Academy\Services\ExistingMemberUserService();
$selection=$picker->find(10,10,$registration['phone'])['users'][0];
$repeat=['existingUserToken'=>$selection['token'],'addresses'=>[]];
$memberCount=(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_members')->fetchColumn();
$roleCount=(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_member_roles WHERE member_id='.$mid)->fetchColumn();
$lessonCount=(int)$pdo->query('SELECT COUNT(*) FROM user_lessons WHERE user_id='.$uid)->fetchColumn();
$first=$result['ids'][8];$second=$result['ids'][9];$third=$result['ids'][10];
$enrolled=$scheduleService->assignStudent(10,$first,$repeat);
check((int)$enrolled['member_id']===$mid,'Second term reuses the original membership');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_members')->fetchColumn()===$memberCount,'Second term does not duplicate membership');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_member_roles WHERE member_id='.$mid)->fetchColumn()===$roleCount,'Second term does not duplicate student role');
check((int)$pdo->query('SELECT COUNT(*) FROM user_lessons WHERE user_id='.$uid)->fetchColumn()===$lessonCount,'Second term does not duplicate lesson');
$addSession=function($term,$start,$end)use($pdo){$booking=Core\database\DB::table('academy_branch_bookings')->insertGetId(['type'=>'group-class','status'=>'approved','requested_date'=>'2030-04-06','start_time'=>$start,'end_time'=>$end,'timezone_id'=>1]);Core\database\DB::table('academy_branch_course_term_sessions')->insert(['term_id'=>$term,'booking_id'=>$booking,'classroom_id'=>0]);return $booking;};
$addSession($first,'09:00','09:30');$clash=$addSession($second,'09:15','09:45');
// Isolate the student's conflict from classroom and teacher conflicts.
$pdo->exec("UPDATE academy_branch_course_term_enrollments SET deleted_at='fixture' WHERE term_id=".$second." AND type='teacher'");
$invoices=(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoices')->fetchColumn();
try{$scheduleService->assignStudent(10,$second,$repeat);throw new LogicException('Student overlap accepted');}catch(RuntimeException $failure){check($failure->getCode()===409,'Overlapping term is rejected by scheduling guard');}
check(!(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_enrollments WHERE term_id='.$second." AND type='student'")->fetchColumn(),'Overlapping enrollment rolls back');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoices')->fetchColumn()===$invoices,'Overlap rolls back invoice');
$pdo->exec("UPDATE academy_branch_bookings SET start_time='09:30',end_time='10:00' WHERE booking_id=".$clash);
$next=$scheduleService->assignStudent(10,$second,$repeat);check((int)$next['member_id']===$mid,'Adjacent sessions are allowed');
$next=$scheduleService->assignStudent(10,$third,$repeat);check((int)$next['member_id']===$mid,'Further terms have no per-student enrollment count limit');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_members')->fetchColumn()===$memberCount,'Multiple terms keep one membership');
$before=(int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoices')->fetchColumn();
denied(fn()=>$scheduleService->assignStudent(10,$third,$repeat));check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoices')->fetchColumn()===$before,'Same-term duplicate creates no invoice');
denied(fn()=>$scheduleService->assignStudent(20,$result['ids'][11],$repeat));
$forged=$repeat;$forged['existingUserToken']='invalid';denied(fn()=>$scheduleService->assignStudent(10,$result['ids'][11],$forged));
echo "Student multiple terms: membership/role/lesson reuse, repeated enrollments, student-only overlap, adjacent sessions, invoice rollback, duplicate and tenant/token guards passed.\n";
`;
const result=spawnSync('php',[],{input:base.replace('echo "Student scheduling:',extra+'\necho "Student scheduling:'),encoding:'utf8'});
process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
