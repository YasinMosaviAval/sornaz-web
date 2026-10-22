const {spawnSync}=require('node:child_process');
const base=require('./term_batch_policy_test.cjs');
const extra=String.raw`
$audit='created_at TEXT,updated_at TEXT,approved_at TEXT,approved_by INTEGER';
if($mysql)foreach(['users'=>'user_id','academy_branch_members'=>'member_id','user_lessons'=>'user_lesson_id']as$table=>$key)$pdo->exec('ALTER TABLE '.$table.' MODIFY '.$key.' INTEGER NOT NULL AUTO_INCREMENT');
$extraColumns=[
'users'=>"phone VARCHAR(30),national_code VARCHAR(30),username VARCHAR(100),gender VARCHAR(20),status VARCHAR(20),locale VARCHAR(10),timezone VARCHAR(100),visibility VARCHAR(20),birthday DATE,register_time TEXT,register_method VARCHAR(30),created_by INTEGER,updated_by INTEGER,$audit",
'academy_branch_members'=>"parent_user_id INTEGER,joined_at TEXT,created_by INTEGER,updated_by INTEGER,$audit",
'academy_branch_member_roles'=>"is_main INTEGER,created_by INTEGER,updated_by INTEGER,$audit",
'user_lessons'=>"user_id INTEGER,level_id INTEGER,status VARCHAR(20),is_primary INTEGER,created_by INTEGER,updated_by INTEGER,$audit",
'academy_branch_course_term_enrollments'=>$audit,
'translations'=>'created_at TEXT,updated_at TEXT',
'access_system_roles'=>'type VARCHAR(20)'];
foreach($extraColumns as$table=>$columns)foreach(explode(',',$columns)as$column)$pdo->exec('ALTER TABLE '.$table.' ADD COLUMN '.$column);
$pdo->exec("INSERT INTO access_system_roles(role_id,name,type) VALUES(2,'academy_student','academy')");
$newTables=[
'user_roles'=>"user_role_id $auto,user_id INTEGER,role_id INTEGER,is_main INTEGER,created_by INTEGER,updated_by INTEGER,$audit",
'user_contacts'=>"user_contact_id $auto,user_id INTEGER,mode VARCHAR(20),platform VARCHAR(20),priority VARCHAR(20),is_main INTEGER,status VARCHAR(20),created_by INTEGER,updated_by INTEGER,$audit",
'world_iran_provinces'=>'province_id INTEGER PRIMARY KEY,province_name VARCHAR(100)',
'world_iran_counties'=>'county_id INTEGER PRIMARY KEY,county_name VARCHAR(100)',
'user_addresses'=>"address_id $auto,user_id INTEGER,country_id INTEGER,province_id INTEGER,county_id INTEGER,is_main INTEGER,latitude VARCHAR(30),longitude VARCHAR(30),postal_code VARCHAR(30),created_by INTEGER,updated_by INTEGER,$audit",
'academy_branch_course_term_invoices'=>"term_invoice_id $auto,term_id INTEGER,member_id INTEGER,discount_id INTEGER,payable_amount DECIMAL(14,2),currency_id INTEGER,status VARCHAR(20),due_date DATE,issued_at TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,$audit",
'academy_branch_course_term_invoice_installments'=>"term_invoice_installment_id $auto,invoice_id INTEGER,installment_number INTEGER,amount DECIMAL(14,2),due_date DATE,status VARCHAR(20),created_by INTEGER,updated_by INTEGER,deleted_at TEXT,$audit"
];
foreach($newTables as$table=>$columns)$pdo->exec('CREATE TABLE '.$table.'('.$columns.')'.($mysql?' ENGINE=InnoDB':''));
$student=['name'=>'Fixture student','phone'=>'09120000001','nationalId'=>'1000000001','birthDate'=>'1990-01-01','fatherName'=>'Father','addresses'=>[['province'=>'Fixture','city'=>'Fixture','address'=>'Fixture address']],'organizationUserId'=>20,'termId'=>999];
$users=fn()=>(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();$before=$users();
denied(fn()=>$service->assignStudent(20,$result['ids'][1],$student));check($users()===$before,'Foreign actor cannot create user');
$assigned=$service->assignStudent(10,$result['ids'][1],$student);
$enrollment=$pdo->query('SELECT * FROM academy_branch_course_term_enrollments WHERE term_id='.$result['ids'][1]." AND type='student'")->fetch();
$member=$pdo->query('SELECT * FROM academy_branch_members WHERE member_id='.$assigned['member_id'])->fetch();
check($enrollment && $enrollment['status']==='active' && (int)$member['academy_id']===100,'Selected term and organization override forged payload');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoices WHERE member_id='.$assigned['member_id'])->fetchColumn()===1,'One student invoice created');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_branch_course_term_invoice_installments')->fetchColumn()===1,'One installment created');
$student['phone']='09120000002';$student['nationalId']='1000000002';$before=$users();
denied(fn()=>$service->assignStudent(10,$result['ids'][1],$student));check($users()===$before,'Occupied term creates no additional user');
$ongoing=$result['ids'][2];$pdo->exec("UPDATE academy_branch_course_terms SET status='ongoing' WHERE term_id=".$ongoing);denied(fn()=>$service->assignStudent(10,$ongoing,$student));
$assigned=$service->assignStudent(30,$pending['ids'][1],$student);check($assigned['status']==='pending','Receptionist student remains pending');
$student['phone']='09120000003';$student['nationalId']='1000000003';$student['birthDate']='2015-01-01';$before=$users();denied(fn()=>$service->assignStudent(10,$result['ids'][3],$student));check($users()===$before,'Minor without parent rolls back registration');
$student['birthDate']='1990-01-01';$finished=$result['ids'][4];$pdo->exec("UPDATE academy_branch_course_terms SET status='finished' WHERE term_id=".$finished);denied(fn()=>$service->assignStudent(10,$finished,$student));check($pdo->query('SELECT status FROM academy_branch_course_terms WHERE term_id='.$finished)->fetchColumn()==='finished','Finished term rejects new enrollment and preserves its status');
echo "Term assignment: ownership, forced context, invoice, enrollment, occupied/ongoing rejection, receptionist and minor validation passed.\n";
`;
const source=base.replace('echo "Term batch:',extra+'\necho "Term batch:');
module.exports=source;
if(require.main===module){const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;}
