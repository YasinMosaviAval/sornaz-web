const {spawnSync}=require('node:child_process');
const base=require('./term_student_assignment_test.cjs');
const extra=String.raw`
$pdo->exec('ALTER TABLE academy_branch_course_term_sessions ADD booking_id INTEGER');$pdo->exec('ALTER TABLE user_lessons ADD start_date DATE');$pdo->exec('ALTER TABLE user_addresses ADD deleted_at TEXT');$pdo->exec('ALTER TABLE user_addresses ADD address_id INTEGER');
$tables=[
'levels'=>"level_id $auto,type VARCHAR(20),is_active INTEGER,deleted_at TEXT",
'academy_branch_bookings'=>"booking_id $auto,status VARCHAR(20),requested_date DATE,deleted_at TEXT",
'academy_branch_course_term_waiting_list'=>"term_waiting_list_id $auto,term_id INTEGER,member_id INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,$audit",
'conversations'=>"conversation_id $auto,type VARCHAR(20),title VARCHAR(255),last_message_id INTEGER,created_by INTEGER,updated_by INTEGER,deleted_at TEXT,created_at TEXT,updated_at TEXT",
'conversation_members'=>"conversation_member_id $auto,conversation_id INTEGER,user_id INTEGER,role VARCHAR(20),is_muted INTEGER,joined_at TEXT,left_at TEXT,deleted_at TEXT,last_read_message_id INTEGER,created_by INTEGER,updated_by INTEGER,created_at TEXT,updated_at TEXT",
// Intentionally no body column: this is the production schema after text migration.
'conversation_messages'=>"conversation_message_id $auto,conversation_id INTEGER,sender_id INTEGER,message_kind VARCHAR(20),created_by INTEGER,updated_by INTEGER,created_at TEXT,updated_at TEXT,deleted_at TEXT",
'f_translations'=>"translation_id $auto,table_name VARCHAR(100),table_id INTEGER,field VARCHAR(100),locale VARCHAR(10),value TEXT,version INTEGER,created_at TEXT,updated_at TEXT,created_by INTEGER,updated_by INTEGER,deleted_at TEXT",
'user_messages'=>"user_message_id $auto,sender_id INTEGER,receiver_user_id INTEGER,type VARCHAR(20),audience VARCHAR(20),priority VARCHAR(20),status VARCHAR(20),related_entity_type VARCHAR(100),related_entity_id INTEGER,is_read INTEGER,created_by INTEGER,updated_by INTEGER,created_at TEXT,updated_at TEXT"
];foreach($tables as$table=>$list)$pdo->exec('CREATE TABLE '.$table.'('.$list.')'.($mysql?' ENGINE=InnoDB':''));
$pdo->exec("INSERT INTO levels(level_id,type,is_active) VALUES(7,'learning',1)");$pdo->exec("INSERT INTO user_lessons(user_id,lesson_id,level_id,status) VALUES(10,5,7,'active')");
$pdo->exec("INSERT INTO users(user_id,username,type,register_method,phone) VALUES(800,'applicant','human','phone','09128880001'),(801,'rollback','human','phone','09128880002')");
$enrollments=new Modules\Academy\Services\PublicAcademyEnrollmentService();$termId=$result['ids'][5];
$profile=['name'=>'Applicant','national_id'=>'3000000001','father_name'=>'Father','birth_date'=>'1990-01-01','address'=>'Fixture address'];
$joined=$enrollments->joinWaitingList(100,800,$termId,7,'09128880001','Fixture note','fa',$profile);
check($joined['id']>0&&$joined['conversationId']>0,'Waiting list and conversation created');
$message=$pdo->query('SELECT * FROM conversation_messages WHERE conversation_id='.$joined['conversationId'])->fetch();
check($message && Core\translation\EntityText::get('conversation_messages',(int)$message['conversation_message_id'],'body')!=='','Initial message stored in translations without body column');
check($enrollments->ensureConversation($joined['id'])===$joined['conversationId'],'Chat reused without duplicate message');
denied(fn()=>$enrollments->joinWaitingList(100,800,$termId,7,'09128880001','','fa',$profile));
$memberIds=array_map('intval',$pdo->query('SELECT user_id FROM conversation_members WHERE conversation_id='.$joined['conversationId'])->fetchAll(PDO::FETCH_COLUMN));check(in_array(800,$memberIds,true)&&in_array(10,$memberIds,true)&&!in_array(20,$memberIds,true),'Chat membership stays in selected academy');
$counts=fn()=>array_map(fn($t)=>(int)$pdo->query('SELECT COUNT(*) FROM '.$t)->fetchColumn(),['academy_branch_course_term_waiting_list','conversations','conversation_members','conversation_messages','f_translations','academy_branch_members']);$before=$counts();
$trigger=$mysql?"CREATE TRIGGER fail_waiting_text BEFORE INSERT ON f_translations FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Fixture failure'":"CREATE TRIGGER fail_waiting_text BEFORE INSERT ON f_translations BEGIN SELECT RAISE(ABORT,'Fixture failure'); END";$pdo->exec($trigger);
$profile['national_id']='3000000002';denied(fn()=>$enrollments->joinWaitingList(100,801,$result['ids'][6],7,'09128880002','','fa',$profile));check($counts()===$before,'Translation error rolls back waiting record and entire chat');$pdo->exec('DROP TRIGGER fail_waiting_text');
echo "Waiting list chat: real registration without body column, translated text, reuse, duplicate rejection, tenant members and atomic rollback passed.\n";
`;
const source=base.replace('echo "Term batch:',extra+'\necho "Term batch:');
const result=spawnSync('php',[],{input:source,encoding:'utf8'});process.stdout.write(result.stdout||'');process.stderr.write(result.stderr||'');process.exitCode=result.status??1;
