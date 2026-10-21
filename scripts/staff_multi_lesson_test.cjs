const {spawnSync}=require('node:child_process');
const base=require('./staff_registration_test.cjs');
const extra=String.raw`
if(!$mysql){$pdo->exec('UPDATE academy_branch_member_contracts SET member_contract_id=rowid WHERE member_contract_id IS NULL');$pdo->exec('CREATE TRIGGER fill_staff_contract_id AFTER INSERT ON academy_branch_member_contracts WHEN NEW.member_contract_id IS NULL BEGIN UPDATE academy_branch_member_contracts SET member_contract_id=NEW.rowid WHERE rowid=NEW.rowid; END');}
if($mysql)$pdo->exec(file_get_contents('docs/database/migrations/2026_10_19_staff_teaching_lessons.sql'));
else $pdo->exec('CREATE TABLE p_academy_branch_member_contract_lessons(member_contract_id INTEGER,user_lesson_id INTEGER,PRIMARY KEY(member_contract_id,user_lesson_id))');
$pdo->exec('CREATE TABLE lessons(lesson_id INTEGER PRIMARY KEY,deleted_at TEXT)'.($mysql?' ENGINE=InnoDB':''));
$pdo->exec('INSERT INTO lessons(lesson_id) VALUES(5),(6),(7)');
$pdo->exec("INSERT INTO levels(level_id,type,is_active,sort_order) VALUES(3,'learning',1,1),(4,'learning',1,2)");
$pdo->exec("INSERT INTO user_lessons(user_id,lesson_id,level_id,status) VALUES(10,5,3,'active'),(10,6,4,'active')");
$pdo->exec("INSERT INTO access_system_roles(role_id,name,type,scope,sort_order,is_panel_visible) VALUES(42,'academy_teacher_1','academy','academy',3,1)");
$existingManager=$saved;$managerContract=$pdo->query('SELECT * FROM academy_branch_member_contracts WHERE member_contract_id='.(int)$saved['member_contract_id'])->fetch();
$selection=(new Modules\Academy\Services\ExistingMemberUserService())->find(10,10,'fixture_owner_created')['users'][0];
$teacher=$staff;$teacher['existingUserToken']=$selection['token'];$teacher['type']='teacher';$teacher['roleId']=42;$teacher['lessons']=[['type'=>5,'level'=>3],['type'=>6,'level'=>4]];unset($teacher['registration']);
$before=$counts();$added=$branchService->storeMember(10,$teacher,false,true);
check($added['member_id']===$existingManager['member_id'] && $added['user_id']===$existingManager['user_id'],'Manager reused as teacher with the same account and membership');
check($counts()[0]===$before[0] && $counts()[1]===$before[1] && $counts()[2]===$before[2]+1,'Only one new teaching contract, no duplicate salary/account/membership');
check(count((new Modules\Academy\Services\StaffTeachingLessons())->forContract($added['member_contract_id']))===2,'Two teaching lessons on one contract');
check(Modules\Academy\Services\StaffTeachingLessons::teaches($added['member_id'],5)&&Modules\Academy\Services\StaffTeachingLessons::teaches($added['member_id'],6),'Both lessons eligible for term scheduling');
$validatePeople=new ReflectionMethod(Modules\Academy\Services\AcademyTermService::class,'validatePeople');
check($validatePeople->invoke(new Modules\Academy\Services\AcademyTermService(),[$added['member_id']],'teacher',0,6,1,100)===[$added['member_id']],'Term enrollment accepts the secondary teaching lesson');
$secondary=Core\database\DB::table('user_lessons')->where('user_id',10)->where('lesson_id',6)->first();check((new Modules\Academy\Services\LessonUsageService())->hasDependencies($secondary),'Secondary lesson participates in contract deletion protection');
$pdo->exec('UPDATE academy_branch_member_contracts SET approved_at=NULL,created_by=30 WHERE member_contract_id='.$added['member_contract_id']);
check(!Modules\Academy\Services\StaffTeachingLessons::teaches($added['member_id'],6),'Pending second role does not grant teaching eligibility');
$pdo->exec("UPDATE academy_branch_member_contracts SET approved_at='2030-01-01',created_by=10 WHERE member_contract_id=".$added['member_contract_id']);
if($mysql){$pdo->exec(file_get_contents('docs/database/migrations/2026_10_19_staff_teaching_lessons.sql'));check(count((new Modules\Academy\Services\StaffTeachingLessons())->forContract($added['member_contract_id']))===2,'Rerunning additive migration retains all lesson links');}
$roles=$pdo->query('SELECT role_id FROM academy_branch_member_roles WHERE member_id='.$added['member_id'])->fetchAll(PDO::FETCH_COLUMN);
check(in_array(40,array_map('intval',$roles),true)&&in_array(42,array_map('intval',$roles),true),'Manager and teacher roles retained');
$before=$counts();denied(fn()=>$branchService->storeMember(10,$teacher,false,true));check($counts()===$before,'Replay does not duplicate teaching contract');
$bad=$teacher;$bad['lessons']=[['type'=>5,'level'=>4]];denied(fn()=>$branchService->prepareMember(10,$bad));
$bad['lessons']=[['type'=>5,'level'=>3],['type'=>5,'level'=>3]];denied(fn()=>$branchService->prepareMember(10,$bad));
$bad['lessons']=[['type'=>7,'level'=>3]];denied(fn()=>$branchService->prepareMember(10,$bad));
$user=$pdo->query('SELECT * FROM users WHERE user_id='.$added['user_id'])->fetch();
$edit=$teacher+['contractId'=>$added['member_contract_id']];$edit['name']='Existing manager';$edit['phone']=$user['phone'];$edit['nationalId']=$user['national_code'];$edit['birthDate']=$user['birthday'];$edit['gender']=$user['gender'];$edit['lessons']=[['type'=>6,'level'=>4]];
$branchService->updateMember(10,$added['member_id'],$edit);
check(count((new Modules\Academy\Services\StaffTeachingLessons())->forContract($added['member_contract_id']))===1,'Edit replaces teaching links without deleting user lessons');
check($pdo->query('SELECT type FROM academy_branch_member_contracts WHERE member_contract_id='.(int)$managerContract['member_contract_id'])->fetchColumn()==='manager','Editing teacher preserves manager contract');
denied(fn()=>$branchService->updateMember(20,$added['member_id'],$edit));
echo "Staff multiple lessons: shared membership, two roles, one contract amount, lesson eligibility, invalid/duplicate levels, replay, edit and tenant checks passed.\n";
`;
const source=base.replace('echo "Staff signup:',extra+'\necho "Staff signup:');
module.exports=source;
if(require.main===module){const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;}
