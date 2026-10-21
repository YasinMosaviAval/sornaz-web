const {spawnSync}=require('node:child_process');
let base=require('./term_student_assignment_test.cjs');
base=base.replace("function app(){return new class{function getLocale(){return 'fa';}};}","function app(){return new class{function getLocale(){return 'fa';}function container(){return $this;}function make($class){return $GLOBALS['staffRegistration'];}};}");
base=base.replace("function session(){return new class{function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}","function session(){return new class{function get($key,$default=null){return $key==='suppress_database_notifications'?true:($GLOBALS['fixtureSession'][$key]??$default);}function put($key,$value){$GLOBALS['fixtureSession'][$key]=$value;}function forget($key){unset($GLOBALS['fixtureSession'][$key]);}};}");
const extra=String.raw`
function auth(){return new class{function id(){return $GLOBALS['registrationActor']??null;}function user(){return ['user_id'=>$this->id()];}};}
function env($key,$default=null){return $default;}function trans($key,$fallback=''){return $fallback;}
if(!$mysql){$pdo->exec("ATTACH DATABASE ':memory:' AS information_schema");$pdo->exec('CREATE TABLE information_schema.COLUMNS(TABLE_SCHEMA TEXT,TABLE_NAME TEXT,COLUMN_NAME TEXT)');$pdo->sqliteCreateFunction('DATABASE',fn()=>'fixture');}
$columns=['users'=>'email VARCHAR(100),password VARCHAR(255),phone_verified_at TEXT','access_system_roles'=>'scope VARCHAR(20),sort_order INTEGER,color VARCHAR(20),is_panel_visible INTEGER','academy_branch_member_contracts'=>'start_date DATE,end_date DATE,price INTEGER,currency_id INTEGER,created_by INTEGER,updated_by INTEGER,'.$audit];
foreach($columns as$table=>$list)foreach(explode(',',$list)as$column)$pdo->exec('ALTER TABLE '.$table.' ADD COLUMN '.$column);
// The base fixture used a keyless contract table because its teacher checks only read it.
if($mysql)$pdo->exec('ALTER TABLE academy_branch_member_contracts ADD member_contract_id INTEGER AUTO_INCREMENT PRIMARY KEY');
else $pdo->exec('ALTER TABLE academy_branch_member_contracts ADD member_contract_id INTEGER');
$tables=[
'levels'=>"level_id $auto,type VARCHAR(20),is_active INTEGER,sort_order INTEGER,deleted_at TEXT",
'f_translations'=>"translation_id $auto,table_name VARCHAR(100),table_id INTEGER,field VARCHAR(100),locale VARCHAR(10),value TEXT,version INTEGER,deleted_at TEXT,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER",
'financial_system_accounts'=>"financial_account_id $auto,user_id INTEGER,type VARCHAR(30),balance INTEGER,status VARCHAR(20)",
'user_referrals'=>"user_referral_id $auto,user_id INTEGER,invite_code VARCHAR(100),referred_by_user_id INTEGER,status VARCHAR(20),created_by INTEGER,updated_by INTEGER,deleted_at TEXT",
'access_system_permissions'=>"permission_id $auto,name VARCHAR(100),deleted_at TEXT",
'access_system_role_permissions'=>"role_id INTEGER,permission_id INTEGER,deleted_at TEXT",
'user_permissions'=>"user_id INTEGER,permission_id INTEGER,expires_at TEXT,deleted_at TEXT"
];foreach($tables as$table=>$list)$pdo->exec('CREATE TABLE '.$table.'('.$list.')'.($mysql?' ENGINE=InnoDB':''));
$pdo->exec('ALTER TABLE user_roles ADD expires_at TEXT');$pdo->exec('ALTER TABLE user_roles ADD deleted_at TEXT');
$pdo->exec("INSERT INTO access_system_roles(role_id,name,type,scope,sort_order,is_panel_visible) VALUES(40,'academy_manager_1','academy','academy',1,1),(41,'academy_receptionist_1','academy','academy',2,1)");
$registrationActor=10;
$mail=new class extends Modules\System\Services\MailService {public string $code='';public function sendRegistrationOtp(string $email,string $code,int $minutes):bool{$this->code=$code;return true;}};
$sms=new class extends Modules\System\Services\SmsService {public string $code='';public function sendRegistrationOtp(string $phone,string $code,int $minutes):bool{$this->code=$code;return true;}};
$otp=new Modules\System\Services\RegistrationOtpService($mail,$sms);
$staffRegistration=new Modules\Academy\Services\StaffRegistrationService(new Modules\System\Services\UserService(new Modules\System\Repositories\UserRepository(),new Modules\System\Services\UserReferralService()),$otp);
$branchService=new Modules\Academy\Services\AcademyBranchService();
$staff=['name'=>'Manager fixture','phone'=>'09123330001','nationalId'=>'2000000001','birthDate'=>'1990-01-01','gender'=>'other','type'=>'manager','roleId'=>40,'organizationUserId'=>10,'contractTitle'=>'Fixture','contractDescription'=>'Fixture','startDate'=>'2030-01-01','endDate'=>'2031-01-01','price'=>100000,'currencyId'=>1];
$counts=fn()=>array_map(fn($t)=>(int)$pdo->query('SELECT COUNT(*) FROM '.$t)->fetchColumn(),['users','academy_branch_members','academy_branch_member_contracts','financial_system_accounts','user_referrals']);$before=$counts();
$branchService->prepareMember(10,$staff);check($counts()===$before,'Opening signup creates no rows');
denied(fn()=>$branchService->storeMember(10,$staff,false,true));check($counts()===$before,'Missing registration creates no employee');
$staff['registration']=['username'=>'fixture_manager','password'=>'StrongPass123!','password2'=>'StrongPass123!','register_method'=>'phone','terms'=>'1'];
denied(fn()=>$branchService->storeMember(10,$staff,false,true));check($counts()===$before,'Missing OTP creates no employee');
$sent=$staffRegistration->send(10,$staff);check($sent['ok']&&$sms->code!=='','OTP delivered through existing service');check($counts()===$before,'OTP creates no rows');
$staff['registration']['otp']='000000';denied(fn()=>$branchService->storeMember(10,$staff,false,true));check($counts()===$before,'Wrong OTP rolls back');
$staff['registration']['otp']=$sms->code;$changed=$staff;$changed['organizationUserId']=20;denied(fn()=>$branchService->storeMember(10,$changed,false,true));check($counts()===$before,'Foreign organization denied');
$changed=$staff;$changed['contractTitle']='Changed';denied(fn()=>$branchService->storeMember(10,$changed,false,true));check($counts()===$before,'OTP bound to contract');
$saved=$branchService->storeMember(10,$staff,false,true);$user=$pdo->query('SELECT * FROM users WHERE user_id='.$saved['user_id'])->fetch();
check($user['username']==='fixture_manager'&&password_verify('StrongPass123!',$user['password']),'Registered credentials persisted');check(!empty($user['phone_verified_at']),'Phone verification persisted');
check((int)$pdo->query('SELECT COUNT(*) FROM financial_system_accounts WHERE user_id='.$saved['user_id'])->fetchColumn()===1,'Financial account created');check((int)$pdo->query('SELECT COUNT(*) FROM user_referrals WHERE user_id='.$saved['user_id'])->fetchColumn()===1,'Referral created');
denied(fn()=>$branchService->storeMember(10,$staff,false,true));
$staff['phone']='09123330002';$staff['nationalId']='2000000002';$staff['registration']['username']='fixture_rollback';$staff['registration']['otp']='';$otp->clear();$staffRegistration->send(10,$staff);$staff['registration']['otp']=$sms->code;$before=$counts();
$trigger=$mysql?"CREATE TRIGGER fail_staff BEFORE INSERT ON academy_branch_member_contracts FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Fixture failure'":"CREATE TRIGGER fail_staff BEFORE INSERT ON academy_branch_member_contracts BEGIN SELECT RAISE(ABORT,'Fixture failure'); END";$pdo->exec($trigger);
denied(fn()=>$branchService->storeMember(10,$staff,false,true));check($counts()===$before,'Late contract failure rolls back account, wallet, referral and employee');$pdo->exec('DROP TRIGGER fail_staff');
$staff['type']='receptionist';$staff['roleId']=41;$staff['registration']['register_method']='email';$staff['registration']['email']='reception@example.test';$staff['registration']['username']='fixture_reception';$otp->clear();$staffRegistration->send(10,$staff);$staff['registration']['otp']=$mail->code;$saved=$branchService->storeMember(10,$staff,false,true);check($pdo->query('SELECT email FROM users WHERE user_id='.$saved['user_id'])->fetchColumn()==='reception@example.test','Email receptionist registration supported');
$pdo->exec("INSERT INTO users(user_id,type) VALUES(1,'admin')");$registrationActor=1;$staff['type']='manager';$staff['roleId']=40;$staff['phone']='09123330004';$staff['nationalId']='2000000004';$staff['registration']=['username'=>'fixture_owner_created','password'=>'StrongPass123!','password2'=>'StrongPass123!','register_method'=>'phone','terms'=>'1'];$saved=$branchService->storeMember(1,$staff,true,true);check($saved['user_id']>0,'Only real site owner can complete without OTP');
echo "Staff signup: no pre-registration rows, OTP/contract binding, ownership, credentials, wallet/referral, replay and atomic rollback passed.\n";
`;
const source=base.replace('echo "Term batch:',extra+'\necho "Term batch:');
module.exports=source;
if(require.main===module){const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;}
