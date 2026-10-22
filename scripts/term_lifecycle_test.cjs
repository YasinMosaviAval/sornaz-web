const {spawnSync}=require('node:child_process');
const base=require('./student_term_schedule_test.cjs');
const extra=String.raw`
$pdo->exec('ALTER TABLE academy_branch_course_terms ADD COLUMN updated_at TEXT');
$pdo->exec('ALTER TABLE academy_branch_course_terms ADD COLUMN approved_at TEXT');
$pdo->exec('ALTER TABLE academy_branch_course_terms ADD COLUMN approved_by INTEGER');
if($mysql){
 $pdo->exec("ALTER TABLE academy_branch_course_terms MODIFY status ENUM('pending','open','ongoing','finished') NOT NULL DEFAULT 'pending'");
 $apply=function()use($pdo){$last=[];$sql=preg_replace('/^--.*$/m','',file_get_contents('docs/database/migrations/2026_10_23_term_cancellation_suspension.sql'));foreach(explode(';',$sql)as$statement){if(trim($statement)==='')continue;$cursor=$pdo->query($statement);if($cursor->columnCount())$last=$cursor->fetchAll();$cursor->closeCursor();}return $last[0]['migration_status']??'';};
 check($apply()==='APPLIED','Lifecycle migration extends the old enum');check($apply()==='ALREADY_APPLIED','Lifecycle migration is repeatable');
}
$lifecycleTerm=$tid;
$pdo->exec("UPDATE academy_branch_course_terms SET status='ongoing' WHERE term_id=".$lifecycleTerm);
$preserved=function()use($pdo,$lifecycleTerm){$result=[];foreach(['academy_branch_course_term_sessions','academy_branch_course_term_enrollments','academy_branch_course_term_invoices']as$table)$result[$table]=$pdo->query('SELECT * FROM '.$table.' WHERE term_id='.$lifecycleTerm)->fetchAll();return $result;};
$originalRecords=$preserved();
$scheduleService->changeStatus(10,$lifecycleTerm,'suspended','ongoing');
check(Core\database\DB::table('academy_branch_course_terms')->where('term_id',$lifecycleTerm)->first()['status']==='suspended','Unfinished term can be suspended');
check($preserved()===$originalRecords,'Suspension preserves sessions, enrollments and financial records');
check(Modules\Academy\Services\TermEnrollmentCapacity::synchronize($lifecycleTerm,10)==='suspended','Capacity synchronization cannot silently resume a suspended term');
$scheduleService->saveAcademyTerm(10,['metadataOnly'=>true,'name'=>'Paused term details'],$lifecycleTerm);
check(Core\database\DB::table('academy_branch_course_terms')->where('term_id',$lifecycleTerm)->first()['status']==='suspended','Metadata editing preserves suspension');
try{$scheduleService->changeStatus(20,$lifecycleTerm,'ongoing');throw new LogicException('Foreign academy changed status');}catch(RuntimeException $e){check($e->getCode()===403,'Foreign academy denied before changing lifecycle');}
try{$scheduleService->changeStatus(10,$lifecycleTerm,'invalid');throw new LogicException('Invalid status accepted');}catch(RuntimeException $e){check($e->getCode()===422,'Unknown lifecycle values rejected');}
try{$scheduleService->changeStatus(10,$lifecycleTerm,'ongoing','open');throw new LogicException('Stale status accepted');}catch(RuntimeException $e){check($e->getCode()===409,'Concurrent stale changes rejected');}
$scheduleService->changeStatus(10,$lifecycleTerm,'ongoing','suspended');
check($preserved()===$originalRecords,'Resuming preserves all original record IDs and financial history');
$scheduleService->changeStatus(10,$lifecycleTerm,'canceled','ongoing');
check(Modules\Academy\Services\TermEnrollmentCapacity::synchronize($lifecycleTerm,10)==='canceled','Canceled terms cannot automatically reactivate');
check($preserved()===$originalRecords,'Cancellation preserves sessions, enrollments and invoices');
check($scheduleService->changeStatus(10,$lifecycleTerm,'canceled','ongoing')['status']==='canceled','Repeated cancellation is idempotent');
try{$scheduleService->cycleStatus(10,$lifecycleTerm);throw new LogicException('Legacy cycle reopened canceled term');}catch(RuntimeException $e){check($e->getCode()===409,'Legacy cycle cannot reopen canceled terms');}
try{$scheduleService->changeStatus(10,$lifecycleTerm,'ongoing');throw new LogicException('Canceled term resumed');}catch(RuntimeException $e){check($e->getCode()===409,'Canceled terms are final');}
$pdo->exec("UPDATE academy_branch_course_terms SET status='finished' WHERE term_id=".$lifecycleTerm);
try{$scheduleService->changeStatus(10,$lifecycleTerm,'suspended');throw new LogicException('Finished term suspended');}catch(RuntimeException $e){check($e->getCode()===409,'Completed terms cannot be suspended');}
echo "Term lifecycle: cancellation/suspension, explicit resume, idempotence, tenant guards, stale requests and unchanged academic/financial records passed.\n";
`;
const result=spawnSync('php',[],{input:base.replace('echo "Student scheduling:',extra+'\necho "Student scheduling:'),encoding:'utf8'});
process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
